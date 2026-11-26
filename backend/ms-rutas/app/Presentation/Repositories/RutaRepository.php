<?php

namespace App\Presentation\Repositories;

use App\Controllers\RutaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RutaRepository
{
    private $controller;

    public function __construct()
    {
        $this->controller = new RutaController();
    }

    public function listar(Request $request, Response $response)
    {
        $rutas = $this->controller->listarTodas();
        $response->getBody()->write(json_encode($rutas));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function obtener(Request $request, Response $response, $args)
    {
        $ruta = $this->controller->obtenerPorId($args['id']);
        if (!$ruta) {
            $response->getBody()->write(json_encode(['error' => 'Ruta no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($ruta));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarOrigen(Request $request, Response $response, $args)
    {
        $rutas = $this->controller->buscarPorOrigen($args['origen']);
        $response->getBody()->write(json_encode($rutas));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarDestino(Request $request, Response $response, $args)
    {
        $rutas = $this->controller->buscarPorDestino($args['destino']);
        $response->getBody()->write(json_encode($rutas));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function crear(Request $request, Response $response)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (
            empty($datos['ciudad_origen']) || empty($datos['ciudad_destino']) ||
            !isset($datos['distancia']) || empty($datos['tiempo_estimado'])
        ) {
            $response->getBody()->write(json_encode(['error' => 'Todos los campos obligatorios deben ser enviados']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!is_numeric($datos['distancia']) || $datos['distancia'] <= 0) {
            $response->getBody()->write(json_encode(['error' => 'La distancia debe ser mayor a cero']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $ruta = $this->controller->crear($datos);
            $response->getBody()->write(json_encode([
                'mensaje' => 'Ruta registrada exitosamente',
                'ruta' => $ruta
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al registrar la ruta']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function actualizar(Request $request, Response $response, $args)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (isset($datos['distancia']) && (!is_numeric($datos['distancia']) || $datos['distancia'] <= 0)) {
            $response->getBody()->write(json_encode(['error' => 'La distancia debe ser mayor a cero']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $ruta = $this->controller->actualizar($args['id'], $datos);
            if (!$ruta) {
                $response->getBody()->write(json_encode(['error' => 'Ruta no encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'mensaje' => 'Ruta actualizada exitosamente',
                'ruta' => $ruta
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al actualizar la ruta']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function eliminar(Request $request, Response $response, $args)
    {
        $eliminado = $this->controller->eliminar($args['id']);
        if (!$eliminado) {
            $response->getBody()->write(json_encode(['error' => 'Ruta no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode(['mensaje' => 'Ruta eliminada exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}