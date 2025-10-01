<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

//?================= Web Routes =================//

$routes->get('login', 'AuthController::loginView', ['namespace' => 'App\Controllers\Web']);
$routes->get('logout', 'AuthController::logoutAction', ['namespace' => 'App\Controllers\Web']);

$routes->group(
    '',
    ['filter' => ['webjwt'], 'namespace' => 'App\Controllers\Web'],
    static function (RouteCollection $routes) {
        $routes->get('/', 'Home::index');

        //? Role Gudang / Admin
        $routes->group(
            'admin',
            ['filter' => ['webrole:gudang']],
            static function (RouteCollection $routes) {
                $routes->get('/', 'Admin\BahanBakuController::index');
                $routes->get('bahanbaku', 'Admin\BahanBakuController::index');
            }
        );

        //? Role Dapur / User
        $routes->group(
            'user',
            ['filter' => ['webrole:dapur']],
            static function (RouteCollection $routes) {}
        );
    }
);

//?================= API Routes =================//

$routes->get('api', 'Home::index', ['namespace' => 'App\Controllers\Api']);

$routes->post('api/login', 'AuthController::loginAction', ['namespace' => 'App\Controllers\Api']);

$routes->group(
    'api',
    ['filter' => ['apijwt'], 'namespace' => 'App\Controllers\Api'],
    static function (RouteCollection $routes) {
        $routes->delete('logout', 'AuthController::logoutAction');

        //? Role Gudang / Admin
        $routes->group(
            'admin',
            ['filter' => ['apirole:gudang'], 'namespace' => 'App\Controllers\Api\Admin'],
            static function (RouteCollection $routes) {

                //? Resource Bahan Baku
                $routes->group('bahanbaku', static function (RouteCollection $routes) {
                    $routes->get('/', 'BahanBakuController::index'); // Mendapatkan bahan baku
                    $routes->get('(:segment)', 'BahanBakuController::show/$1'); // Mendapatkan detail bahan baku
                    $routes->post('/', 'BahanBakuController::create'); // Memproses tambah bahan baku
                    $routes->put('(:segment)', 'BahanBakuController::update/$1'); // Memproses edit bahan baku
                    $routes->delete('/', 'BahanBakuController::delete'); // Menghapus data bahan baku (individu/massal)
                });
            }
        );

        //? Role Dapur / User
        $routes->group(
            'user',
            ['filter' => ['apirole:dapur']],
            static function (RouteCollection $routes) {}
        );
    }
);

//! Override untuk 404 not found di API
//! Supaya returnnya json, bukan html
$routes->get(
    'api/(:any)',
    fn($p) => response()->setStatusCode(404)->setJSON([
        'error' => true,
        'message' => 'Endpoint tidak ditemukan!',
        'endpoint' => base_url('api/' . $p),
    ])
);
