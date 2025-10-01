<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class AuthController extends BaseApiController
{
    public function loginAction(): ResponseInterface
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]|max_length[255]',
        ];

        //* Validate input
        if (! $this->validateData($this->request->getJSON(true), $rules)) {
            return $this->respond(
                status: 400,
                data: [
                    'error' => true,
                    'messages' => $this->validator->getErrors(),
                ],
            );
        }

        $email = $this->request->getJSON(true)['email'];
        $password = $this->request->getJSON(true)['password'];

        $model = new UserModel();

        $user = $model->where('email', $email)->asArray()->first();

        //! User tidak ditemukan (email salah)
        if (!$user) {
            return $this->respond(
                status: 404,
                data: [
                    'error' => true,
                    'message' => 'User tidak ditemukan.',
                ]
            );
        }

        //! Salah password
        if (! md5($password) === $user['password']) {
            return $this->respond(
                status: 401,
                data: [
                    'error' => true,
                    'message' => 'Password salah.',
                ]
            );
        }

        // Generate JWT
        $jwt = JWT::encode(
            payload: [
                'iss' => base_url(),
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + HOUR,
                'data' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                ],
            ],
            key: jwtSecretKey(),
            alg: 'HS256'
        );

        // Taruh JWT di cookie
        $this->response->setCookie(
            name: 'jwt_token',
            value: $jwt,
            expire: HOUR,
            httponly: true,
            samesite: 'strict'
        );

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Login berhasil.',
                'jwt_token' => $jwt,
                'redirect_url' => url_to('/'),
            ]
        );
    }

    public function logoutAction(): ResponseInterface
    {
        // Delete the JWT cookie
        $this->response->deleteCookie('jwt_token');

        return $this->respond(
            status: 200,
            data: [
                'error' => false,
                'message' => 'Logout berhasil.',
                'redirect_url' => url_to('login'),
            ]
        );
    }
}
