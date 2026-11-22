<?php

namespace App\Controllers;

use App\Models\Vehiculo;

class VehiculoController
{
    public function listarTodos()
    {
        return Vehiculo::all();
    }

    public function obtenerPorId($id)
    {
        return Vehiculo::find($id);
    }

    public function buscarPorPlaca($placa)
    {
        return Vehiculo::where('placa', strtoupper($placa))->first();
    }

    public function buscarPorEstado($estado)
    {
        return Vehiculo::where('estado', $estado)->get();
    }

    public function buscarPorTipo($tipo)
    {
        return Vehiculo::where('tipo_vehiculo', $tipo)->get();
    }

    public function crear($datos)
    {
        $vehiculo = new Vehiculo();
        $vehiculo->placa = strtoupper($datos['placa']);
        $vehiculo->tipo_vehiculo = $datos['tipo_vehiculo'];
        $vehiculo->capacidad_carga = $datos['capacidad_carga'];
        $vehiculo->modelo = $datos['modelo'];
        $vehiculo->marca = $datos['marca'];
        $vehiculo->estado = $datos['estado'] ?? 'disponible';
        $vehiculo->save();

        return $vehiculo;
    }

    public function actualizar($id, $datos)
    {
        $vehiculo = Vehiculo::find($id);
        if (!$vehiculo) {
            return null;
        }

        if (!empty($datos['placa'])) {
            $vehiculo->placa = strtoupper($datos['placa']);
        }
        $vehiculo->tipo_vehiculo = $datos['tipo_vehiculo'] ?? $vehiculo->tipo_vehiculo;
        $vehiculo->capacidad_carga = $datos['capacidad_carga'] ?? $vehiculo->capacidad_carga;
        $vehiculo->modelo = $datos['modelo'] ?? $vehiculo->modelo;
        $vehiculo->marca = $datos['marca'] ?? $vehiculo->marca;
        $vehiculo->estado = $datos['estado'] ?? $vehiculo->estado;
        $vehiculo->save();

        return $vehiculo;
    }

    public function eliminar($id)
    {
        $vehiculo = Vehiculo::find($id);
        if (!$vehiculo) {
            return false;
        }

        return $vehiculo->delete();
    }
}