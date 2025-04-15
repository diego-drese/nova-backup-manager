<?php

namespace DiegoDrese\NovaBackupManager\Http\Controllers;

use Illuminate\Http\Response;

abstract class ApiController
{
    public function respondSuccess(): Response {
        return response('', Response::HTTP_NO_CONTENT);
    }

}
