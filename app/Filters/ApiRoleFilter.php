<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiRoleFilter implements FilterInterface
{
    protected $response;

    public function __construct()
    {
        $this->response = \Config\Services::response();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        assert($request instanceof IncomingRequest);

        $user = currentUser();

        if (! $user) {
            return $this->response->setStatusCode(401)->setJSON([
                'error'   => true,
                'message' => 'Anda harus login terlebih dahulu.',
            ])->send();
        }

        foreach ($arguments as $role) {
            if ($user && $user['role'] === $role) {
                return;
            }
        }

        return $this->response->setStatusCode(403)->setJSON([
            'error'   => true,
            'message' => 'Anda tidak memiliki akses ke halaman tersebut.',
        ])->send();
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
