<?php
include_once __DIR__ . '/../../controller/activiteC.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}

// Récupération des activités
$activiteC = new ActiviteSportiveC();
$activites = $activiteC->afficherActivites();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Activités - AgriCulture</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/apple-touch-icon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Marcellus:wght@400&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  
  <style>
    .activities {
      background-color: #f8f9fa;
      padding: 80px 0;
    }

    .activity-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .activity-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .activity-image {
      height: 200px;
      background: linear-gradient(45deg, #3ab54a, #2e8b3a);
      position: relative;
      overflow: hidden;
      border-radius: 20px 20px 0 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .activity-image::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('assets/img/pattern.png') center;
      opacity: 0.1;
    }

    .activity-image-icon {
      font-size: 3rem;
      color: white;
      z-index: 1;
    }

    .activity-status {
      position: absolute;
      top: 20px;
      right: 20px;
      padding: 8px 15px;
      border-radius: 30px;
      font-size: 0.8rem;
      font-weight: 600;
      z-index: 2;
      color: white;
    }

    .status-active {
      background-color: #28a745;
    }

    .status-inactive {
      background-color: #6c757d;
    }

    .activity-content {
      padding: 30px;
    }

    .activity-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #333;
      margin-bottom: 15px;
      word-wrap: break-word;
    }

    .activity-details {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }

    .detail-item {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .detail-icon {
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f0f8f1;
      border-radius: 10px;
      color: #3ab54a;
      font-size: 1.1rem;
    }

    .detail-info {
      flex: 1;
      min-width: 0;
    }

    .detail-text {
      font-size: 0.9rem;
      color: #333;
      font-weight: 500;
      word-wrap: break-word;
      display: block;
    }

    .detail-label {
      display: block;
      font-size: 0.75rem;
      color: #999;
      margin-bottom: 2px;
    }

    .activity-description {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 25px;
      border-left: 3px solid #3ab54a;
      padding-left: 15px;
      word-wrap: break-word;
      min-height: 50px;
    }

    .activity-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 20px;
      border-top: 1px solid #eee;
    }

    .capacity {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #666;
    }

    .btn-register {
      padding: 12px 28px;
      border-radius: 30px;
      background: linear-gradient(45deg, #3ab54a, #2e8b3a);
      color: white;
      border: none;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .btn-register:hover {
      background: linear-gradient(45deg, #2e8b3a, #236b2d);
      transform: translateX(5px);
    }

    .btn-register:disabled {
      background: #6c757d;
      cursor: not-allowed;
      transform: none;
    }

    .btn-closed {
      padding: 12px 28px;
      border-radius: 30px;
      background: #6c757d;
      color: white;
      border: none;
      font-weight: 600;
      cursor: not-allowed;
      font-size: 0.9rem;
    }

    .level-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .level-beginner {
      background-color: #e8f5e9;
      color: #2e7d32;
    }

    .level-intermediate {
      background-color: #e3f2fd;
      color: #1565c0;
    }

    .level-advanced {
      background-color: #fbe9e7;
      color: #d84315;
    }

    .error-data {
      color: #dc3545;
      font-style: italic;
    }

    .no-activities {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .no-activities i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 20px;
    }
  </style>

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center position-relative">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="AgriCulture" width="80" height="40">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <div class="w3-display-container">
        <img src="assets/img/apple-touch-icon.png" alt="Lights" width="80" height="40">
        <h6>Grow&Glow</h6>
        <div class="w3-display-topright w3-container"></div>
      </div>

    </div>
  </header>

  <main class="main">
    <!-- Activities Section -->
    <section id="activities" class="activities section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Nos Activités</h2>
        <p>Découvrez nos activités sportives et rejoignez-nous!</p>
      </div><!-- End Section Title -->
      
      <div class="container">
        <?php if (empty($activites)): ?>
          <div class="no-activities">
            <i class="bi bi-calendar-x"></i>
            <h3>Aucune activité disponible</h3>
            <p>Il n'y a actuellement aucune activité disponible. Revenez plus tard!</p>
          </div>
        <?php else: ?>
          <div class="row">
            <?php foreach ($activites as $activite): 
              // Nettoyer et valider les données
              $nom_activite = !empty($activite['nom_activite']) ? htmlspecialchars(trim($activite['nom_activite'])) : 'Activité sans nom';
              $lieu = !empty($activite['lieu']) ? htmlspecialchars(trim($activite['lieu'])) : 'Lieu non défini';
              $description = !empty($activite['description']) ? htmlspecialchars(trim($activite['description'])) : 'Aucune description disponible';
              $niveau = !empty($activite['niveau']) ? htmlspecialchars(trim($activite['niveau'])) : 'Non défini';
              $date_debut = !empty($activite['date_debut']) ? htmlspecialchars($activite['date_debut']) : 'Date non définie';
              $date_fin = !empty($activite['date_fin']) ? htmlspecialchars($activite['date_fin']) : 'Date non définie';
              $capacite_max = isset($activite['capacite_max']) && is_numeric($activite['capacite_max']) ? (int)$activite['capacite_max'] : 0;
              $statut = !empty($activite['statut']) ? strtolower(trim($activite['statut'])) : 'inactive';
              
              // Déterminer la classe pour le niveau
              $niveau_class = '';
              $niveau_lower = strtolower($niveau);
              if (strpos($niveau_lower, 'débutant') !== false || strpos($niveau_lower, 'beginner') !== false) {
                  $niveau_class = 'level-beginner';
              } elseif (strpos($niveau_lower, 'intermédiaire') !== false || strpos($niveau_lower, 'intermediate') !== false) {
                  $niveau_class = 'level-intermediate';
              } elseif (strpos($niveau_lower, 'avancé') !== false || strpos($niveau_lower, 'advanced') !== false) {
                  $niveau_class = 'level-advanced';
              } else {
                  $niveau_class = 'level-beginner';
              }

              // Déterminer l'icône selon le type d'activité
              $activity_icon = 'bi-trophy';
              if (strpos(strtolower($nom_activite), 'football') !== false) $activity_icon = 'bi-circle';
              elseif (strpos(strtolower($nom_activite), 'tennis') !== false) $activity_icon = 'bi-circle';
              elseif (strpos(strtolower($nom_activite), 'natation') !== false) $activity_icon = 'bi-water';
              elseif (strpos(strtolower($nom_activite), 'course') !== false) $activity_icon = 'bi-speedometer2';
            ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="activity-card">
                <div class="activity-image">
                  <i class="activity-image-icon bi <?php echo $activity_icon; ?>"></i>
                  <span class="activity-status <?php echo $statut === 'active' ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $statut === 'active' ? 'Active' : 'Inactive'; ?>
                  </span>
                </div>
                
                <div class="activity-content">
                  <h3 class="activity-title"><?php echo $nom_activite; ?></h3>
                  
                  <div class="activity-details">
                    <div class="detail-item">
                      <div class="detail-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                      </div>
                      <div class="detail-info">
                        <span class="detail-label">Lieu</span>
                        <span class="detail-text"><?php echo $lieu; ?></span>
                      </div>
                    </div>
                    
                    <div class="detail-item">
                      <div class="detail-icon">
                        <i class="bi bi-calendar-event"></i>
                      </div>
                      <div class="detail-info">
                        <span class="detail-label">Date début</span>
                        <span class="detail-text"><?php echo $date_debut; ?></span>
                      </div>
                    </div>
                    
                    <div class="detail-item">
                      <div class="detail-icon">
                        <i class="bi bi-bar-chart-fill"></i>
                      </div>
                      <div class="detail-info">
                        <span class="detail-label">Niveau</span>
                        <span class="level-badge <?php echo $niveau_class; ?>"><?php echo $niveau; ?></span>
                      </div>
                    </div>
                    
                    <div class="detail-item">
                      <div class="detail-icon">
                        <i class="bi bi-clock-fill"></i>
                      </div>
                      <div class="detail-info">
                        <span class="detail-label">Date fin</span>
                        <span class="detail-text"><?php echo $date_fin; ?></span>
                      </div>
                    </div>
                  </div>

                  <div class="activity-description">
                    <?php echo nl2br($description); ?>
                  </div>
                  
                  <div class="activity-footer">
                    <div class="capacity">
                      <i class="bi bi-people-fill"></i>
                      <span><?php echo $capacite_max; ?> places</span>
                    </div>
                    
                    <?php if ($statut === 'active'): ?>
                      <button type="button" class="btn-register" onclick="afficherPopupInscription(<?php echo $activite['id_activite']; ?>, '<?php echo addslashes($nom_activite); ?>')">
                        S'inscrire <i class="bi bi-arrow-right"></i>
                      </button>
                    <?php else: ?>
                      <button class="btn-closed" disabled>
                        Inscriptions fermées
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section><!-- /Activities Section -->
  </main>

  <footer id="footer" class="footer dark-background">
    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <!-- Footer content -->
        </div>
      </div>
    </div>

    <div class="copyright text-center">
      <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
        <div class="d-flex flex-column align-items-center align-items-lg-start">
          <div>
            © Copyright <strong><span>MyWebsite</span></strong>. All Rights Reserved
          </div>
          <div class="credits">
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
          </div>
        </div>

        <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
          <a href=""><i class="bi bi-twitter-x"></i></a>
          <a href=""><i class="bi bi-facebook"></i></a>
          <a href=""><i class="bi bi-instagram"></i></a>
          <a href=""><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    function afficherPopupInscription(idActivite, nomActivite) {
      Swal.fire({
        title: 'Inscription à l\'activité',
        html: `
          <div style="text-align: left; margin: 20px 0;">
            <h4 style="color: #3ab54a; margin-bottom: 15px;">
              <i class="bi bi-trophy" style="margin-right: 8px;"></i>
              ${nomActivite}
            </h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
              <p style="margin: 0; color: #666;">
                <i class="bi bi-info-circle" style="color: #3ab54a; margin-right: 8px;"></i>
                Confirmez-vous votre inscription à cette activité ?
              </p>
            </div>
            <div style="margin-bottom: 15px;">
              <label for="commentaire" style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">
                Commentaire (optionnel):
              </label>
              <textarea 
                id="commentaire" 
                class="swal2-textarea" 
                placeholder="Ajoutez un commentaire sur votre inscription..."
                style="width: 100%; min-height: 80px; border: 2px solid #e9ecef; border-radius: 8px; padding: 10px;"
              ></textarea>
            </div>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3ab54a',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="bi bi-check-circle"></i> Confirmer l\'inscription',
        cancelButtonText: '<i class="bi bi-x-circle"></i> Annuler',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        customClass: {
          popup: 'swal-wide',
          confirmButton: 'swal-btn-confirm',
          cancelButton: 'swal-btn-cancel'
        },
        preConfirm: () => {
          const commentaire = document.getElementById('commentaire').value.trim();
          
          // Validation optionnelle du commentaire
          if (commentaire.length > 500) {
            Swal.showValidationMessage('Le commentaire ne peut pas dépasser 500 caractères');
            return false;
          }
          
          // Envoyer la requête AJAX
          const formData = new FormData();
          formData.append('id_activite', idActivite);
          if (commentaire) {
            formData.append('commentaire', commentaire);
          }
          
          return fetch('inscrire.php', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              throw new Error(data.message || 'Une erreur s\'est produite');
            }
            return data;
          })
          .catch(error => {
            console.error('Erreur d\'inscription:', error);
            Swal.showValidationMessage(
              `<div style="color: #dc3545;">
                <i class="bi bi-exclamation-triangle"></i> 
                ${error.message || 'Une erreur s\'est produite lors de l\'inscription'}
              </div>`
            );
          });
        }
      }).then((result) => {
        if (result.isConfirmed && result.value) {
          // Déterminer le type de succès selon le statut
          const isWaitingList = result.value.statut === 'liste_attente';
          
          Swal.fire({
            title: isWaitingList ? 'Ajouté à la liste d\'attente!' : 'Inscription confirmée!',
            html: `
              <div style="text-align: center;">
                <div style="font-size: 4rem; color: ${isWaitingList ? '#ffc107' : '#28a745'}; margin-bottom: 15px;">
                  <i class="bi ${isWaitingList ? 'bi-clock-history' : 'bi-check-circle'}"></i>
                </div>
                <p style="font-size: 1.1rem; color: #333; margin-bottom: 10px;">
                  ${result.value.message}
                </p>
                ${isWaitingList ? 
                  '<p style="color: #666; font-size: 0.9rem;"><i class="bi bi-info-circle"></i> Vous serez notifié si une place se libère.</p>' : 
                  '<p style="color: #666; font-size: 0.9rem;"><i class="bi bi-calendar-check"></i> Votre place est réservée!</p>'
                }
              </div>
            `,
            icon: 'success',
            confirmButtonColor: '#3ab54a',
            confirmButtonText: '<i class="bi bi-arrow-clockwise"></i> Actualiser la page',
            allowOutsideClick: false
          }).then(() => {
            // Recharger la page pour mettre à jour les informations
            window.location.reload();
          });
        } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
          // Animation de annulation
          Swal.fire({
            title: 'Inscription annulée',
            html: `
              <div style="text-align: center;">
                <div style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;">
                  <i class="bi bi-x-circle"></i>
                </div>
                <p style="color: #666;">Votre inscription a été annulée.</p>
              </div>
            `,
            icon: 'info',
            confirmButtonColor: '#3ab54a',
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true
          });
        }
      });
    }

    // Fonction pour afficher les détails d'une activité
    function afficherDetailsActivite(activite) {
      Swal.fire({
        title: activite.nom,
        html: `
          <div style="text-align: left;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
              <div><strong>📍 Lieu:</strong> ${activite.lieu}</div>
              <div><strong>📅 Début:</strong> ${activite.date_debut}</div>
              <div><strong>📊 Niveau:</strong> ${activite.niveau}</div>
              <div><strong>⏰ Fin:</strong> ${activite.date_fin}</div>
            </div>
            <div style="border-left: 3px solid #3ab54a; padding-left: 15px; margin: 20px 0;">
              <strong>Description:</strong><br>
              ${activite.description}
            </div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
              <strong>👥 Capacité:</strong> ${activite.capacite_max} places<br>
              <strong>📋 Statut:</strong> <span style="color: ${activite.statut === 'active' ? '#28a745' : '#6c757d'}">${activite.statut === 'active' ? 'Active' : 'Inactive'}</span>
            </div>
          </div>
        `,
        width: 600,
        confirmButtonColor: '#3ab54a',
        confirmButtonText: 'Fermer'
      });
    }

    // Gestion d'erreur globale pour les requêtes AJAX
    window.addEventListener('unhandledrejection', function(event) {
      console.error('Erreur non gérée:', event.reason);
      Swal.fire({
        title: 'Erreur système',
        text: 'Une erreur inattendue s\'est produite. Veuillez réessayer.',
        icon: 'error',
        confirmButtonColor: '#3ab54a'
      });
    });

    // CSS personnalisé pour SweetAlert
    const style = document.createElement('style');
    style.textContent = `
      .swal-wide {
        width: 600px !important;
      }
      .swal-btn-confirm {
        font-weight: 600 !important;
        padding: 12px 24px !important;
      }
      .swal-btn-cancel {
        font-weight: 600 !important;
        padding: 12px 24px !important;
      }
      .swal2-textarea:focus {
        border-color: #3ab54a !important;
        box-shadow: 0 0 0 0.2rem rgba(58, 181, 74, 0.25) !important;
      }
    `;
    document.head.appendChild(style);
  </script>

</body>

</html>