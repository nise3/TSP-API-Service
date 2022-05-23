<?php

namespace App\Helpers\Classes;

use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ServiceToServiceCallHandler
{

    /**
     * @param string $idpUserId
     * @return mixed
     * @throws RequestException
     */
    public function getAuthUserWithRolePermission(string $idpUserId): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'auth-user-info';
        $userPostField = [
            "idp_user_id" => $idpUserId
        ];

        $responseData = Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug')
            ])
            ->timeout(120)
            ->post($url, $userPostField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');


        Log::info("userInfo:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * @param int $industryAssociationId
     * @return mixed
     * @throws RequestException
     */
    public function getIndustryAssociationCode(int $industryAssociationId): mixed
    {
        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/industry-associations/' . $industryAssociationId . '/get-code';

        $responseData = Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug')
            ])
            ->timeout(120)
            ->get($url)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');


        Log::info("industry_association_id:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * @param int $industryAssociationId
     * @return mixed
     * @throws RequestException
     */
    public function getIndustryAssociationData(int $industryAssociationId): mixed
    {
        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/industry-associations/' . $industryAssociationId;

        $responseData = Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug')
            ])
            ->timeout(120)
            ->get($url)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');


        Log::info("industry_association_id:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * @param array $youthIds
     * @return mixed
     * @throws RequestException
     */
    public function getYouthProfilesByIds(array $youthIds): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/youth-profiles';
        $postField = [
            "youth_ids" => $youthIds
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(120)
            ->post($url, $postField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("Youth Data:" . json_encode($youthData));

        return $youthData;
    }

    /**
     * @param array $trainer
     * @return mixed
     * @throws RequestException
     */
    public function createTrainerYouthUser(array $trainer): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/create-trainer-youth';
        $trainerInfo = [
            'user_name_type' => BaseModel::USER_NAME_TYPE_MOBILE_NUMBER,
            'first_name' => $trainer['trainer_name'] ?? "",
            'first_name_en' => $trainer['trainer_name_en'] ?? "",
            'last_name' => $trainer['trainer_name'] ?? "",
            'last_name_en' => $trainer['trainer_name_en'] ?? "",
            'loc_division_id' => $trainer['present_address_division_id'] ?? "",
            'loc_district_id' => $trainer['present_address_district_id'] ?? "",
            'loc_upazila_id' => $trainer['present_address_upazila_id'] ?? "",
            'date_of_birth' => $trainer['date_of_birth'],
            'gender' => $trainer['gender'],
            'email' => $trainer['email'] ?? "",
            'mobile' => $trainer['mobile'],
            'physical_disability_status' => BaseModel::FALSE,
            'skills' => $trainer['skills'],
            'password' => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            'password_confirmation' => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            'village_or_area' => $trainer['present_house_address'] ?? "",
            'village_or_area_en' => $trainer['present_house_address_en'] ?? "",
            'house_n_road' => $trainer['present_house_address'] ?? "",
            'house_n_road_en' => $trainer['present_house_address_en'] ?? ""
        ];

        $postField = [
            "trainer_info" => $trainerInfo
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(120)
            ->post($url, $postField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("Youth Data:" . json_encode($youthData));

        return $youthData;
    }

    public function updateTrainerYouthUser(array $trainer): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/update-trainer-youth';

        $postField = [
            "trainer_info" => $trainer
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(5)
            ->post($url, $postField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("Youth Data:" . json_encode($youthData));

        return $youthData;
    }

    /**
     * @param array $trainer
     * @param array $youth
     * @return mixed
     * @throws RequestException
     */
    public function createTrainerCoreUser(array $trainer, array $youth): mixed
    {
        $authUser = Auth::user();
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'service-to-service-call/create-trainer-user';
        $trainerInfo = [
            'user_type' => BaseModel::YOUTH_USER_TYPE,
            'username' => $trainer['mobile'],
            'trainer_id' => $trainer['id'],
            'role_id' => $trainer['role_id'],
            'name_en' => $trainer['trainer_name_en'] ?? "",
            'name' => $trainer['trainer_name'] ?? "",
            'email' => $trainer['email'] ?? "",
            'mobile' => $trainer['mobile'],
            'loc_division_id' => $trainer['present_address_division_id'] ?? "",
            'loc_district_id' => $trainer['present_address_district_id'] ?? "",
            'loc_upazila_id' => $trainer['present_address_upazila_id'] ?? "",
            'password' => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            'password_confirmation' => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            'row_status' => BaseModel::ROW_STATUS_ACTIVE
        ];

        /** Set the id who is creating this TRAINER */
        if ($authUser['user_type'] == BaseModel::INSTITUTE_USER_TYPE) {
            $trainerInfo['institute_id'] = $authUser['id'];
        } else if ($authUser['user_type'] == BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE) {
            $trainerInfo['industry_association_id'] = $authUser['id'];
        }

        $postField = [
            "trainer_info" => $trainerInfo,
            "youth_info" => $youth
        ];

        $user = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(120)
            ->post($url, $postField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("user Data:" . json_encode($user));

        return $user;
    }

    /**
     * @param array $youth
     * @return mixed
     * @throws RequestException
     */
    public function rollbackTrainerYouthUser(array $youth): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/rollback-trainer-youth-user';

        $postField = [
            "youth_info" => $youth
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(120)
            ->post($url, $postField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json('data');

        Log::info("Youth Data:" . json_encode($youthData));

        return $youthData;
    }

    public function rollbackYouthUserById(string $mobile): mixed
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/rollback-youth-user-by-id';

        $postField = [
            "username" => $mobile
        ];

        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(120)
            ->post($url, $postField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            });

        Log::info("Youth Data:" . json_encode($youthData->json('data')));
        return $youthData->json("data");
    }

    public function updateOrCreateYouthUser(array $payload): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'service-to-service-call/youth-create-or-update-for-course-enrollment';
        $youthData = Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->post($url, $payload)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                CustomExceptionHandler::customHttpResponseMessage($httpResponse->body());
            });

        Log::info("Youth Data:" . json_encode($youthData->json('data')));
        return $youthData;
    }
}
