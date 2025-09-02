<?php
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/inscription.php';

class InscriptionC
{
    // --- CREATE ---
    function ajouterInscription(Inscription $inscription)
    {
        $sql = "INSERT INTO inscriptions 
                (id_activite, id_utilisateur, date_inscription, statut, commentaire) 
                VALUES 
                (:id_activite, :id_utilisateur, :date_inscription, :statut, :commentaire)";
        
        $db = config::getConnexion();
        try {
            // Vérifier si l'utilisateur n'est pas déjà inscrit à cette activité
            if ($this->verifierInscriptionExistante($inscription->getIdActivite(), $inscription->getIdUtilisateur())) {
                throw new Exception("Vous êtes déjà inscrit à cette activité.");
            }

            // Vérifier la capacité disponible
            if (!$this->verifierCapaciteDisponible($inscription->getIdActivite())) {
                throw new Exception("Cette activité a atteint sa capacité maximale.");
            }

            $query = $db->prepare($sql);
            $query->execute([
                'id_activite' => $inscription->getIdActivite(),
                'id_utilisateur' => $inscription->getIdUtilisateur(),
                'date_inscription' => $inscription->getDateInscription() ? $inscription->getDateInscription()->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                'statut' => $inscription->getStatut(),
                'commentaire' => $inscription->getCommentaire()
            ]);

            return $db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }

    // --- READ (Afficher toutes les inscriptions) ---
    function afficherInscriptions()
    {
        $sql = "SELECT i.*, a.nom_activite, u.nom, u.prenom, u.email 
                FROM inscriptions i 
                JOIN activite_sportive a ON i.id_activite = a.id_activite 
                LEFT JOIN utilisateur u ON i.id_utilisateur = u.id 
                ORDER BY i.date_inscription DESC";
        
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // --- READ (Récupérer les inscriptions d'un utilisateur) ---
    function afficherInscriptionsUtilisateur($id_utilisateur)
    {
        $sql = "SELECT i.*, a.nom_activite, a.description, a.date_debut, a.date_fin, a.lieu, a.niveau, a.statut as statut_activite
                FROM  inscriptions i 
                JOIN activite_sportive a ON i.id_activite = a.id_activite 
                WHERE i.id_utilisateur = :id_utilisateur 
                ORDER BY i.date_inscription DESC";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_utilisateur' => $id_utilisateur]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // --- READ (Récupérer les inscriptions d'une activité) ---
    function afficherInscriptionsActivite($id_activite)
    {
        $sql = "SELECT i.*, u.nom, u.prenom, u.email, u.telephone 
                FROM inscriptions i 
                LEFT JOIN utilisateur u ON i.id_utilisateur = u.id 
                WHERE i.id_activite = :id_activite 
                ORDER BY i.date_inscription ASC";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_activite' => $id_activite]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // --- READ (Récupérer une inscription par ID) ---
    function recupererInscription($id)
    {
        $sql = "SELECT i.*, a.nom_activite, u.nom, u.prenom, u.email 
                FROM inscriptions i 
                JOIN activite_sportive a ON i.id_activite = a.id_activite 
                LEFT JOIN utilisateur u ON i.id_utilisateur = u.id 
                WHERE i.id_inscription = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // --- UPDATE ---
    function modifierInscription(Inscription $inscription, $id)
    {
        $sql = "UPDATE inscriptions 
                SET id_activite = :id_activite, 
                    id_utilisateur = :id_utilisateur, 
                    date_inscription = :date_inscription, 
                    statut = :statut, 
                    commentaire = :commentaire
                WHERE id_inscription = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_activite' => $inscription->getIdActivite(),
                'id_utilisateur' => $inscription->getIdUtilisateur(),
                'date_inscription' => $inscription->getDateInscription() ? $inscription->getDateInscription()->format('Y-m-d H:i:s') : null,
                'statut' => $inscription->getStatut(),
                'commentaire' => $inscription->getCommentaire(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
        }
    }


    // --- DELETE ---
    function supprimerInscription($id)
    {
        $sql = "DELETE FROM inscriptions WHERE id_inscription = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // --- DELETE (Désinscrire un utilisateur d'une activité) ---
    function desinscrireUtilisateur($id_activite, $id_utilisateur)
    {
        $sql = "DELETE FROM inscriptions WHERE id_activite = :id_activite AND id_utilisateur = :id_utilisateur";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_activite' => $id_activite,
                'id_utilisateur' => $id_utilisateur
            ]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la désinscription: ' . $e->getMessage());
        }
    }

    // --- UTILITAIRES ---

    // Vérifier si un utilisateur est déjà inscrit à une activité
    function verifierInscriptionExistante($id_activite, $id_utilisateur)
    {
        $sql = "SELECT COUNT(*) FROM inscriptions WHERE id_activite = :id_activite AND id_utilisateur = :id_utilisateur";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_activite' => $id_activite,
                'id_utilisateur' => $id_utilisateur
            ]);
            return $query->fetchColumn() > 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Vérifier la capacité disponible d'une activité
    function verifierCapaciteDisponible($id_activite)
    {
        $sql = "SELECT a.capacite_max, COUNT(i.id_inscription) as inscriptions_actuelles 
                FROM activite_sportive a 
                LEFT JOIN inscriptions i ON a.id_activite = i.id_activite AND i.statut IN ('confirmee', 'en_attente')
                WHERE a.id_activite = :id_activite 
                GROUP BY a.id_activite, a.capacite_max";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_activite' => $id_activite]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false; // Activité non trouvée
            }
            
            return $result['inscriptions_actuelles'] < $result['capacite_max'];
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Compter les inscriptions par statut pour une activité
    function compterInscriptionsParStatut($id_activite)
    {
        $sql = "SELECT statut, COUNT(*) as nombre 
                FROM inscriptions 
                WHERE id_activite = :id_activite 
                GROUP BY statut";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_activite' => $id_activite]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Obtenir le nombre de places restantes pour une activité
    function obtenirPlacesRestantes($id_activite)
    {
        $sql = "SELECT a.capacite_max - COUNT(i.id_inscription) as places_restantes 
                FROM activite_sportive a 
                LEFT JOIN inscriptions i ON a.id_activite = i.id_activite AND i.statut IN ('confirmee', 'en_attente')
                WHERE a.id_activite = :id_activite 
                GROUP BY a.id_activite, a.capacite_max";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_activite' => $id_activite]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['places_restantes'] : 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Obtenir les statistiques générales des inscriptions
    function obtenirStatistiquesInscriptions()
    {
        $sql = "SELECT 
                    COUNT(*) as total_inscriptions,
                    COUNT(CASE WHEN statut = 'confirmee' THEN 1 END) as inscriptions_confirmees,
                    COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as inscriptions_en_attente,
                    COUNT(CASE WHEN statut = 'annulee' THEN 1 END) as inscriptions_annulees,
                    COUNT(DISTINCT id_utilisateur) as utilisateurs_uniques,
                    COUNT(DISTINCT id_activite) as activites_avec_inscriptions
                FROM inscriptions";
        
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Obtenir les inscriptions récentes (dernières 24h, 7 jours, etc.)
    function obtenirInscriptionsRecentes($periode = '24h')
    {
        $interval = match($periode) {
            '24h' => 'INTERVAL 1 DAY',
            '7d' => 'INTERVAL 7 DAY',
            '30d' => 'INTERVAL 30 DAY',
            default => 'INTERVAL 1 DAY'
        };

        $sql = "SELECT i.*, a.nom_activite, u.nom, u.prenom, u.email 
                FROM inscriptions i 
                JOIN activite_sportive a ON i.id_activite = a.id_activite 
                LEFT JOIN utilisateur u ON i.id_utilisateur = u.id 
                WHERE i.date_inscription >= DATE_SUB(NOW(), $interval)
                ORDER BY i.date_inscription DESC";
        
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Obtenir les activités les plus populaires
    function obtenirActivitesPopulaires($limite = 5)
    {
        $sql = "SELECT a.*, COUNT(i.id_inscription) as nombre_inscriptions,
                       a.capacite_max - COUNT(i.id_inscription) as places_restantes
                FROM activite_sportive a 
                LEFT JOIN inscriptions i ON a.id_activite = i.id_activite AND i.statut IN ('confirmee', 'en_attente')
                WHERE a.statut = 'active'
                GROUP BY a.id_activite 
                ORDER BY nombre_inscriptions DESC 
                LIMIT :limite";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Valider une inscription (changer le statut de 'en_attente' à 'confirmee')
    function validerInscription($id_inscription)
    {
        return $this->modifierStatutInscription($id_inscription, 'confirmee');
    }

    // Annuler une inscription
    function annulerInscription($id_inscription)
    {
        return $this->modifierStatutInscription($id_inscription, 'annulee');
    }

    // Mettre une inscription en liste d'attente
    function mettreEnListeAttente($id_inscription)
    {
        return $this->modifierStatutInscription($id_inscription, 'liste_attente');
    }

    // Rechercher des inscriptions avec des critères
    function rechercherInscriptions($criteres = [])
    {
        $sql = "SELECT i.*, a.nom_activite, u.nom, u.prenom, u.email 
                FROM inscriptions i 
                JOIN activite_sportive a ON i.id_activite = a.id_activite 
                LEFT JOIN utilisateur u ON i.id_utilisateur = u.id 
                WHERE 1=1";
        
        $params = [];

        // Filtrer par statut
        if (!empty($criteres['statut'])) {
            $sql .= " AND i.statut = :statut";
            $params['statut'] = $criteres['statut'];
        }

        // Filtrer par activité
        if (!empty($criteres['id_activite'])) {
            $sql .= " AND i.id_activite = :id_activite";
            $params['id_activite'] = $criteres['id_activite'];
        }

        // Filtrer par utilisateur
        if (!empty($criteres['id_utilisateur'])) {
            $sql .= " AND i.id_utilisateur = :id_utilisateur";
            $params['id_utilisateur'] = $criteres['id_utilisateur'];
        }

        // Filtrer par période
        if (!empty($criteres['date_debut']) && !empty($criteres['date_fin'])) {
            $sql .= " AND i.date_inscription BETWEEN :date_debut AND :date_fin";
            $params['date_debut'] = $criteres['date_debut'];
            $params['date_fin'] = $criteres['date_fin'];
        }

        // Recherche textuelle dans le nom d'activité ou nom d'utilisateur
        if (!empty($criteres['recherche'])) {
            $sql .= " AND (a.nom_activite LIKE :recherche OR u.nom LIKE :recherche OR u.prenom LIKE :recherche)";
            $params['recherche'] = '%' . $criteres['recherche'] . '%';
        }

        $sql .= " ORDER BY i.date_inscription DESC";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Obtenir un rapport détaillé des inscriptions par activité
    function obtenirRapportInscriptions()
    {
        $sql = "SELECT 
                    a.id_activite,
                    a.nom_activite,
                    a.capacite_max,
                    a.date_debut,
                    a.date_fin,
                    a.statut as statut_activite,
                    COUNT(i.id_inscription) as total_inscriptions,
                    COUNT(CASE WHEN i.statut = 'confirmee' THEN 1 END) as inscriptions_confirmees,
                    COUNT(CASE WHEN i.statut = 'en_attente' THEN 1 END) as inscriptions_en_attente,
                    COUNT(CASE WHEN i.statut = 'annulee' THEN 1 END) as inscriptions_annulees,
                    COUNT(CASE WHEN i.statut = 'liste_attente' THEN 1 END) as inscriptions_liste_attente,
                    (a.capacite_max - COUNT(CASE WHEN i.statut IN ('confirmee', 'en_attente') THEN 1 END)) as places_restantes,
                    ROUND((COUNT(CASE WHEN i.statut IN ('confirmee', 'en_attente') THEN 1 END) * 100.0 / a.capacite_max), 2) as taux_remplissage
                FROM activite_sportive a 
                LEFT JOIN inscription i ON a.id_activite = i.id_activite 
                GROUP BY a.id_activite 
                ORDER BY taux_remplissage DESC";
        
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Inscription automatique avec gestion de la liste d'attente
public function inscrireAvecListeAttente(int $id_activite, int $id_utilisateur, string $commentaire = ''): array {
        $db = config::getConnexion();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Exemple de requête
            $sql = "INSERT INTO inscriptions (id_activite, id_utilisateur, commentaire)
                    VALUES (:id_activite, :id_utilisateur, :commentaire)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_activite' => $id_activite,
                ':id_utilisateur' => $id_utilisateur,
                ':commentaire' => $commentaire
            ]);

            return [
                'success' => true,
                'message' => 'Inscription réussie',
                'statut' => 'confirmé'
            ];

        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'inscription : ' . $e->getMessage()
            ];
        }
    }
public function modifierStatutInscription(int $id_inscription, string $nouveau_statut): array {
    $db = config::getConnexion();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        // 1. Vérifier que le statut est valide
        $statutsAutorises = ['confirmee', 'en_attente', 'annulee', 'liste_attente'];
        if (!in_array($nouveau_statut, $statutsAutorises, true)) {
            return [
                'success' => false,
                'message' => 'Statut non valide.'
            ];
        }

        // 2. Vérifier que l'inscription existe
        $sqlCheck = "SELECT COUNT(*) FROM inscriptions WHERE id_inscription = :id";
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $id_inscription]);
        if ($stmtCheck->fetchColumn() == 0) {
            return [
                'success' => false,
                'message' => 'Inscription introuvable.'
            ];
        }

        // 3. Mettre à jour le statut
        $sqlUpdate = "UPDATE inscriptions 
                      SET statut = :statut
                      WHERE id_inscription = :id";
        $stmt = $db->prepare($sqlUpdate);
        $stmt->execute([
            ':statut' => $nouveau_statut,
            ':id' => $id_inscription
        ]);

        return [
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'nouveau_statut' => $nouveau_statut
        ];

    } catch (PDOException $e) {
        error_log("Erreur SQL: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erreur lors de la modification du statut : ' . $e->getMessage()
        ];
    }
}




}
?>