<?php
class Solicitud
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function existeSolicitud($usuarioId, $tallerId)
    {
        //Verifica si ya existe una solicitud pendiente o aprobada para evitar duplicados
        $query = "SELECT id FROM solicitudes WHERE usuario_id = ? AND taller_id = ? AND estado IN ('pendiente', 'aprobada')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $usuarioId, $tallerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function crear($usuarioId, $tallerId)
    {
        //Crea una nueva solicitud con estado "pendiente"
        $query = "INSERT INTO solicitudes (usuario_id, taller_id, estado) VALUES (?, ?, 'pendiente')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $usuarioId, $tallerId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getPendientes()
    {
        //Traemos las solicitudes pendientes junto con el nombre del taller y el username del usuario
        $query = "SELECT s.id, s.fecha_solicitud, t.nombre as taller, u.username as usuario 
                  FROM solicitudes s
                  INNER JOIN talleres t ON s.taller_id = t.id
                  INNER JOIN usuarios u ON s.usuario_id = u.id
                  WHERE s.estado = 'pendiente'
                  ORDER BY s.fecha_solicitud ASC";
        $result = $this->conn->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = $row;
        }
        return $solicitudes;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM solicitudes WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function actualizarEstado($id, $estado)
    {
        $query = "UPDATE solicitudes SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getTalleresSolicitadosPorUsuario($usuarioId)
    {
        $query = "SELECT taller_id, estado FROM solicitudes WHERE usuario_id = ? AND estado IN ('pendiente', 'aprobada')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $solicitados = [];
        while ($row = $result->fetch_assoc()) {
            //Guardamos el ID del taller como llave y su estado como valor
            $solicitados[$row['taller_id']] = $row['estado'];
        }
        return $solicitados;
    }
}