<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grow and Glow</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E6F2E6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('background-agriculture.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }

        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            padding: 25px;
            width: 90%;
            max-width: 650px;
        }

        .hidden {
            display: none;
        }

        h1 {
            color: #006400;
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            font-size: 18px;
            background-color: #228B22;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #006400;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #F0FFF0;
        }

        label {
            font-weight: bold;
            margin-top: 12px;
            color: #228B22;
        }
    </style>
</head>
<body>
    <!-- Page d'accueil -->
    <div class="container" id="home-page">
        <h1>Bienvenue sur CACTUS FIT </h1>

        <a href="#" class="btn" onclick="showSignUp()">Sign Up</a>
        <a href="#" class="btn" onclick="showLogin()">Login</a>
    </div>

    <!-- Page d'inscription -->
    <div class="container hidden" id="signup-page">
        <h1>Inscription</h1>
        <form id="signup-form" action="signup.php" method="POST" onsubmit="return validateSignupForm()" novalidate>
            <label for="first-name">Prénom:</label>
            <input type="text" id="first-name" name="prenom">
            <div class="error-message" id="prenom-error"></div>

            <label for="last-name">Nom:</label>
            <input type="text" id="last-name" name="nom">
            <div class="error-message" id="nom-error"></div>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <div class="error-message" id="email-error"></div>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="mdp">
            <div class="error-message" id="password-error"></div>

            <label for="address">Adresse:</label>
            <textarea id="address" name="adresse"></textarea>
            <div class="error-message" id="adresse-error"></div>

            <label for="phone">Téléphone:</label>
            <input type="tel" id="phone" name="telephone">
            <div class="error-message" id="telephone-error"></div>

            <input type="hidden" name="role" value="adherant"> <!-- Default role -->
            <button type="submit" class="btn">S'inscrire</button>
        </form>
        <a href="#" class="btn" onclick="goBack()">Retour</a>
    </div>

    <!-- Page de connexion -->
    <div class="container hidden" id="login-page">
        <h1>Connexion</h1>
        <form action="login.php" method="POST">
            <label for="login-email">Email:</label>
            <input type="email" id="login-email" name="email" required><br>
            <label for="login-password">Mot de passe:</label>
            <input type="password" id="login-password" name="mdp" required><br><br>
            <input type="submit" value="Se connecter" class="btn">
        </form>
        <a href="#" class="btn" onclick="goBack()">Retour</a>
    </div>

    <script>
        function hideAllPages() {
            var pages = document.querySelectorAll('.container');
            pages.forEach(function(page) {
                page.classList.add('hidden');
            });
        }

        function goBack() {
            hideAllPages();
            document.getElementById('home-page').classList.remove('hidden');
        }

        function showSignUp() {
            hideAllPages();
            document.getElementById('signup-page').classList.remove('hidden');
        }

        function showLogin() {
            hideAllPages();
            document.getElementById('login-page').classList.remove('hidden');
        }

        function validateSignupForm() {
            let isValid = true;

            // Efface les messages d'erreur précédents
            document.querySelectorAll('.error-message').forEach(span => span.innerText = '');

            // Validation des champs
            const prenom = document.getElementById('first-name').value.trim();
            const nom = document.getElementById('last-name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const address = document.getElementById('address').value.trim();
            const phone = document.getElementById('phone').value.trim();

            if (prenom === "") {
                document.getElementById('prenom-error').innerText = "Le prénom est obligatoire.";
                isValid = false;
            }

            if (nom === "") {
                document.getElementById('nom-error').innerText = "Le nom est obligatoire.";
                isValid = false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === "") {
                document.getElementById('email-error').innerText = "L'email est obligatoire.";
                isValid = false;
            } else if (!emailRegex.test(email)) {
                document.getElementById('email-error').innerText = "Veuillez entrer une adresse email valide.";
                isValid = false;
            }

            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (password === "") {
                document.getElementById('password-error').innerText = "Le mot de passe est obligatoire.";
                isValid = false;
            } else if (!passwordRegex.test(password)) {
                document.getElementById('password-error').innerText = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                isValid = false;
            }

            if (address === "") {
                document.getElementById('adresse-error').innerText = "L'adresse est obligatoire.";
                isValid = false;
            } else if (address.length < 5) {
                document.getElementById('adresse-error').innerText = "L'adresse doit contenir au moins 5 caractères.";
                isValid = false;
            }

            const phoneRegex = /^\+?[0-9\s-]{8,15}$/;
            if (phone === "") {
                document.getElementById('telephone-error').innerText = "Le téléphone est obligatoire.";
                isValid = false;
            } else if (!phoneRegex.test(phone)) {
                document.getElementById('telephone-error').innerText = "Veuillez entrer un numéro de téléphone valide (8 à 15 chiffres).";
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>














        
