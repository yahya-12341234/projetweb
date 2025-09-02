<?php
include_once __DIR__.'/../../controller/utilisateurC.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch the user information
$userId = $_SESSION['user_id'];
$utilisateurC = new UtilisateurC();
$user = $utilisateurC->recupererUtilisateur($userId);

// Update the user profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    $utilisateurC->modifierUtilisateur(
        new Utilisateur($user['id'], $nom, $prenom, $email, $user['mdp'],$adresse,$telephone, $user['role']),
        $userId
    );

    // Reload updated user data
    $user = $utilisateurC->recupererUtilisateur($userId);
    $message = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Profile - Grow&Glow</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body>
    <header id="header" class="header d-flex align-items-center position-relative">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
            <a href="index.html" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="Grow&Glow">
            </a>
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="profile.php" class="active">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container py-5">
            <h1 class="text-center">Your Profile</h1>
            <?php if (isset($message)) : ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="profile.php" class="mt-4">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" class="form-control" id="nom" value="<?php echo $user['nom']; ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" class="form-control" id="prenom" value="<?php echo $user['prenom']; ?>" required>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group mt-3">
                    <label for="adresse">Adresse</label>
                    <input type="text" name="adresse" class="form-control" id="adresse" value="<?php echo $user['adresse']; ?>" required>
                </div>
                <div class="form-group mt-3">
                    <label for="telephone">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" id="telephone" value="<?php echo $user['telephone']; ?>" required>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </main>

    <footer id="footer" class="footer dark-background">
        <div class="container text-center">
            <p>© Grow&Glow. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
