<?php

use App\Presentation\Repositories\VehiculoRepository;

return function ($app) {
    $vehiculoRepo = new VehiculoRepository();

    $app->get('/vehiculos', [$vehiculoRepo, 'listar']);
    $app->get('/vehiculos/{id}', [$vehiculoRepo, 'obtener']);
    $app->get('/vehiculos/placa/{placa}', [$vehiculoRepo, 'buscarPlaca']);
    $app->get('/vehiculos/estado/{estado}', [$vehiculoRepo, 'buscarEstado']);
    $app->get('/vehiculos/tipo/{tipo}', [$vehiculoRepo, 'buscarTipo']);
    $app->post('/vehiculos', [$vehiculoRepo, 'crear']);
    $app->put('/vehiculos/{id}', [$vehiculoRepo, 'actualizar']);
    $app->delete('/vehiculos/{id}', [$vehiculoRepo, 'eliminar']);
};