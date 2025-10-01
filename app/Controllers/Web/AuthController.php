<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    public function loginView()
    {
        try {
            $token = $this->request->getCookie('jwt_token') ?? '';

            $decoded = JWT::decode($token, new Key(
                jwtSecretKey(),
                'HS256'
            ));

            $email = $decoded->data->email ?? null;

            if (!$email) {
                throw new \Exception('Email not found in token');
            }

            $model = new UserModel();
            $user = $model->where('email', $email)->asArray()->first();

            if ($user !== null) {
                return redirect()->to('/');
            }
        } catch (\Exception $e) {
        }

        return view('login');
    }

    public function logoutAction()
    {
        // Delete the JWT cookie
        $this->response->deleteCookie('jwt_token');

        return redirect()->to('/login')->withCookies();
    }
}
