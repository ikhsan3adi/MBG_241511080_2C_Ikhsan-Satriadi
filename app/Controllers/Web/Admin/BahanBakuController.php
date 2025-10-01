<?php

namespace App\Controllers\Web\Admin;

use App\Controllers\BaseController;

class BahanBakuController extends BaseController
{
    public function index()
    {
        return view('admin/bahanbaku/index', ['title' => 'Manajemen Bahan Baku']);
    }
}
