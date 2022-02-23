<?php

return env('LOG_CHANNEL') === 'elasticsearch' ? config('elasticSearchLogConfig') : config('lumenDefaultLogConfig');
