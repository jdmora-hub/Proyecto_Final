<?php

namespace App\Controllers;

use App\Models\Conductor;

class ConductorController
{
    public function listarTodos()
    {
        return Conductor::all();
    }

    public function obtenerPorId($id)
    {
        return Conductor::find($id);
    }

    public function buscarPorDocumento($documento)
    {
        return Conductor::where('documento', $documento)->first();
    }

    public function buscarPorLicencia($licencia)
    {
        return Conductor::where('numero_licencia', $licencia)->first();
    }

    public function buscarPorEstado($estado)
    {
        return Conductor::where('estado', $estado)->get();
    }

    public function crear($datos)
    {
        $conductor = new Conductor();
        $conductor->nombres = $datos['nombres'];
        $conductor->apellidos = $datos['apellidos'];
        $conductor->documento = $datos['documento'];
        $conductor->telefono = $datos['telefono'];
        $conductor->correo = $datos['correo'];
        $conductor->numero_licencia = $datos['numero_licencia'];
        $conductor->categoria_licencia = $datos['categoria_licencia'];
        $conductor->fecha_vencimiento_licencia = $datos['fecha_vencimiento_licencia'];
        $conductor->estado = $datos['estado'] ?? 'disponible';
        $conductor->save();

        return $conductor;
    }

    public function actualizar($id, $datos)
    {
        $conductor = Conductor::find($id);
        if (!$conductor) {
            return null;
        }

        $conductor->nombres = $datos['nombres'] ?? $conductor->nombres;
        $conductor->apellidos = $datos['apellidos'] ?? $conductor->apellidos;
        $conductor->documento = $datos['documento'] ?? $conductor->documento;
        $conductor->telefono = $datos['telefono'] ?? $conductor->telefono;
        $conductor->correo = $datos['correo'] ?? $conductor->correo;
        $conductor->numero_licencia = $datos['numero_licencia'] ?? $conductor->numero_licencia;
        $conductor->categoria_licencia = $datos['categoria_licencia'] ?? $conductor->categoria_licencia;
        $conductor->fecha_vencimiento_licencia = $datos['fecha_vencimiento_licencia'] ?? $conductor->fecha_vencimiento_licencia;
        $conductor->estado = $datos['estado'] ?? $conductor->estado;
        $conductor->save();

        return $conductor;
    }

    public function eliminar($id)
    {
        $conductor = Conductor::find($id);
        if (!$conductor) {
            return false;
        }

        return $conductor->delete();
    }
}