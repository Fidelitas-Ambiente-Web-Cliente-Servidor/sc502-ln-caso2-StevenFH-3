<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Solicitud.php';
require_once __DIR__ . '/../models/Taller.php';

class AdminController
{
    private $solicitudModel;
    private $tallerModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->solicitudModel = new Solicitud($db);
        $this->tallerModel = new Taller($db);
    }

    public function solicitudes()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: index.php?page=login');
            return;
        }
        require __DIR__ . '/../views/admin/solicitudes.php';
    }

    //Obtiene las solicitudes por AJAX 
    public function getSolicitudesJson()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode([]);
            return;
        }
        $solicitudes = $this->solicitudModel->getPendientes();
        header('Content-Type: application/json');
        echo json_encode($solicitudes);
    }
    
    // Aprobar solicitud
    public function aprobar()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $solicitudId = $_POST['id_solicitud'] ?? 0;
        
        try {
            //Obtenemos la solicitud para saber a qué taller pertenece
            $solicitud = $this->solicitudModel->getById($solicitudId);

            if (!$solicitud || $solicitud['estado'] !== 'pendiente') {
                echo json_encode(['success' => false, 'error' => 'Solicitud no válida o ya procesada.']);
                return;
            }

            //Si descontarCupo() devuelve true, significa que sí había campo y se descontó
            if ($this->tallerModel->descontarCupo($solicitud['taller_id'])) {
                //Actualizamos el estado a aprobada
                $this->solicitudModel->actualizarEstado($solicitudId, 'aprobada');
                echo json_encode(['success' => true, 'message' => 'Solicitud aprobada y cupo descontado.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Operación fallida: El taller ya no cuenta con cupos disponibles.']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function rechazar()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $solicitudId = $_POST['id_solicitud'] ?? 0;
        
        //Usamos el método del modelo para actualizar a rechazada
        if ($this->solicitudModel->actualizarEstado($solicitudId, 'rechazada')) {
            echo json_encode(['success' => true, 'message' => 'Solicitud rechazada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al rechazar']);
        }
    }
}