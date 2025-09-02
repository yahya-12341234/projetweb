<?php
include_once "../config.php";
include_once "../model/utilisateur.php";
include_once "../controller/utilisateurC.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT); // Encrypt password
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];

    $utilisateur = new Utilisateur(null, $nom, $prenom, $email, $mdp,$adresse,$telephone, $role);


    $utilisateurC = new UtilisateurC();
    $utilisateurC->ajouterUtilisateur($utilisateur);

    header("Location: index.php");
}
?>
