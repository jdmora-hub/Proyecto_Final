<?php

namespace App\Presentation\Repositories;

use App\Controllers\ProgramacionViajeController;
use App\Controllers\RutaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProgramacionViajeRepository
{
    private $controller;
    private $rutaController;
    private $estadosPermitidos = ['programado', 'en_transito', 'retrasado', 'finalizado', 'cancelado'];

    public function __construct()
    {
        $this->controller = new ProgramacionViajeController();
        $this->rutaController = new RutaController();
    }

    public function listar(Request $request, Response $response)
    {
        $viajes = $this->controller->listarTodos();
        $response->getBody()->write(json_encode($viajes));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function obtener(Request $request, Response $response, $args)
    {
        $viaje = $this->controller->obtenerPorId($args['id']);
        if (!$viaje) {
            $response->getBody()->write(json_encode(['error' => 'Programación de viaje no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($viaje));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarEstado(Request $request, Response $response, $args)
    {
        $viajes = $this->controller->buscarPorEstado($args['estado']);
        $response->getBody()->write(json_encode($viajes));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarConductor(Request $request, Response $response, $args)
    {
        $viajes = $this->controller->buscarPorConductor($args['conductor_id']);
        $response->getBody()->write(json_encode($viajes));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarVehiculo(Request $request, Response $response, $args)
    {
        $viajes = $this->controller->buscarPorVehiculo($args['vehiculo_id']);
        $response->getBody()->write(json_encode($viajes));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function crear(Request $request, Response $response)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (
            empty($datos['conductor_id']) || empty($datos['vehiculo_id']) ||
            empty($datos['ruta_id']) || empty($datos['fecha_salida']) ||
            empty($datos['hora_salida']) || empty($datos['fecha_estimada_llegada'])
        ) {
            $response->getBody()->write(json_encode(['error' => 'Todos los campos obligatorios deben ser enviados']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!$this->rutaController->obtenerPorId($datos['ruta_id'])) {
            $response->getBody()->write(json_encode(['error' => 'La ruta indicada no existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($datos['fecha_estimada_llegada'] < $datos['fecha_salida']) {
            $response->getBody()->write(json_encode(['error' => 'La fecha estimada de llegada no puede ser anterior a la fecha de salida']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!empty($datos['estado']) && !in_array($datos['estado'], $this->estadosPermitidos)) {
            $response->getBody()->write(json_encode(['error' => 'Estado no válido. Use: programado, en_transito, retrasado, finalizado o cancelado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $viaje = $this->controller->crear($datos);
            $response->getBody()->write(json_encode([
                'mensaje' => 'Viaje programado exitosamente',
                'viaje' => $viaje
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al programar el viaje']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function actualizar(Request $request, Response $response, $args)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (!empty($datos['ruta_id']) && !$this->rutaController->obtenerPorId($datos['ruta_id'])) {
            $response->getBody()->write(json_encode(['error' => 'La ruta indicada no existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!empty($datos['estado']) && !in_array($datos['estado'], $this->estadosPermitidos)) {
            $response->getBody()->write(json_encode(['error' => 'Estado no válido. Use: programado, en_transito, retrasado, finalizado o cancelado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $viaje = $this->controller->actualizar($args['id'], $datos);
            if (!$viaje) {
                $response->getBody()->write(json_encode(['error' => 'Programación de viaje no encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'mensaje' => 'Programación de viaje actualizada exitosamente',
                'viaje' => $viaje
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al actualizar la programación de viaje']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function eliminar(Request $request, Response $response, $args)
    {
        $eliminado = $this->controller->eliminar($args['id']);
        if (!$eliminado) {
            $response->getBody()->write(json_encode(['error' => 'Programación de viaje no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode(['mensaje' => 'Programación de viaje eliminada exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}