<?php

namespace App\Presentation\Repositories;

use App\Controllers\ConductorController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConductorRepository
{
    private $controller;

    public function __construct()
    {
        $this->controller = new ConductorController();
    }

    public function listar(Request $request, Response $response)
    {
        $conductores = $this->controller->listarTodos();
        $response->getBody()->write(json_encode($conductores));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function obtener(Request $request, Response $response, $args)
    {
        $conductor = $this->controller->obtenerPorId($args['id']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['error' => 'Conductor no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($conductor));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarDocumento(Request $request, Response $response, $args)
    {
        $conductor = $this->controller->buscarPorDocumento($args['documento']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['error' => 'Conductor no encontrado con ese documento']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($conductor));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarLicencia(Request $request, Response $response, $args)
    {
        $conductor = $this->controller->buscarPorLicencia($args['licencia']);
        if (!$conductor) {
            $response->getBody()->write(json_encode(['error' => 'Conductor no encontrado con esa licencia']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($conductor));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarEstado(Request $request, Response $response, $args)
    {
        $conductores = $this->controller->buscarPorEstado($args['estado']);
        $response->getBody()->write(json_encode($conductores));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function crear(Request $request, Response $response)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (
            empty($datos['nombres']) || empty($datos['apellidos']) ||
            empty($datos['documento']) || empty($datos['telefono']) ||
            empty($datos['correo']) || empty($datos['numero_licencia']) ||
            empty($datos['categoria_licencia']) || empty($datos['fecha_vencimiento_licencia'])
        ) {
            $response->getBody()->write(json_encode(['error' => 'Todos los campos obligatorios deben ser enviados']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Validar que la licencia no esté vencida
        if ($datos['fecha_vencimiento_licencia'] < date('Y-m-d')) {
            $response->getBody()->write(json_encode(['error' => 'La licencia de conducción se encuentra vencida']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $conductor = $this->controller->crear($datos);
            $response->getBody()->write(json_encode([
                'mensaje' => 'Conductor registrado exitosamente',
                'conductor' => $conductor
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al registrar conductor (verifique documento, correo o licencia duplicados)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function actualizar(Request $request, Response $response, $args)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (!empty($datos['fecha_vencimiento_licencia']) && $datos['fecha_vencimiento_licencia'] < date('Y-m-d')) {
            $response->getBody()->write(json_encode(['error' => 'La fecha de vencimiento de la licencia no puede estar vencida']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $conductor = $this->controller->actualizar($args['id'], $datos);
            if (!$conductor) {
                $response->getBody()->write(json_encode(['error' => 'Conductor no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'mensaje' => 'Conductor actualizado exitosamente',
                'conductor' => $conductor
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al actualizar el conductor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function eliminar(Request $request, Response $response, $args)
    {
        $eliminado = $this->controller->eliminar($args['id']);
        if (!$eliminado) {
            $response->getBody()->write(json_encode(['error' => 'Conductor no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode(['mensaje' => 'Conductor eliminado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}