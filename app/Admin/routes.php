<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('kelompok', KelompokController::class);
    $router->resource('instansi', InstansiController::class);
    $router->resource('layanan', LayananController::class);
    $router->resource('sampel', SampelController::class);
    $router->resource('jk', JkController::class);
    $router->resource('jam', JamController::class);
    $router->resource('pendidikan', PendidikanController::class);
    $router->resource('pekerjaan', PekerjaanController::class);
    $router->resource('periode', PeriodeController::class);
    $router->resource('token', TokenController::class);
});
