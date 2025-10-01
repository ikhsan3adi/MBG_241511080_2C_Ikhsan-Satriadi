<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\IncomingRequest;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;
use Config\Auth;

class WebJWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        assert($request instanceof IncomingRequest);

        $token = $request->getCookie('jwt_token') ?? '';

        try {
            $decoded = JWT::decode($token, new Key(
                jwtSecretKey(),
                'HS256'
            ));

            $email = $decoded->data->email ?? null;

            if (!$email) {
                throw new \Exception('Email not found in token');
            }
        } catch (\Exception $e) {
            log_message('error', 'JWT Decode Error: ' . $e->getMessage());
            return redirect()
                ->to('/login')
                ->with('error', 'Token tidak valid.');
        }

        $model = new UserModel();
        $user = $model->where('email', $email)->asArray()->first();

        if (!$user) {
            return redirect()
                ->to('/login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }

        setCurrentUser($user);
    }

    public function after(RequestInterface $request, $response, $arguments = null) {}
}
