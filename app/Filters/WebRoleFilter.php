<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class WebRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        assert($request instanceof IncomingRequest);

        $user = currentUser();

        if (! $user) {
            return redirect()
                ->to('/login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }

        foreach ($arguments as $role) {
            if ($user && $user['role'] === $role) {
                return;
            }
        }

        return redirect()
            ->back()
            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
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
