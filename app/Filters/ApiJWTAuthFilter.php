<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\IncomingRequest;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;
use Config\Auth;

class ApiJWTAuthFilter implements FilterInterface
{
    protected $response;

    public function __construct()
    {
        $this->response = \Config\Services::response();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        assert($request instanceof IncomingRequest);

        $token = $request->header('Authorization')?->getValue() ?? '';

        try {
            $token = preg_replace('/^Bearer\s/', '', $token);

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
            return $this->response->setStatusCode(401)->setJSON([
                'error'   => true,
                'message' => 'Token tidak valid.',
            ])->send();
        }

        $model = new UserModel();
        $user = $model->where('email', $email)->asArray()->first();

        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'error'   => true,
                'message' => 'Anda harus login terlebih dahulu.',
            ])->send();
        }

        setCurrentUser($user);
    }

    public function after(RequestInterface $request, $response, $arguments = null) {}
}
