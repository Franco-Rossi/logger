<?php

namespace App\Http\Controllers;

use App\Http\Requests\{LogShowRequest, LogStoreRequest};
use zuma\tools\responseBuilder\HTTPJSONResponse;

class LogController extends Controller
{
    /**
     *
     *  Creates a new log register with the information sent by the API
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LogStoreRequest $request)
    {
        return HTTPJSONResponse::DoResponse($request->save());
    }

    /**
     *
     *  Show all the DB logs from a certain date
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(LogShowRequest $request)
    {
        return HTTPJSONResponse::DoResponse($request->show());
    }
}
