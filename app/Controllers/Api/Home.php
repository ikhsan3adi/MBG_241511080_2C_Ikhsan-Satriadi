<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseApiController
{
    public function index(): ResponseInterface
    {
        $appName = env('app.name');

        return $this->respond([
            "error" => false,
            "message" => "Selamat datang di API {$appName}!"
        ]);
    }
}
