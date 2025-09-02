<?php
include_once __DIR__ . '/../../controller/utilisateurC.php';
include_once __DIR__ . '/../../controller/activiteC.php';
include_once __DIR__ . '/../../controller/inscriptionC.php';
session_start();

$utilisateurC = new UtilisateurC();
$activiteC = new ActiviteSportiveC();
$inscriptionC = new InscriptionC();

// Vérifier la session utilisateur
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontOffice/login.php");
    exit();
}

// Récupérer les informations de l'utilisateur
$admin = $utilisateurC->recupererUtilisateur($_SESSION['user_id']);

// Gestion des activités sportives
// Ajouter une activité
if (isset($_POST['ajouter_activite'])) {
    try {
        $activite = new ActiviteSportive(
            null,
            $_POST['nom_activite'],
            $_POST['description'],
            new DateTime($_POST['date_debut']),
            new DateTime($_POST['date_fin']),
            $_POST['lieu'],
            $_POST['capacite_max'],
            $_POST['niveau'],
            $_SESSION['user_id'],
            'active'
        );

        $activiteC->ajouterActivite($activite);
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'Activité ajoutée avec succès!');
        header("Location: entrainer.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage());
        header("Location: entrainer.php");
        exit();
    }
}

// Modifier une activité
if (isset($_POST['modifier_activite'])) {
    try {
        $activite = new ActiviteSportive(
            $_POST['id_activite'],
            $_POST['nom_activite'],
            $_POST['description'],
            new DateTime($_POST['date_debut']),
            new DateTime($_POST['date_fin']),
            $_POST['lieu'],
            $_POST['capacite_max'],
            $_POST['niveau'],
            $_SESSION['user_id'],
            $_POST['statut']
        );

        $activiteC->modifierActivite($activite, $_POST['id_activite']);
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'Activité modifiée avec succès!');
        header("Location: entrainer.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Erreur lors de la modification: ' . $e->getMessage());
        header("Location: entrainer.php");
        exit();
    }
}

// Supprimer une activité
if (isset($_GET['supprimer_activite'])) {
    try {
        $activiteC->supprimerActivite($_GET['supprimer_activite']);
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'Activité supprimée avec succès!');
        header("Location: entrainer.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Erreur lors de la suppression: ' . $e->getMessage());
        header("Location: entrainer.php");
        exit();
    }
}

// Gestion des inscriptions
// Modifier le statut d'une inscription
if (isset($_POST['modifier_statut_inscription'])) {
    try {
        $inscriptionC->modifierStatutInscription($_POST['id_inscription'], $_POST['nouveau_statut']);
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'Statut de l\'inscription modifié avec succès!');
        header("Location: entrainer.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Erreur lors de la modification: ' . $e->getMessage());
        header("Location: entrainer.php");
        exit();
    }
}

// Supprimer une inscription
if (isset($_GET['supprimer_inscription'])) {
    try {
        $inscriptionC->supprimerInscription($_GET['supprimer_inscription']);
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'Inscription supprimée avec succès!');
        header("Location: entrainer.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Erreur lors de la suppression: ' . $e->getMessage());
        header("Location: entrainer.php");
        exit();
    }
}

// Récupérer toutes les activités
$activites = $activiteC->afficherActivites();

