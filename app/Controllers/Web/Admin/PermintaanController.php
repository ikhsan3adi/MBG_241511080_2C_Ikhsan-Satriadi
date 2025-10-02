<?php

namespace App\Controllers\Web\Admin;

use App\Controllers\BaseController;

class PermintaanController extends BaseController
{
    public function index()
    {
        return view('admin/permintaan/index', ['title' => 'Manajemen Permintaan Bahan Baku']);
    }
}
