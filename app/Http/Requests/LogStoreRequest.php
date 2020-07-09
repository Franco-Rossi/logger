<?php

namespace App\Http\Requests;

use App\Repository\RepositoryLog;
use Config;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log as FileLog;
use zuma\tools\responseBuilder\ResponseType;
use zuma\tools\validations\ValidationDataGenericClass;

class LogStoreRequest extends FormRequest
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
            'title' => 'required|max:50',
            'from' => 'required|max:25',
            'extra' => '',
            'config.level' => 'required|string',
            'config.db' => 'integer',
            'config.file' => 'integer',
        ];
    }

    public function save()
    {
        $data = $this->input();
        $data['url'] = $this->url();
        $data['ip'] = $this->ip();

        if ($data["config"]["db"]) {
            $newLog = $this->saveDB($data);
        }

        if ($data["config"]["file"]) {
            $this->saveFile($data);
        }

        return $newLog;
    }

    public function saveFile($data)
    {
        // Replaces normal space with $nbsp;
        // First parameter is normal space, second one is %nbsp; in ascii (alt+255)
        $data = str_replace(" ", " ", $data);
        $data["extra"] = str_replace(" ", " ", $data["extra"]);
        $level = $data["config"]["level"];

        $log = FileLog::channel("monitor")->$level($data["title"], $data);
        return $log;
    }

    public function saveDB($data)
    {
        $atributes = [
            "title" => $data['title'],
            "from" => $data['from'],
            "url" => $data['url'],
            "extra" => json_encode($data['extra']),
            "ip" => $data['ip'],
            "level" => $data['config']['level']
        ];
        return $this->log->create($atributes);
    }
}
