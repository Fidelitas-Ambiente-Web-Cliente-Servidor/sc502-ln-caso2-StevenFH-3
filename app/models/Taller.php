<?php
class Taller
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM talleres ORDER BY nombre");
        $talleres = [];
        while ($row = $result->fetch_assoc()) {
            $talleres[] = $row;
        }
        return $talleres;
    }

    public function getAllDisponibles()
    {
        //Solo trae los talleres donde el cupo disponible sea mayor a 0
        $query = "SELECT * FROM talleres WHERE cupo_disponible > 0 ORDER BY nombre";
        $result = $this->conn->query($query);
        $talleres = [];
        while ($row = $result->fetch_assoc()) {
            $talleres[] = $row;
        }
        return $talleres;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM talleres WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function descontarCupo($tallerId)
    {
        //El "AND cupo_disponible > 0" evita números negativos
        $query = "UPDATE talleres SET cupo_disponible = cupo_disponible - 1 WHERE id = ? AND cupo_disponible > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $tallerId);
        $stmt->execute();
        
        //Retorna true si había cupo 
        return $stmt->affected_rows > 0;
    }

    public function sumarCupo($tallerId)
    {
        $query = "UPDATE talleres SET cupo_disponible = cupo_disponible + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $tallerId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}