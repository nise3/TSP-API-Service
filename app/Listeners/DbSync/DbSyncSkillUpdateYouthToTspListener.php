<?php

namespace App\Listeners\DbSync;

use App\Models\BaseModel;
use App\Models\Skill;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;


class DbSyncSkillUpdateYouthToTspListener implements ShouldQueue
{

    /**
     * @param $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        $eventData = json_decode(json_encode($event), true);
        $data = isset($eventData['data']) && is_array($eventData['data']) ? $eventData['data'] : [];

        try {

            throw_if(!(isset($data['operation']) && in_array($data['operation'], ['add', 'update', 'delete']) && !empty($data['skill_data'])));

            $id = trim($data['skill_data']['id']);

            if ($data['operation'] === 'add') {
                $skill = Skill::find($id);
                if (!($skill && $skill->id)) {
                    Skill::create($data['skill_data']);
                }
            } else if ($data['operation'] === 'update') {
                $skill = Skill::findOrFail($id);
                $skill->title_en = trim($data['skill_data']['title_en']);
                $skill->title = trim($data['skill_data']['title']);
                $skill->save();
            } else if ($data['operation'] === 'delete') {
                $skill = Skill::findOrFail($id);
                $skill->delete();
            }

        } catch (Throwable $e) {
            if ($e instanceof QueryException && $e->getCode() == BaseModel::DATABASE_CONNECTION_ERROR_CODE) {
                /** Technical Recoverable Error Occurred. RETRY mechanism with DLX-DLQ apply now by sending a rejection */
                throw new PDOException("Database Connectivity Error");
            }
        }
    }
}
