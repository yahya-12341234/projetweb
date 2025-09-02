<?php
include_once __DIR__.'/../../controller/utilisateurC.php';
include_once __DIR__.'/../../model/utilisateur.php';
include_once __DIR__.'/../../controller/activiteC.php';
$utilisateurC = new UtilisateurC();
$activiteC = new ActiviteSportiveC();
$listactivites = $activiteC->afficherActivites();

// Fonction de validation des données
function validerDonnees($nom, $prenom, $email, $telephone, $adresse, $role) {
    $erreurs = [];
    
    if (empty(trim($nom))) {
        $erreurs[] = "Le nom est obligatoire";
    }
    
    if (empty(trim($prenom))) {
        $erreurs[] = "Le prénom est obligatoire";
    }
    
    if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'email est invalide";
    }
    
    if (empty(trim($telephone)) || !preg_match('/^[0-9+\-\s]+$/', $telephone)) {
        $erreurs[] = "Le téléphone est invalide";
    }
    
    if (empty(trim($adresse))) {
        $erreurs[] = "L'adresse est obligatoire";
    }
    
    if (empty(trim($role))) {
        $erreurs[] = "Le rôle est obligatoire";
    }
    
    return $erreurs;
}

// Suppression d'un utilisateur
if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    try {
        $utilisateurC->supprimerUtilisateur($id);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        exit();
    }
}

