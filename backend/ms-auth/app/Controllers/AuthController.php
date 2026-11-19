<?php

namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    public function login(string $identificador, string $contrasena): Usuario
    {
        $usuario = Usuario::where(function ($query) use ($identificador) {
                $query->where('usuario', $identificador)
                      ->orWhere('correo', $identificador);
            })
            ->where('contrasena', $contrasena)
            ->first();

        if (!$usuario) {
            throw new Exception("Credenciales incorrectas", 401);
        }

        if ($usuario->estado !== 'activo') {
            throw new Exception("El usuario se encuentra inactivo", 403);
        }

        $usuario->token = bin2hex(random_bytes(32));
        $usuario->sesion_activa = true;
        $usuario->save();

        return $usuario;
    }

    public function logout(string $token): bool
    {
        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (!$usuario) {
            throw new Exception("Token inválido o sesión ya cerrada", 401);
        }

        $usuario->token = null;
        $usuario->sesion_activa = false;
        $usuario->save();

        return true;
    }

    public function validarSesion(string $token): Usuario
    {
        if (empty($token)) {
            throw new Exception("Token no proporcionado", 401);
        }

        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            throw new Exception("Sesión inválida o expirada", 401);
        }

        return $usuario;
    }
}