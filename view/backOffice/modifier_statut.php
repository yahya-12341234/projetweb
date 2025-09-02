<?php
include_once __DIR__ . '/../../controller/inscriptionC.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_inscription'], $_POST['nouveau_statut'])) {
    $inscriptionC = new InscriptionC();
    $result = $inscriptionC->modifierStatutInscription((int)$_POST['id_inscription'], $_POST['nouveau_statut']);

    if ($result['success']) {
        echo json_encode(['status' => 'success', 'message' => $result['message']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
}