// Récupérer toutes les inscriptions
$inscriptions = $inscriptionC->afficherInscriptions();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Coach - Gestion des Activités et Inscriptions</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DataTables CSS pour la recherche et le filtrage -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Coach</div>
            </a>
            <hr class="sidebar-divider">

            <li class="nav-item active">
                <a class="nav-link" href="entrainer.php">
                    <i class="fas fa-running"></i>
                    <span>Activités & Inscriptions</span>
                </a>
            </li>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $admin['nom'] ?> <?= $admin['prenom'] ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../frontOffice/logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Déconnexion
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Main Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Activités Sportives et Inscriptions</h1>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#ajouterActiviteModal">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Nouvelle Activité
                        </button>
                    </div>
                    <!-- Include jsPDF and jsPDF-AutoTable -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>

                    <!-- Export Button -->
                    <button id="exportPDF" class="btn btn-success mb-3">💾📄 Exporter les activités en PDF</button>

                    <script>
                        document.getElementById('exportPDF').addEventListener('click', function() {
                            const {
                                jsPDF
                            } = window.jspdf;
                            const doc = new jsPDF();

                            // Title
                            doc.setFontSize(16);
                            doc.text('Liste des Activités', 14, 15);

                            // Extract table data
                            const tableData = [];
                            const rows = document.querySelectorAll('#activitesTable tbody tr');

                            rows.forEach(row => {
                                const cells = row.querySelectorAll('td');

                                if (cells.length >= 8) { // Excluding the Actions column
                                    const nom = cells[0].textContent.trim();
                                    const description = cells[1].textContent.trim();
                                    const dateDebut = cells[2].textContent.trim();
                                    const dateFin = cells[3].textContent.trim();
                                    const lieu = cells[4].textContent.trim();
                                    const capacite = cells[5].textContent.trim();
                                    const niveau = cells[6].textContent.trim();
                                    const statut = cells[7].textContent.trim();

                                    tableData.push([nom, description, dateDebut, dateFin, lieu, capacite, niveau, statut]);
                                }
                            });

                            // Generate PDF table
                            doc.autoTable({
                                startY: 25,
                                head: [
                                    ['Nom', 'Description', 'Date début', 'Date fin', 'Lieu', 'Capacité', 'Niveau', 'Statut']
                                ],
                                body: tableData,
                                styles: {
                                    fontSize: 8,
                                    cellPadding: 3
                                },
                                headStyles: {
                                    fillColor: [41, 128, 185],
                                    textColor: 255,
                                    fontStyle: 'bold'
                                }
                            });

                            // Save the file
                            doc.save('liste_activites.pdf');
                        });
                    </script>

                    <!-- Tableau des activités -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Liste des activités</h6>


                        </div>
                        <div class="card-body">
                            <div class="mb-3">

                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="activitesTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Lieu</th>
                                            <th>Capacité</th>
                                            <th>Niveau</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activites as $activite): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($activite['nom_activite']) ?></td>
                                                <td><?= htmlspecialchars($activite['description']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($activite['date_debut'])) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($activite['date_fin'])) ?></td>
                                                <td><?= htmlspecialchars($activite['lieu']) ?></td>
                                                <td><?= $activite['capacite_max'] ?></td>
                                                <td><?= htmlspecialchars($activite['niveau']) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $activite['statut'] == 'active' ? 'success' : 'secondary' ?>">
                                                        <?= $activite['statut'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary modifier-activite"
                                                        data-id="<?= $activite['id_activite'] ?>"
                                                        data-nom="<?= htmlspecialchars($activite['nom_activite']) ?>"
                                                        data-description="<?= htmlspecialchars($activite['description']) ?>"
                                                        data-datedebut="<?= $activite['date_debut'] ?>"
                                                        data-datefin="<?= $activite['date_fin'] ?>"
                                                        data-lieu="<?= htmlspecialchars($activite['lieu']) ?>"
                                                        data-capacite="<?= $activite['capacite_max'] ?>"
                                                        data-niveau="<?= htmlspecialchars($activite['niveau']) ?>"
                                                        data-statut="<?= $activite['statut'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger supprimer-activite"
                                                        data-id="<?= $activite['id_activite'] ?>"
                                                        data-nom="<?= htmlspecialchars($activite['nom_activite']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button id="exportPDFInscriptions" class="btn btn-success mb-3">💾📄 Exporter les inscriptions en PDF</button>

                    <!-- Tableau des inscriptions -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Liste des inscriptions</h6>
                            <div class="dropdown no-arrow">

                            </div>
                        </div>

                        <!-- Include jsPDF and jsPDF-AutoTable -->
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>

                        <!-- Export Button -->

                        <script>
                            document.getElementById('exportPDFInscriptions').addEventListener('click', function() {
                                const {
                                    jsPDF
                                } = window.jspdf;
                                const doc = new jsPDF();

                                // Title
                                doc.setFontSize(16);
                                doc.text('Liste des Inscriptions', 14, 15);

                                // Extract table data
                                const tableData = [];
                                const rows = document.querySelectorAll('#inscriptionsTable tbody tr');

                                rows.forEach(row => {
                                    const cells = row.querySelectorAll('td');

                                    if (cells.length >= 7) { // Ignore the Actions column
                                        const id = cells[0].textContent.trim();
                                        const activite = cells[1].textContent.trim();
                                        const participant = cells[2].textContent.trim();
                                        const email = cells[3].textContent.trim();
                                        const dateInscription = cells[4].textContent.trim();
                                        const statut = cells[5].textContent.trim();
                                        const commentaire = cells[6].textContent.trim();

                                        tableData.push([id, activite, participant, email, dateInscription, statut, commentaire]);
                                    }
                                });

                                // Generate PDF table
                                doc.autoTable({
                                    startY: 25,
                                    head: [
                                        ['ID', 'Activité', 'Participant', 'Email', "Date d'inscription", 'Statut', 'Commentaire']
                                    ],
                                    body: tableData,
                                    styles: {
                                        fontSize: 8,
                                        cellPadding: 3
                                    },
                                    headStyles: {
                                        fillColor: [41, 128, 185],
                                        textColor: 255,
                                        fontStyle: 'bold'
                                    }
                                });

                                // Save the file
                                doc.save('liste_inscriptions.pdf');
                            });
                        </script>

                        <div class="card-body">
                            <div class="mb-3">

                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="inscriptionsTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Activité</th>
                                            <th>Participant</th>
                                            <th>Email</th>
                                            <th>Date d'inscription</th>
                                            <th>Commentaire</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inscriptions as $inscription): ?>
                                            <tr>
                                                <td><?= $inscription['id_inscription'] ?></td>
                                                <td><?= htmlspecialchars($inscription['nom_activite']) ?></td>
                                                <td><?= htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']) ?></td>
                                                <td><?= htmlspecialchars($inscription['email']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($inscription['date_inscription'])) ?></td>

                                                <td><?= htmlspecialchars($inscription['commentaire'] ?? '') ?></td>
                                                <td>

                                                    <button class="btn btn-sm btn-danger supprimer-inscription"
                                                        data-id="<?= $inscription['id_inscription'] ?>"
                                                        data-activite="<?= htmlspecialchars($inscription['nom_activite']) ?>"
                                                        data-participant="<?= htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']) ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

    <!-- Modal Ajouter Activité -->
    <div class="modal fade" id="ajouterActiviteModal" tabindex="-1" role="dialog" aria-labelledby="ajouterActiviteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajouterActiviteModalLabel">Ajouter une nouvelle activité</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="" id="formAjouterActivite">
                    <div class="modal-body">
                        <input type="hidden" name="ajouter_activite" value="1">
                        <div class="form-group">
                            <label for="nom_activite">Nom de l'activité *</label>
                            <input type="text" class="form-control" id="nom_activite" name="nom_activite">
                            <div class="invalid-feedback" id="error-nom_activite"></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="error-description"></div>
                        </div>
                        <div class="form-group">
                            <label for="date_debut">Date de début *</label>
                            <input type="datetime-local" class="form-control" id="date_debut" name="date_debut">
                            <div class="invalid-feedback" id="error-date_debut"></div>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date de fin *</label>
                            <input type="datetime-local" class="form-control" id="date_fin" name="date_fin">
                            <div class="invalid-feedback" id="error-date_fin"></div>
                        </div>
                        <div class="form-group">
                            <label for="lieu">Lieu *</label>
                            <input type="text" class="form-control" id="lieu" name="lieu">
                            <div class="invalid-feedback" id="error-lieu"></div>
                        </div>
                        <div class="form-group">
                            <label for="capacite_max">Capacité maximale *</label>
                            <input type="number" class="form-control" id="capacite_max" name="capacite_max" min="1">
                            <div class="invalid-feedback" id="error-capacite_max"></div>
                        </div>
                        <div class="form-group">
                            <label for="niveau">Niveau *</label>
                            <select class="form-control" id="niveau" name="niveau">
                                <option value="">Sélectionnez un niveau</option>
                                <option value="Débutant">Débutant</option>
                                <option value="Intermédiaire">Intermédiaire</option>
                                <option value="Avancé">Avancé</option>
                            </select>
                            <div class="invalid-feedback" id="error-niveau"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modifier Activité -->
    <div class="modal fade" id="modifierActiviteModal" tabindex="-1" role="dialog" aria-labelledby="modifierActiviteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifierActiviteModalLabel">Modifier l'activité</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="" id="formModifierActivite">
                    <div class="modal-body">
                        <input type="hidden" name="modifier_activite" value="1">
                        <input type="hidden" id="id_activite" name="id_activite">
                        <div class="form-group">
                            <label for="modif_nom_activite">Nom de l'activité *</label>
                            <input type="text" class="form-control" id="modif_nom_activite" name="nom_activite">
                            <div class="invalid-feedback" id="error-modif_nom_activite"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_description">Description *</label>
                            <textarea class="form-control" id="modif_description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="error-modif_description"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_date_debut">Date de début *</label>
                            <input type="datetime-local" class="form-control" id="modif_date_debut" name="date_debut">
                            <div class="invalid-feedback" id="error-modif_date_debut"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_date_fin">Date de fin *</label>
                            <input type="datetime-local" class="form-control" id="modif_date_fin" name="date_fin">
                            <div class="invalid-feedback" id="error-modif_date_fin"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_lieu">Lieu *</label>
                            <input type="text" class="form-control" id="modif_lieu" name="lieu">
                            <div class="invalid-feedback" id="error-modif_lieu"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_capacite_max">Capacité maximale *</label>
                            <input type="number" class="form-control" id="modif_capacite_max" name="capacite_max" min="1">
                            <div class="invalid-feedback" id="error-modif_capacite_max"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_niveau">Niveau *</label>
                            <select class="form-control" id="modif_niveau" name="niveau">
                                <option value="">Sélectionnez un niveau</option>
                                <option value="Débutant">Débutant</option>
                                <option value="Intermédiaire">Intermédiaire</option>
                                <option value="Avancé">Avancé</option>
                            </select>
                            <div class="invalid-feedback" id="error-modif_niveau"></div>
                        </div>
                        <div class="form-group">
                            <label for="modif_statut">Statut *</label>
                            <select class="form-control" id="modif_statut" name="statut">
                                <option value="">Sélectionnez un statut</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback" id="error-modif_statut"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modifier Statut Inscription -->
    <div class="modal fade" id="modifierStatutInscriptionModal" tabindex="-1" role="dialog" aria-labelledby="modifierStatutInscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifierStatutInscriptionModalLabel">Modifier le statut de l'inscription</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables JS pour la recherche et le filtrage -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

    <script>
