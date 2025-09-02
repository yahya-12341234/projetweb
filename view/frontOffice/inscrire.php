<?php
include_once __DIR__ . '/../../controller/InscriptionC.php';
include_once __DIR__ . '/../../model/inscription.php';
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour vous inscrire.'
    ]);
    exit();
}

// Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ]);
    exit();
}

// Vérification des données requises
if (!isset($_POST['id_activite']) || empty($_POST['id_activite'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de l\'activité manquant.'
    ]);
    exit();
}

try {
    $id_activite = (int)$_POST['id_activite'];
    $id_utilisateur = (int)$_SESSION['user_id'];
    $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : null;

    // Créer une instance du contrôleur
    $inscriptionC = new InscriptionC();
    
    // Utiliser la méthode avec gestion automatique de la liste d'attente
    $resultat = $inscriptionC->inscrireAvecListeAttente($id_activite, $id_utilisateur, $commentaire);
    
    echo json_encode($resultat);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
    ]);
}
?>