<?php

namespace App\Presentation\Repositories;

use App\Controllers\AuthController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthRepository
{
    private function extraerToken(Request $request): string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }
        return trim($authHeader);
    }

    public function login(Request $request, Response $response, $args): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $identificador = $data['usuario'] ?? $data['correo'] ?? '';
            $contrasena = $data['contrasena'] ?? '';

            if (empty($identificador) || empty($contrasena)) {
                throw new Exception("Debe ingresar usuario/correo y contraseña", 400);
            }

            $controller = new AuthController();
            $usuario = $controller->login($identificador, $contrasena);

            $payload = json_encode([
                'mensaje' => 'Inicio de sesión exitoso',
                'token'   => $usuario->token,
                'usuario' => $usuario
            ]);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $ex) {
            $code = ($ex->getCode() >= 400 && $ex->getCode() < 600) ? $ex->getCode() : 400;
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
        }
    }

    public function logout(Request $request, Response $response, $args): Response
    {
        try {
            $token = $this->extraerToken($request);
            if (empty($token)) {
                $data = json_decode($request->getBody()->getContents(), true);
                $token = $data['token'] ?? '';
            }

            $controller = new AuthController();
            $controller->logout($token);

            $response->getBody()->write(json_encode(['mensaje' => 'Sesión cerrada correctamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $ex) {
            $code = ($ex->getCode() >= 400 && $ex->getCode() < 600) ? $ex->getCode() : 400;
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
        }
    }

    public function validar(Request $request, Response $response, $args): Response
    {
        try {
            $token = $this->extraerToken($request);
            $controller = new AuthController();
            $usuario = $controller->validarSesion($token);

            $payload = json_encode([
                'valida'  => true,
                'usuario' => $usuario
            ]);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode([
                'valida' => false,
                'error'  => $ex->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}