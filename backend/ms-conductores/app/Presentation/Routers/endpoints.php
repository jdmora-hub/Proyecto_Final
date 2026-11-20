<?php

use App\Presentation\Repositories\ConductorRepository;

return function ($app) {
    $conductorRepo = new ConductorRepository();

    $app->get('/conductores', [$conductorRepo, 'listar']);
    $app->get('/conductores/{id}', [$conductorRepo, 'obtener']);
    $app->get('/conductores/documento/{documento}', [$conductorRepo, 'buscarDocumento']);
    $app->get('/conductores/licencia/{licencia}', [$conductorRepo, 'buscarLicencia']);
    $app->get('/conductores/estado/{estado}', [$conductorRepo, 'buscarEstado']);
    $app->post('/conductores', [$conductorRepo, 'crear']);
    $app->put('/conductores/{id}', [$conductorRepo, 'actualizar']);
    $app->delete('/conductores/{id}', [$conductorRepo, 'eliminar']);
};