$(document).ready(function() {

    // --- Affichage des alertes SweetAlert (si session alert existe) ---
    <?php if (isset($_SESSION['alert'])): ?>
        Swal.fire({
            icon: '<?= $_SESSION['alert']['type'] ?>',
            title: '<?= $_SESSION['alert']['type'] === 'success' ? 'Succès' : 'Erreur' ?>',
            text: '<?= $_SESSION['alert']['message'] ?>',
            timer: 3000,
            showConfirmButton: false
        });
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>


    // --- Fonctions utilitaires de validation ---
    function validateRequired(value, fieldName) {
        return (!value || value.trim() === '') ? 'Le champ ' + fieldName + ' est requis' : '';
    }

    function validateDateDebut(dateDebut) {
        const now = new Date();
        const selectedDate = new Date(dateDebut);
        return (selectedDate <= now) ? 'La date de début doit être dans le futur' : '';
    }

    function validateDateFin(dateDebut, dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        if (fin <= debut) return 'La date de fin doit être après la date de début';
        const diffHours = (fin - debut) / (1000 * 60 * 60);
        return (diffHours < 2) ? 'La durée minimale doit être de 2 heures' : '';
    }

    function validateNumber(value, fieldName, min = 1) {
        return (!value || isNaN(value) || value < min)
            ? 'Le champ ' + fieldName + ' doit être un nombre supérieur ou égal à ' + min
            : '';
    }

    function showError(inputId, errorId, message) {
        $('#' + inputId).addClass('is-invalid');
        $('#' + errorId).text(message);
    }

    function clearError(inputId, errorId) {
        $('#' + inputId).removeClass('is-invalid');
        $('#' + errorId).text('');
    }

    function adjustEndDate(startDateInput, endDateInput) {
        const startDate = new Date(startDateInput.val());
        if (!isNaN(startDate.getTime())) {
            const endDate = new Date(startDate.getTime() + 2 * 60 * 60 * 1000);
            endDateInput.val(endDate.toISOString().slice(0, 16));
            clearError(endDateInput.attr('id'), 'error-' + endDateInput.attr('id'));
        }
    }

    // --- Ajustement automatique de la date de fin ---
    $('#date_debut').on('change', function() { adjustEndDate($(this), $('#date_fin')); });
    $('#modif_date_debut').on('change', function() { adjustEndDate($(this), $('#modif_date_fin')); });


    // --- Validation formulaire d'ajout d'activité ---
    $('#formAjouterActivite').on('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        const fields = {
            nomActivite: $('#nom_activite').val(),
            description: $('#description').val(),
            dateDebut: $('#date_debut').val(),
            dateFin: $('#date_fin').val(),
            lieu: $('#lieu').val(),
            capaciteMax: $('#capacite_max').val(),
            niveau: $('#niveau').val()
        };



$('#formModifierStatutInscription').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'modifier_statut.php', // fichier dédié
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            try {
                let res = JSON.parse(response);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Erreur', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Erreur inattendue', response, 'error');
            }
        },
        error: function() {
            Swal.fire('Erreur', 'Erreur de communication avec le serveur.', 'error');
        }
    });
});




        clearError('nom_activite', 'error-nom_activite');
        clearError('description', 'error-description');
        clearError('date_debut', 'error-date_debut');
        clearError('date_fin', 'error-date_fin');
        clearError('lieu', 'error-lieu');
        clearError('capacite_max', 'error-capacite_max');
        clearError('niveau', 'error-niveau');

        let error;
        error = validateRequired(fields.nomActivite, "nom de l'activité");
        if (error) { showError('nom_activite', 'error-nom_activite', error); isValid = false; }

        error = validateRequired(fields.description, 'description');
        if (error) { showError('description', 'error-description', error); isValid = false; }

        error = validateRequired(fields.dateDebut, 'date de début');
        if (error) { showError('date_debut', 'error-date_debut', error); isValid = false; }
        else {
            error = validateDateDebut(fields.dateDebut);
            if (error) { showError('date_debut', 'error-date_debut', error); isValid = false; }
        }

        error = validateRequired(fields.dateFin, 'date de fin');
        if (error) { showError('date_fin', 'error-date_fin', error); isValid = false; }
        else if (fields.dateDebut) {
            error = validateDateFin(fields.dateDebut, fields.dateFin);
            if (error) { showError('date_fin', 'error-date_fin', error); isValid = false; }
        }

        error = validateRequired(fields.lieu, 'lieu');
        if (error) { showError('lieu', 'error-lieu', error); isValid = false; }

        error = validateRequired(fields.capaciteMax, 'capacité maximale');
        if (error) { showError('capacite_max', 'error-capacite_max', error); isValid = false; }
        else {
            error = validateNumber(fields.capaciteMax, 'capacité maximale');
            if (error) { showError('capacite_max', 'error-capacite_max', error); isValid = false; }
        }

        error = validateRequired(fields.niveau, 'niveau');
        if (error) { showError('niveau', 'error-niveau', error); isValid = false; }

        if (isValid) this.submit();
    });


    // --- Validation formulaire de modification d'activité ---
    $('#formModifierActivite').on('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        const fields = {
            nomActivite: $('#modif_nom_activite').val(),
            description: $('#modif_description').val(),
            dateDebut: $('#modif_date_debut').val(),
            dateFin: $('#modif_date_fin').val(),
            lieu: $('#modif_lieu').val(),
            capaciteMax: $('#modif_capacite_max').val(),
            niveau: $('#modif_niveau').val(),
            statut: $('#modif_statut').val()
        };

        Object.keys(fields).forEach(id => clearError('modif_' + id, 'error-modif_' + id));

        let error;
        error = validateRequired(fields.nomActivite, "nom de l'activité");
        if (error) { showError('modif_nom_activite', 'error-modif_nom_activite', error); isValid = false; }

        error = validateRequired(fields.description, 'description');
        if (error) { showError('modif_description', 'error-modif_description', error); isValid = false; }

        error = validateRequired(fields.dateDebut, 'date de début');
        if (error) { showError('modif_date_debut', 'error-modif_date_debut', error); isValid = false; }
        else {
            error = validateDateDebut(fields.dateDebut);
            if (error) { showError('modif_date_debut', 'error-modif_date_debut', error); isValid = false; }
        }

        error = validateRequired(fields.dateFin, 'date de fin');
        if (error) { showError('modif_date_fin', 'error-modif_date_fin', error); isValid = false; }
        else if (fields.dateDebut) {
            error = validateDateFin(fields.dateDebut, fields.dateFin);
            if (error) { showError('modif_date_fin', 'error-modif_date_fin', error); isValid = false; }
        }

        error = validateRequired(fields.lieu, 'lieu');
        if (error) { showError('modif_lieu', 'error-modif_lieu', error); isValid = false; }

        error = validateRequired(fields.capaciteMax, 'capacité maximale');
        if (error) { showError('modif_capacite_max', 'error-modif_capacite_max', error); isValid = false; }
        else {
            error = validateNumber(fields.capaciteMax, 'capacité maximale');
            if (error) { showError('modif_capacite_max', 'error-modif_capacite_max', error); isValid = false; }
        }

        error = validateRequired(fields.niveau, 'niveau');
        if (error) { showError('modif_niveau', 'error-modif_niveau', error); isValid = false; }

        error = validateRequired(fields.statut, 'statut');
        if (error) { showError('modif_statut', 'error-modif_statut', error); isValid = false; }

        if (isValid) this.submit();
    });


    // --- Gestion des modals pour les activités ---
    $('.modifier-activite').click(function() {
        const dateToInput = (dateString) => new Date(dateString).toISOString().slice(0, 16);

        $('#id_activite').val($(this).data('id'));
        $('#modif_nom_activite').val($(this).data('nom'));
        $('#modif_description').val($(this).data('description'));
        $('#modif_date_debut').val(dateToInput($(this).data('datedebut')));
        $('#modif_date_fin').val(dateToInput($(this).data('datefin')));
        $('#modif_lieu').val($(this).data('lieu'));
        $('#modif_capacite_max').val($(this).data('capacite'));
        $('#modif_niveau').val($(this).data('niveau'));
        $('#modif_statut').val($(this).data('statut'));

        $('#modifierActiviteModal').modal('show');
    });


    // --- Confirmation de suppression d'activité ---
    $('.supprimer-activite').click(function() {
        const id = $(this).data('id');
        const nom = $(this).data('nom');

        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: `Vous êtes sur le point de supprimer l'activité "${nom}". Cette action est irréversible !`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = 'entrainer.php?supprimer_activite=' + id;
            }
        });
    });


    // --- Gestion des inscriptions : modification de statut ---
    $(document).on('click', '.modifier-statut-inscription', function() {
        $('#id_inscription_statut').val($(this).data('id'));
        $('#nom_activite_statut').text($(this).data('activite'));
        $('#nom_participant_statut').text($(this).data('participant'));
        $('#nouveau_statut').val($(this).data('statut'));

        $('#modifierStatutInscriptionModal').modal('show');
    });

    $('#formModifierStatutInscription').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'modifier_statut.php', // adapter si nécessaire
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Statut modifié avec succès.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Erreur', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Erreur inattendue', response, 'error');
                }
            },
            error: function() {
                Swal.fire('Erreur', 'Erreur de communication avec le serveur.', 'error');
            }
        });
    });


    // --- Confirmation de suppression d'inscription ---
    $('.supprimer-inscription').click(function() {
        const id = $(this).data('id');
        const activite = $(this).data('activite');
        const participant = $(this).data('participant');

        Swal.fire({
            title: 'Êtes-vous sûr ?',
            html: `Vous êtes sur le point de supprimer l'inscription de <strong>${participant}</strong> à l'activité <strong>"${activite}"</strong>.<br><br>Cette action est irréversible !`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = 'entrainer.php?supprimer_inscription=' + id;
            }
        });
    });


    // --- Nettoyage des erreurs quand les modals se ferment ---
    $('#ajouterActiviteModal, #modifierActiviteModal, #modifierStatutInscriptionModal').on('hidden.bs.modal', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

});
</script>

</body>

</html>