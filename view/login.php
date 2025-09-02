<?php
session_start();
include_once "../config.php";
include_once "../controller/utilisateurC.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['mdp']);

    $utilisateurC = new UtilisateurC();
    $user = $utilisateurC->login($email, $password);

    if ($user) {
        // Start the session and store user details
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_prenom'] = $user->getPrenom();
        $_SESSION['user_nom'] = $user->getNom();
        $_SESSION['user_role'] = $user->getRole();

        // Redirect to profile page
        if($_SESSION['user_role'] == "adherant")
        header("Location: frontOffice/index.php");
    else if ($_SESSION['user_role'] == "entrainer")
    header("Location: backOffice/entrainer.php");
    else
        header("Location: backOffice/index.php");
        exit();
    } else {
        // Set an error message and stay on the login page
        $error = "Email ou mot de passe incorrect.";
    }
}
?>