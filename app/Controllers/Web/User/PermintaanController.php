<?php

namespace App\Controllers\Web\User;

use App\Controllers\BaseController;

class PermintaanController extends BaseController
{
    public function index()
    {
        return view('user/permintaan/index', ['title' => 'Permintaan Bahan Baku']);
    }
}
