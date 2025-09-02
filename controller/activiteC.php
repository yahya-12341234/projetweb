<?php
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/avtivite.php';

class ActiviteSportiveC
{
    // --- CREATE ---
    function ajouterActivite(ActiviteSportive $activite)
    {
        $sql = "INSERT INTO activite_sportive 
                (nom_activite, description, date_debut, date_fin, lieu, capacite_max, niveau, id_entraineur, statut) 
                VALUES 
                (:nom_activite, :description, :date_debut, :date_fin, :lieu, :capacite_max, :niveau, :id_entraineur, :statut)";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom_activite' => $activite->getNomActivite(),
                'description' => $activite->getDescription(),
                'date_debut' => $activite->getDateDebut()->format('Y-m-d H:i:s'),
                'date_fin' => $activite->getDateFin()->format('Y-m-d H:i:s'),
                'lieu' => $activite->getLieu(),
                'capacite_max' => $activite->getCapaciteMax(),
                'niveau' => $activite->getNiveau(),
                'id_entraineur' => $activite->getIdEntraineur(),
                'statut' => $activite->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // --- READ (Afficher toutes les activités) ---
    function afficherActivites()
    {
        $sql = "SELECT * FROM activite_sportive";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // --- READ (Récupérer une activité par ID) ---
    function recupererActivite($id)
    {
        $sql = "SELECT * FROM activite_sportive WHERE id_activite = :id";
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
    function modifierActivite(ActiviteSportive $activite, $id)
    {
        $sql = "UPDATE activite_sportive 
                SET nom_activite = :nom_activite, 
                    description = :description, 
                    date_debut = :date_debut, 
                    date_fin = :date_fin, 
                    lieu = :lieu, 
                    capacite_max = :capacite_max, 
                    niveau = :niveau, 
                    id_entraineur = :id_entraineur, 
                    statut = :statut
                WHERE id_activite = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom_activite' => $activite->getNomActivite(),
                'description' => $activite->getDescription(),
                'date_debut' => $activite->getDateDebut()->format('Y-m-d H:i:s'),
                'date_fin' => $activite->getDateFin()->format('Y-m-d H:i:s'),
                'lieu' => $activite->getLieu(),
                'capacite_max' => $activite->getCapaciteMax(),
                'niveau' => $activite->getNiveau(),
                'id_entraineur' => $activite->getIdEntraineur(),
                'statut' => $activite->getStatut(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // --- DELETE ---
    function supprimerActivite($id)
    {
        $sql = "DELETE FROM activite_sportive WHERE id_activite = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
