<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class Home extends BaseController
{
    // Redirect user based on their role after login
    public function index()
    {
        if (!isAuthenticated()) {
            return redirect()->to('/login', 401);
        }

        $user = currentUser();

        if ($user['role'] === 'gudang') {
            return redirect()->to('/admin');
        }

        if ($user['role'] === 'dapur') {
            return redirect()->to('/user');
        }

        return redirect()->back(403);
    }
}
