<?php

class Inscription
{
    private ?int $id_inscription;
    private int $id_activite;
    private int $id_utilisateur;
    private ?DateTime $date_inscription;
    private string $statut;
    private ?string $commentaire;

    public function __construct(
        ?int $id_inscription,
        int $id_activite,
        int $id_utilisateur,
        ?DateTime $date_inscription = null,
        string $statut = 'en_attente',
        ?string $commentaire = null
    ) {
        $this->id_inscription = $id_inscription;
        $this->id_activite = $id_activite;
        $this->id_utilisateur = $id_utilisateur;
        $this->date_inscription = $date_inscription;
        $this->statut = $statut;
        $this->commentaire = $commentaire;
    }

    // --- Getters ---
    public function getIdInscription(): ?int { return $this->id_inscription; }
    public function getIdActivite(): int { return $this->id_activite; }
    public function getIdUtilisateur(): int { return $this->id_utilisateur; }
    public function getDateInscription(): ?DateTime { return $this->date_inscription; }
    public function getStatut(): string { return $this->statut; }
    public function getCommentaire(): ?string { return $this->commentaire; }

    // --- Setters ---
    public function setIdActivite(int $id_activite): void { $this->id_activite = $id_activite; }
    public function setIdUtilisateur(int $id_utilisateur): void { $this->id_utilisateur = $id_utilisateur; }
    public function setDateInscription(?DateTime $date): void { $this->date_inscription = $date; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setCommentaire(?string $commentaire): void { $this->commentaire = $commentaire; }
}
