<?php

namespace App\Controllers;

use App\Models\SeguimientoViaje;

class SeguimientoViajeController
{
    public function listarTodos()
    {
        return SeguimientoViaje::all();
    }

    public function obtenerPorId($id)
    {
        return SeguimientoViaje::find($id);
    }

    public function buscarPorViaje($programacionViajeId)
    {
        return SeguimientoViaje::where('programacion_viaje_id', $programacionViajeId)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();
    }

    public function buscarPorEstado($estado)
    {
        return SeguimientoViaje::where('estado', $estado)->get();
    }

    public function crear($datos)
    {
        $seguimiento = new SeguimientoViaje();
        $seguimiento->programacion_viaje_id = $datos['programacion_viaje_id'];
        $seguimiento->fecha = $datos['fecha'];
        $seguimiento->hora = $datos['hora'];
        $seguimiento->estado = $datos['estado'];
        $seguimiento->novedad = $datos['novedad'] ?? null;
        $seguimiento->save();

        return $seguimiento;
    }

    public function actualizar($id, $datos)
    {
        $seguimiento = SeguimientoViaje::find($id);
        if (!$seguimiento) {
            return null;
        }

        $seguimiento->programacion_viaje_id = $datos['programacion_viaje_id'] ?? $seguimiento->programacion_viaje_id;
        $seguimiento->fecha = $datos['fecha'] ?? $seguimiento->fecha;
        $seguimiento->hora = $datos['hora'] ?? $seguimiento->hora;
        $seguimiento->estado = $datos['estado'] ?? $seguimiento->estado;
        if (array_key_exists('novedad', $datos)) {
            $seguimiento->novedad = $datos['novedad'];
        }
        $seguimiento->save();

        return $seguimiento;
    }

    public function eliminar($id)
    {
        $seguimiento = SeguimientoViaje::find($id);
        if (!$seguimiento) {
            return false;
        }

        return $seguimiento->delete();
    }
}