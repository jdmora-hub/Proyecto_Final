<?php

use App\Presentation\Repositories\RutaRepository;
use App\Presentation\Repositories\ProgramacionViajeRepository;

return function ($app) {
    $rutaRepo = new RutaRepository();
    $viajeRepo = new ProgramacionViajeRepository();

    $app->get('/rutas', [$rutaRepo, 'listar']);
    $app->get('/rutas/{id}', [$rutaRepo, 'obtener']);
    $app->get('/rutas/origen/{origen}', [$rutaRepo, 'buscarOrigen']);
    $app->get('/rutas/destino/{destino}', [$rutaRepo, 'buscarDestino']);
    $app->post('/rutas', [$rutaRepo, 'crear']);
    $app->put('/rutas/{id}', [$rutaRepo, 'actualizar']);
    $app->delete('/rutas/{id}', [$rutaRepo, 'eliminar']);

    $app->get('/programaciones-viajes', [$viajeRepo, 'listar']);
    $app->get('/programaciones-viajes/{id}', [$viajeRepo, 'obtener']);
    $app->get('/programaciones-viajes/estado/{estado}', [$viajeRepo, 'buscarEstado']);
    $app->get('/programaciones-viajes/conductor/{conductor_id}', [$viajeRepo, 'buscarConductor']);
    $app->get('/programaciones-viajes/vehiculo/{vehiculo_id}', [$viajeRepo, 'buscarVehiculo']);
    $app->post('/programaciones-viajes', [$viajeRepo, 'crear']);
    $app->put('/programaciones-viajes/{id}', [$viajeRepo, 'actualizar']);
    $app->delete('/programaciones-viajes/{id}', [$viajeRepo, 'eliminar']);
};