// Modification d'un utilisateur
if (isset($_POST['edit_user'])) {
    header('Content-Type: application/json');
    
    $id = $_POST['id'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $adresse = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);
    
    // Validation des données
    $erreurs = validerDonnees($nom, $prenom, $email, $telephone, $adresse, $role);
    
    if (!empty($erreurs)) {
        echo json_encode(['status' => 'error', 'message' => implode(', ', $erreurs)]);
        exit();
    }
    
    try {
        // Cryptage du mot de passe seulement s'il n'est pas vide
        $mdp = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;
        
        $utilisateur = new Utilisateur(
            $id,
            $nom, 
            $prenom, 
            $email, 
            $mdp,
            $adresse, 
            $telephone, 
            $role
        );

        $utilisateurC->modifierUtilisateur($utilisateur, $id);
        echo json_encode(['status' => 'success', 'message' => 'Utilisateur modifié avec succès']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la modification: ' . $e->getMessage()]);
        exit();
    }
}

// Récupération des utilisateurs
$utilisateurs = $utilisateurC->afficherUtilisateurs();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Gestion des utilisateurs</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Modal personnalisé styles */
        .swal2-popup.custom-modal {
            border-radius: 20px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: none !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            overflow: hidden !important;
        }

        .swal2-popup.custom-modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: -1;
        }

        .custom-modal .swal2-title {
            color: #2d3748 !important;
            font-weight: 700 !important;
            font-size: 1.8rem !important;
            margin-bottom: 30px !important;
            text-shadow: none !important;
        }

        .swal2-input-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: flex-end;
        }

        .swal2-input-group {
            flex: 1;
            text-align: left;
        }

        .swal2-input-group.full-width {
            flex: 1 1 100%;
        }

        .swal2-input-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .custom-modal .swal2-input,
        .custom-modal .swal2-select {
            margin: 0 !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            font-size: 1rem !important;
            background: white !important;
            color: #2d3748 !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .custom-modal .swal2-input:focus,
        .custom-modal .swal2-select:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
            transform: translateY(-2px) !important;
        }

        .custom-modal .swal2-input::placeholder {
            color: #a0aec0 !important;
            font-style: italic;
        }

        .input-icon {
            position: relative;
        }

        .input-icon .fas {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 10;
        }

        .input-icon .swal2-input {
            padding-left: 40px !important;
        }

        .custom-modal .swal2-actions {
            margin-top: 30px !important;
            gap: 15px !important;
        }

        .custom-modal .swal2-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
            transition: all 0.3s ease !important;
        }

        .custom-modal .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6) !important;
        }

        .custom-modal .swal2-cancel {
            background: #e2e8f0 !important;
            color: #4a5568 !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .custom-modal .swal2-cancel:hover {
            background: #cbd5e0 !important;
            transform: translateY(-2px) !important;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .role-user { background: #e6fffa; color: #38b2ac; }
        .role-admin { background: #fed7d7; color: #e53e3e; }
        .role-entrainer { background: #fef5e7; color: #d69e2e; }

        /* Animation d'entrée */
        .swal2-popup.custom-modal {
            animation: slideInUp 0.4s ease-out !important;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 100%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        /* Styles pour le tableau des activités */
        .activities-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e3e6f0;
        }

        .activities-section h2 {
            color: #4e73df;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active { background: #e6fffa; color: #38b2ac; }
        .status-inactive { background: #fed7d7; color: #e53e3e; }
        .status-pending { background: #fef5e7; color: #d69e2e; }

        /* Responsive */
        @media (max-width: 768px) {
            .swal2-input-row {
                flex-direction: column;
                gap: 0;
            }
            
            .custom-modal {
                width: 95% !important;
                margin: 10px !important;
            }
        }
    </style>
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>
        <hr class="sidebar-divider">
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-user"></i>
                <span>Utilisateurs</span>
            </a>
        </li>

    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../frontOffice/logout.php">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid mt-4">
                <h1 class="h3 mb-4 text-gray-800">Liste des utilisateurs</h1>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Adresse</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur) { ?>
                        <tr>
                            <td><?= htmlspecialchars($utilisateur['id']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['telephone']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['adresse']) ?></td>
                            <td>
                                <span class="role-badge role-<?= htmlspecialchars($utilisateur['role']) ?>">
                                    <?= ucfirst(htmlspecialchars($utilisateur['role'])) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-user"
                                    data-id="<?= htmlspecialchars($utilisateur['id']) ?>"
                                    data-nom="<?= htmlspecialchars($utilisateur['nom']) ?>"
                                    data-prenom="<?= htmlspecialchars($utilisateur['prenom']) ?>"
                                    data-email="<?= htmlspecialchars($utilisateur['email']) ?>"
                                    data-telephone="<?= htmlspecialchars($utilisateur['telephone']) ?>"
                                    data-adresse="<?= htmlspecialchars($utilisateur['adresse']) ?>"
                                    data-role="<?= htmlspecialchars($utilisateur['role']) ?>">
                                    Modifier
                                </button>
                                <button class="btn btn-danger btn-sm delete-user" 
                                    data-id="<?= htmlspecialchars($utilisateur['id']) ?>">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Section des activités -->
                <div class="activities-section">
                    <h2 class="h3 mb-4 text-gray-800">Liste des activités sportives</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Lieu</th>
                                <th>Capacité</th>
                                <th>Niveau</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listactivites as $activite) { ?>
                            <tr>
                                <td><?= htmlspecialchars($activite['id_activite']) ?></td>
                                <td><?= htmlspecialchars($activite['nom_activite']) ?></td>
                                <td><?= htmlspecialchars($activite['description']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($activite['date_debut'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($activite['date_fin'])) ?></td>
                                <td><?= htmlspecialchars($activite['lieu']) ?></td>
                                <td><?= htmlspecialchars($activite['capacite_max']) ?></td>
                                <td><?= htmlspecialchars($activite['niveau']) ?></td>

                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Votre site 2024</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
// Fonction de validation côté client
function validerFormulaire(nom, prenom, email, telephone, adresse, role) {
    const erreurs = [];
    
    if (!nom.trim()) erreurs.push("Le nom est obligatoire");
    if (!prenom.trim()) erreurs.push("Le prénom est obligatoire");
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.trim() || !emailRegex.test(email)) erreurs.push("L'email est invalide");
    
    const telephoneRegex = /^[0-9+\-\s]+$/;
    if (!telephone.trim() || !telephoneRegex.test(telephone)) erreurs.push("Le téléphone est invalide");
    
    if (!adresse.trim()) erreurs.push("L'adresse est obligatoire");
    if (!role.trim()) erreurs.push("Le rôle est obligatoire");
    
    return erreurs;
}

document.querySelectorAll('.delete-user').forEach(button => {
    button.addEventListener('click', function () {
        const userId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cet utilisateur sera supprimé définitivement.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('delete_user', '1');
                formData.append('user_id', userId);

                fetch('', { method: 'POST', body: formData })
                    .then(response => {
                        if (response.redirected) {
                            location.reload();
                        } else {
                            return response.json();
                        }
                    })
                    .then(data => {
                        if (data && data.status === 'error') {
                            Swal.fire('Erreur', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
                    });
            }
        });
    });
});

document.querySelectorAll('.edit-user').forEach(button => {
    button.addEventListener('click', function () {
        const userId = this.dataset.id;
        const nom = this.dataset.nom;
        const prenom = this.dataset.prenom;
        const email = this.dataset.email;
        const telephone = this.dataset.telephone;
        const adresse = this.dataset.adresse;
        const role = this.dataset.role;

        Swal.fire({
            title: '✨ Modifier l\'utilisateur',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <div class="swal2-input-row">
                        <div class="swal2-input-group">
                            <label class="swal2-input-label">👤 Nom *</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input id="nom" class="swal2-input" placeholder="Entrez le nom" value="${nom}" required>
                            </div>
                        </div>
                        <div class="swal2-input-group">
                            <label class="swal2-input-label">👤 Prénom *</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input id="prenom" class="swal2-input" placeholder="Entrez le prénom" value="${prenom}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swal2-input-row">
                        <div class="swal2-input-group full-width">
                            <label class="swal2-input-label">📧 Email *</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input id="email" class="swal2-input" placeholder="exemple@email.com" value="${email}" type="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swal2-input-row">
                        <div class="swal2-input-group">
                            <label class="swal2-input-label">📱 Téléphone *</label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input id="telephone" class="swal2-input" placeholder="+33 1 23 45 67 89" value="${telephone}" required>
                            </div>
                        </div>
                        <div class="swal2-input-group">
                            <label class="swal2-input-label">🎭 Rôle *</label>
                            <select id="role" class="swal2-select" required>
                                <option value="user" ${role === 'user' ? 'selected' : ''}>👥 Utilisateur</option>
                                <option value="admin" ${role === 'admin' ? 'selected' : ''}>👑 Administrateur</option>
                                <option value="entrainer" ${role === 'entrainer' ? 'selected' : ''}>🛡️ entrainer</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="swal2-input-row">
                        <div class="swal2-input-group full-width">
                            <label class="swal2-input-label">🏠 Adresse *</label>
                            <div class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input id="adresse" class="swal2-input" placeholder="Entrez l'adresse complète" value="${adresse}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swal2-input-row">
                        <div class="swal2-input-group full-width">
                            <label class="swal2-input-label">🔐 Nouveau mot de passe (optionnel)</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input id="password" type="password" class="swal2-input" placeholder="Laissez vide pour conserver l'actuel">
                            </div>
                            <small style="color: #a0aec0; font-size: 0.85rem; margin-top: 5px; display: block;">
                                💡 Minimum 8 caractères recommandés
                            </small>
                        </div>
                    </div>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: '💾 Enregistrer',
            cancelButtonText: '❌ Annuler',
            width: '650px',
            customClass: {
                popup: 'custom-modal'
            },
            preConfirm: () => {
                const nom = document.getElementById('nom').value.trim();
                const prenom = document.getElementById('prenom').value.trim();
                const email = document.getElementById('email').value.trim();
                const telephone = document.getElementById('telephone').value.trim();
                const adresse = document.getElementById('adresse').value.trim();
                const role = document.getElementById('role').value.trim();
                const password = document.getElementById('password').value.trim();

                // Validation côté client
                const erreurs = validerFormulaire(nom, prenom, email, telephone, adresse, role);
                
                if (erreurs.length > 0) {
                    Swal.showValidationMessage(erreurs.join('<br>'));
                    return false;
                }

                return {
                    id: userId,
                    nom,
                    prenom,
                    email,
                    telephone,
                    adresse,
                    role,
                    password
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('edit_user', '1');
                for (const [key, value] of Object.entries(result.value)) {
                    formData.append(key, value);
                }

                // Afficher un loader stylé
                Swal.fire({
                    title: '⏳ Modification en cours...',
                    html: '<div style="margin-top: 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'custom-modal'
                    },
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('', { method: 'POST', body: formData })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Réponse réseau non valide');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: '🎉 Succès !',
                                text: data.message,
                                icon: 'success',
                                timer: 2500,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'custom-modal'
                                },
                                didOpen: () => {
                                    // Animation de confettis
                                    const popup = Swal.getPopup();
                                    popup.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                                    popup.style.color = 'white';
                                }
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '❌ Erreur',
                                text: data.message,
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-modal'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur de fetch:', error);
                        Swal.fire({
                            title: '⚠️ Erreur',
                            text: 'Une erreur est survenue lors de la modification.',
                            icon: 'error',
                            customClass: {
                                popup: 'custom-modal'
                            }
                        });
                    });
            }
        });
    });
});
</script>

</body>
</html>