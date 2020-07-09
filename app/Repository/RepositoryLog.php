<?php

namespace App\Repository;

use App\Models\Log;
use zuma\tools\repository\ModelRepository;

class RepositoryLog extends ModelRepository
{
    public function create($request)
    {
        try {
            $log = Log::create($request);
            $this->responseType->status = true;
            $this->responseType->message = trans('zuma.registered');
            config('constants.CONFIG.HTTP_CODE_OK');
            $this->responseType->code = config('constants.CONFIG.HTTP_CREATED');
            $this->responseType->data = $log;

            return $this->responseType;
        } catch (\Exception $e) {
            $this->responseType->status = false;
            $this->responseType->message = "Errors: " . $e->getMessage();
            $this->responseType->code = config('constants.CONFIG.HTTP_CODE_INTERNAL_SERVER_ERROR');

            return $this->responseType;
        }
    }

    public function show($date)
    {
        try {
            $result = Log::where("created_at", "LIKE", "$date%")->get();
            $this->responseType->status = true;
            $this->responseType->code = config('constants.CONFIG.HTTP_CODE_OK');
            $this->responseType->data = $result;
            return $this->responseType;
        } catch (\Exception $e) {
            $this->responseType->status = false;
            $this->responseType->message = "Errors: " . $e->getMessage();
            $this->responseType->code = config('constants.CONFIG.HTTP_CODE_INTERNAL_SERVER_ERROR');

            return $this->responseType;
        }
    }
}
