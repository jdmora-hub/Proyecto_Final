<?php

namespace App\Presentation\Repositories;

use App\Controllers\VehiculoController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VehiculoRepository
{
    private $controller;
    private $estadosPermitidos = ['disponible', 'en_ruta', 'mantenimiento', 'inactivo'];

    public function __construct()
    {
        $this->controller = new VehiculoController();
    }

    public function listar(Request $request, Response $response)
    {
        $vehiculos = $this->controller->listarTodos();
        $response->getBody()->write(json_encode($vehiculos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function obtener(Request $request, Response $response, $args)
    {
        $vehiculo = $this->controller->obtenerPorId($args['id']);
        if (!$vehiculo) {
            $response->getBody()->write(json_encode(['error' => 'Vehículo no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($vehiculo));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarPlaca(Request $request, Response $response, $args)
    {
        $vehiculo = $this->controller->buscarPorPlaca($args['placa']);
        if (!$vehiculo) {
            $response->getBody()->write(json_encode(['error' => 'Vehículo no encontrado con esa placa']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($vehiculo));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarEstado(Request $request, Response $response, $args)
    {
        $vehiculos = $this->controller->buscarPorEstado($args['estado']);
        $response->getBody()->write(json_encode($vehiculos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function buscarTipo(Request $request, Response $response, $args)
    {
        $vehiculos = $this->controller->buscarPorTipo($args['tipo']);
        $response->getBody()->write(json_encode($vehiculos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function crear(Request $request, Response $response)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (
            empty($datos['placa']) || empty($datos['tipo_vehiculo']) ||
            !isset($datos['capacidad_carga']) || empty($datos['modelo']) ||
            empty($datos['marca'])
        ) {
            $response->getBody()->write(json_encode(['error' => 'Todos los campos obligatorios deben ser enviados']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!is_numeric($datos['capacidad_carga']) || $datos['capacidad_carga'] <= 0) {
            $response->getBody()->write(json_encode(['error' => 'La capacidad de carga debe ser mayor a cero']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!empty($datos['estado']) && !in_array($datos['estado'], $this->estadosPermitidos)) {
            $response->getBody()->write(json_encode(['error' => 'Estado no válido. Use: disponible, en_ruta, mantenimiento o inactivo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($this->controller->buscarPorPlaca($datos['placa'])) {
            $response->getBody()->write(json_encode(['error' => 'Ya existe un vehículo registrado con esa placa']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $vehiculo = $this->controller->crear($datos);
            $response->getBody()->write(json_encode([
                'mensaje' => 'Vehículo registrado exitosamente',
                'vehiculo' => $vehiculo
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al registrar el vehículo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function actualizar(Request $request, Response $response, $args)
    {
        $datos = json_decode($request->getBody()->getContents(), true);

        if (isset($datos['capacidad_carga']) && (!is_numeric($datos['capacidad_carga']) || $datos['capacidad_carga'] <= 0)) {
            $response->getBody()->write(json_encode(['error' => 'La capacidad de carga debe ser mayor a cero']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!empty($datos['estado']) && !in_array($datos['estado'], $this->estadosPermitidos)) {
            $response->getBody()->write(json_encode(['error' => 'Estado no válido. Use: disponible, en_ruta, mantenimiento o inactivo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!empty($datos['placa'])) {
            $existente = $this->controller->buscarPorPlaca($datos['placa']);
            if ($existente && $existente->id != $args['id']) {
                $response->getBody()->write(json_encode(['error' => 'Ya existe otro vehículo con esa placa']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }

        try {
            $vehiculo = $this->controller->actualizar($args['id'], $datos);
            if (!$vehiculo) {
                $response->getBody()->write(json_encode(['error' => 'Vehículo no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'mensaje' => 'Vehículo actualizado exitosamente',
                'vehiculo' => $vehiculo
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Error al actualizar el vehículo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function eliminar(Request $request, Response $response, $args)
    {
        $eliminado = $this->controller->eliminar($args['id']);
        if (!$eliminado) {
            $response->getBody()->write(json_encode(['error' => 'Vehículo no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode(['mensaje' => 'Vehículo eliminado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}