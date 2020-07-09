<?php

namespace App\Http\Requests;

use App\Repository\RepositoryLog;
use Config;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log as FileLog;
use zuma\tools\responseBuilder\ResponseType;
use zuma\tools\validations\ValidationDataGenericClass;

class LogShowRequest extends FormRequest
{
    use ValidationDataGenericClass;

    public function __construct(RepositoryLog $log, ResponseType $responseType)
    {
        $this->log = $log;
        $this->responseType = $responseType;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'string',
        ];
    }

    public function show()
    {
        $responseFile = [];
        $responseDb = [];

        try {
            $data = $this->input();
            $date = $data['date'];

            $db_log = $this->log->show($date)->data;
            $file_log = file(storage_path() . '/logs/events-' . $date . '.log');
            //$file_log = file(storage_path() . '\logs\events-' . $date . '.log');
        } catch (\Exception $e) {
            // return
            $this->log->responseType->message = $e->getMessage();
            $this->log->responseType->status = false;
            $this->log->responseType->code = Config::get('constants.CONFIG.HTTP_CODE_BAD_REQUEST');

            return $this->log->responseType;
        }

        foreach ($file_log as $file_line) {
            $explodedLog = explode(" ", $file_line);

            // Normalizes the 'level' atribute to be the same as in the DB
            $level = strtolower(preg_replace(['/local./', '/:/'], "", $explodedLog[2]));

            $responseFile[$level][] = [
                "date" => $explodedLog[0] . $explodedLog[1],
                "title" => $explodedLog[3],
                "data" => json_decode($explodedLog[4])
            ];
        }

        foreach ($db_log as $db_line) {
            $db_line->extra = json_decode($db_line->extra);
            $responseDb[$db_line->level][] = $db_line;
        }
        $result = ["db" => $responseDb, "file" => $responseFile];

        $this->log->responseType->status = true;
        $this->log->responseType->code = Config::get('constants.CONFIG.HTTP_CODE_OK');
        $this->log->responseType->data = $result;
        return $this->log->responseType;
    }
}
