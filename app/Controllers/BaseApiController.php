<?php

namespace App\Controllers;

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\RESTful\ResourceController;

abstract class BaseApiController extends ResourceController
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected function getCreateValidationRules(...$args): array
    {
        return [];
    }

    protected function getUpdateValidationRules(...$args): array
    {
        return [];
    }

    public function __construct()
    {
        $this->validator = \Config\Services::validation();
    }
}
