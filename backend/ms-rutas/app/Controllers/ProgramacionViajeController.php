<?php

namespace App\Controllers;

use App\Models\ProgramacionViaje;

class ProgramacionViajeController
{
    public function listarTodos()
    {
        return ProgramacionViaje::all();
    }

    public function obtenerPorId($id)
    {
        return ProgramacionViaje::find($id);
    }

    public function buscarPorEstado($estado)
    {
        return ProgramacionViaje::where('estado', $estado)->get();
    }

    public function buscarPorConductor($conductorId)
    {
        return ProgramacionViaje::where('conductor_id', $conductorId)->get();
    }

    public function buscarPorVehiculo($vehiculoId)
    {
        return ProgramacionViaje::where('vehiculo_id', $vehiculoId)->get();
    }

    public function crear($datos)
    {
        $viaje = new ProgramacionViaje();
        $viaje->conductor_id = $datos['conductor_id'];
        $viaje->vehiculo_id = $datos['vehiculo_id'];
        $viaje->ruta_id = $datos['ruta_id'];
        $viaje->fecha_salida = $datos['fecha_salida'];
        $viaje->hora_salida = $datos['hora_salida'];
        $viaje->fecha_estimada_llegada = $datos['fecha_estimada_llegada'];
        $viaje->observaciones = $datos['observaciones'] ?? null;
        $viaje->estado = $datos['estado'] ?? 'programado';
        $viaje->save();

        return $viaje;
    }

    public function actualizar($id, $datos)
    {
        $viaje = ProgramacionViaje::find($id);
        if (!$viaje) {
            return null;
        }

        $viaje->conductor_id = $datos['conductor_id'] ?? $viaje->conductor_id;
        $viaje->vehiculo_id = $datos['vehiculo_id'] ?? $viaje->vehiculo_id;
        $viaje->ruta_id = $datos['ruta_id'] ?? $viaje->ruta_id;
        $viaje->fecha_salida = $datos['fecha_salida'] ?? $viaje->fecha_salida;
        $viaje->hora_salida = $datos['hora_salida'] ?? $viaje->hora_salida;
        $viaje->fecha_estimada_llegada = $datos['fecha_estimada_llegada'] ?? $viaje->fecha_estimada_llegada;
        if (array_key_exists('observaciones', $datos)) {
            $viaje->observaciones = $datos['observaciones'];
        }
        $viaje->estado = $datos['estado'] ?? $viaje->estado;
        $viaje->save();

        return $viaje;
    }

    public function eliminar($id)
    {
        $viaje = ProgramacionViaje::find($id);
        if (!$viaje) {
            return false;
        }

        return $viaje->delete();
    }
}