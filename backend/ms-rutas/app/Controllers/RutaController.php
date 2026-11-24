<?php

namespace App\Controllers;

use App\Models\Ruta;

class RutaController
{
    public function listarTodas()
    {
        return Ruta::all();
    }

    public function obtenerPorId($id)
    {
        return Ruta::find($id);
    }

    public function buscarPorOrigen($origen)
    {
        return Ruta::where('ciudad_origen', 'LIKE', "%{$origen}%")->get();
    }

    public function buscarPorDestino($destino)
    {
        return Ruta::where('ciudad_destino', 'LIKE', "%{$destino}%")->get();
    }

    public function crear($datos)
    {
        $ruta = new Ruta();
        $ruta->ciudad_origen = $datos['ciudad_origen'];
        $ruta->ciudad_destino = $datos['ciudad_destino'];
        $ruta->distancia = $datos['distancia'];
        $ruta->tiempo_estimado = $datos['tiempo_estimado'];
        $ruta->observaciones = $datos['observaciones'] ?? null;
        $ruta->save();

        return $ruta;
    }

    public function actualizar($id, $datos)
    {
        $ruta = Ruta::find($id);
        if (!$ruta) {
            return null;
        }

        $ruta->ciudad_origen = $datos['ciudad_origen'] ?? $ruta->ciudad_origen;
        $ruta->ciudad_destino = $datos['ciudad_destino'] ?? $ruta->ciudad_destino;
        $ruta->distancia = $datos['distancia'] ?? $ruta->distancia;
        $ruta->tiempo_estimado = $datos['tiempo_estimado'] ?? $ruta->tiempo_estimado;
        if (array_key_exists('observaciones', $datos)) {
            $ruta->observaciones = $datos['observaciones'];
        }
        $ruta->save();

        return $ruta;
    }

    public function eliminar($id)
    {
        $ruta = Ruta::find($id);
        if (!$ruta) {
            return false;
        }

        return $ruta->delete();
    }
}