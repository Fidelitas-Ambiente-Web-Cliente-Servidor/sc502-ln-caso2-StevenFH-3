<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Taller.php';
require_once __DIR__ . '/../models/Solicitud.php';

class TallerController
{
    private $tallerModel;
    private $solicitudModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->tallerModel = new Taller($db);
        $this->solicitudModel = new Solicitud($db);
    }

    public function index()
    {
        if (!isset($_SESSION['id'])) {
            header('Location: index.php?page=login');
            return;
        }
        require __DIR__ . '/../views/taller/listado.php';
    }
    
    public function getTalleresJson()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode([]);
            return;
        }
        
        $usuarioId = $_SESSION['id'];
        $talleres = $this->tallerModel->getAllDisponibles();
        $solicitados = $this->solicitudModel->getTalleresSolicitadosPorUsuario($usuarioId);

        //Agrega información de solicitud a cada taller
        foreach ($talleres as &$taller) {
            $tallerId = $taller['id'];
            if (isset($solicitados[$tallerId])) {
                $taller['ya_solicitado'] = true;
                $taller['estado_solicitud'] = $solicitados[$tallerId];
            } else {
                $taller['ya_solicitado'] = false;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($talleres);
    }
    
    public function solicitar()
    {
        //Solo usuarios logueados pueden solicitar
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
            return;
        }
        
        $tallerId = $_POST['taller_id'] ?? 0;
        $usuarioId = $_SESSION['id'];

        //Valida que el tallerId sea un número positivo
        if ($tallerId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Taller no válido']);
            return;
        }

        //Valida que no tenga ya una solicitud (pendiente o aprobada)
        if ($this->solicitudModel->existeSolicitud($usuarioId, $tallerId)) {
            echo json_encode(['success' => false, 'error' => 'Ya tienes una solicitud activa o aprobada para este taller.']);
            return;
        }

        //Crear la solicitud
        if ($this->solicitudModel->crear($usuarioId, $tallerId)) {
            echo json_encode(['success' => true, 'message' => 'Solicitud enviada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud.']);
        }
    }
}