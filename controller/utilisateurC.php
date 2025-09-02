<?php
include_once __DIR__.'/../config.php';
require_once __DIR__.'/../model/utilisateur.php'; 

class UtilisateurC
{
    function ajouterUtilisateur($utilisateur)
    {
        $sql = "INSERT INTO utilisateur 
                (nom, prenom, email, mdp, adresse, telephone, role) 
                VALUES 
                (:nom, :prenom, :email, :mdp, :adresse, :telephone, :role)";
    
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'mdp' => $utilisateur->getMdp(),
                'adresse' => $utilisateur->getAdresse(),
                'telephone' => $utilisateur->getTelephone(),
                'role' => $utilisateur->getRole()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
    
    function afficherUtilisateurs()
    {
        $sql = "SELECT * FROM utilisateur";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    function supprimerUtilisateur($id)
    {
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    function recupererUtilisateur($id)
    {
        $sql = "SELECT * FROM utilisateur WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $utilisateur = $query->fetch(PDO::FETCH_ASSOC);
            return $utilisateur;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    function modifierUtilisateur($utilisateur, $id)
    {
        $sql = "UPDATE utilisateur 
                SET nom = :nom, prenom = :prenom, email = :email, 
                    mdp = :mdp, adresse = :adresse, telephone = :telephone, role = :role 
                WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        try {
            $req->execute([
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'mdp' => $utilisateur->getMdp(),
                'adresse' => $utilisateur->getAdresse(),
                'telephone' => $utilisateur->getTelephone(),
                'role' => $utilisateur->getRole(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }
    function login($email, $password)
{
    $sql = "SELECT * FROM utilisateur WHERE email = :email";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->bindValue(':email', $email);
        $query->execute();
        $userRow = $query->fetch(PDO::FETCH_ASSOC);

        if ($userRow) {
            echo "Plain password: " . $password . "<br>";
            echo "Hashed password from DB: " . $userRow['mdp'] . "<br>";

            if (password_verify($password, $userRow['mdp'])) {
                echo "Password matches!";
                return new Utilisateur(
                    $userRow['id'], 
                    $userRow['nom'], 
                    $userRow['prenom'],
                    $userRow['email'], 
                    $userRow['mdp'],
                    $userRow['adresse'],
                    $userRow['telephone'],
                    $userRow['role']
                );
            } else {
                echo "Password does not match!";
            }
        } else {
            echo "Email not found!";
        }
    } catch (Exception $e) {
        die('Erreur:' . $e->getMessage());
    }

    return null;
}

}
?>
