<?php

class ActiviteSportive
{
    private ?int $id_activite;
    private string $nom_activite;
    private ?string $description;
    private DateTime $date_debut;
    private DateTime $date_fin;
    private string $lieu;
    private int $capacite_max;
    private string $niveau;
    private ?int $id_entraineur;
    private string $statut;
    private ?DateTime $date_creation;

    public function __construct(
        ?int $id_activite,
        string $nom_activite,
        ?string $description,
        DateTime $date_debut,
        DateTime $date_fin,
        string $lieu,
        int $capacite_max,
        string $niveau = 'Débutant',
        ?int $id_entraineur = null,
        string $statut = 'Active',
        ?DateTime $date_creation = null
    ) {
        $this->id_activite = $id_activite;
        $this->nom_activite = $nom_activite;
        $this->description = $description;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->lieu = $lieu;
        $this->capacite_max = $capacite_max;
        $this->niveau = $niveau;
        $this->id_entraineur = $id_entraineur;
        $this->statut = $statut;
        $this->date_creation = $date_creation;
    }

    // --- Getters ---
    public function getIdActivite(): ?int { return $this->id_activite; }
    public function getNomActivite(): string { return $this->nom_activite; }
    public function getDescription(): ?string { return $this->description; }
    public function getDateDebut(): DateTime { return $this->date_debut; }
    public function getDateFin(): DateTime { return $this->date_fin; }
    public function getLieu(): string { return $this->lieu; }
    public function getCapaciteMax(): int { return $this->capacite_max; }
    public function getNiveau(): string { return $this->niveau; }
    public function getIdEntraineur(): ?int { return $this->id_entraineur; }
    public function getStatut(): string { return $this->statut; }
    public function getDateCreation(): ?DateTime { return $this->date_creation; }

    // --- Setters ---
    public function setNomActivite(string $nom): void { $this->nom_activite = $nom; }
    public function setDescription(?string $desc): void { $this->description = $desc; }
    public function setDateDebut(DateTime $date): void { $this->date_debut = $date; }
    public function setDateFin(DateTime $date): void { $this->date_fin = $date; }
    public function setLieu(string $lieu): void { $this->lieu = $lieu; }
    public function setCapaciteMax(int $capacite): void { $this->capacite_max = $capacite; }
    public function setNiveau(string $niveau): void { $this->niveau = $niveau; }
    public function setIdEntraineur(?int $id): void { $this->id_entraineur = $id; }
    public function setStatut(string $statut): void { $this->statut = $statut; }
}
