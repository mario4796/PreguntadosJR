<?php

class UsuarioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function validate($user, $pass)
    {
        $sql = "SELECT contrasenia, activo FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();

        if ($resultado) {
            $hashedPassword = $resultado['contrasenia'];
            $activo = $resultado['activo'];

            if ($activo == 0) {
                return 'inactiva';
            }
            if (password_verify($pass, $hashedPassword)) {
                return true;
            }
        }
        return false;
    }

    public function filter($user)
    {
        $sql = "SELECT id, nombre_usuario, nombre_completo, anio_nacimiento, sexo, pais, ciudad, foto_perfil, tipo_usuario
                FROM usuarios 
                WHERE nombre_usuario = ?";

        $stmt = $this->database->prepare($sql);
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $data["usuario"] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    public function crearUsuario($uuid, $username, $password, $fullname, $birthyear, $sexo, $email, $country, $city, $rutaImagen)
    {
        $existingUser = $this->filter($username);
        if (!empty($existingUser['usuario'])) {
            return 'existe';
        }
        $token = bin2hex(random_bytes(16));
        $activo = 0;

        $sql = "INSERT INTO usuarios (uuid, nombre_usuario, contrasenia, nombre_completo, anio_nacimiento, sexo, mail, pais, ciudad, foto_perfil, token, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param(
            'sssssssssssi',
            $uuid,
            $username,
            $password,
            $fullname,
            $birthyear,
            $sexo,
            $email,
            $country,
            $city,
            $rutaImagen,
            $token,
            $activo
        );
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $token;
    }

    public function buscarUsuarioPorToken($token)
    {
        $sql = "SELECT uuid FROM usuarios WHERE token = ? AND activo = 0";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function activarUsuario($uuid)
    {
        $sql = "UPDATE usuarios SET activo = 1 WHERE uuid = ?";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param('s', $uuid);
        $stmt->execute();
    }

    public function getUserRanking($usuario)
    {
        $sql = "SELECT MAX(resultado) as resultado FROM partidas WHERE id_jugador = " . $usuario;
        $result =$this->database->query($sql);
        return $result[0]['resultado'];
    }

    public function getHistorial5Partida($usuario)
    {
        $sql = "SELECT * FROM partidas
                WHERE id_jugador = " . $usuario . "
                ORDER BY fecha_creacion DESC
                LIMIT 5";
        return $this->database->query($sql);
    }

    public function getHistorialPartidas($usuario)
    {
        $sql = "SELECT *
        FROM partidas
        WHERE id_jugador = " . $usuario . "
        ORDER BY fecha_creacion DESC";
        return $this->database->query($sql);
    }

    public function existeUsername($username) {
        $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $this->database->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->num_rows > 0;
    }

    public function obtenerRankingUsuarios() {
        $sql = "SELECT u.nombre_usuario, MAX(p.resultado) AS resultado
            FROM usuarios u
            LEFT JOIN partidas p ON u.id = p.id_jugador
            GROUP BY u.id
            ORDER BY resultado DESC";
        $result = $this->database->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}