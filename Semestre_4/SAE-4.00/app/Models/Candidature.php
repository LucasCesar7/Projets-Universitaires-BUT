<?php

namespace App\Models;

/**
 * Model representing an application in the Social Media Awards.
 */
class Candidature
{
    private ?int $id_candidature = null;
    private string $libelle;
    private string $plateforme;
    private string $url_contenu;
    private ?string $image = null;
    private string $argumentaire;
    private string $date_soumission;
    private string $statut = 'En attente';
    private int $id_compte;
    private int $id_categorie;
    private ?string $candidat_pseudonyme = null;
    private ?string $candidat_email = null;
    private ?string $categorie_nom = null;
    private ?string $edition_nom = null;

    /**
        * Candidature model constructor.
     *
        * @param array $data Data used to initialize the object.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->setIdCandidature($data['id_candidature'] ?? null);
            $this->setLibelle($data['libelle'] ?? '');
            $this->setPlateforme($data['plateforme'] ?? '');
            $this->setUrlContenu($data['url_contenu'] ?? '');
            $this->setImage($data['image'] ?? null);
            $this->setArgumentaire($data['argumentaire'] ?? '');
            $this->setDateSoumission($data['date_soumission'] ?? date('Y-m-d H:i:s'));
            $this->setStatut($data['statut'] ?? 'En attente');
            $this->setIdCompte($data['id_compte'] ?? 0);
            $this->setIdCategorie($data['id_categorie'] ?? 0);
            $this->setCandidatPseudonyme($data['candidat_pseudonyme'] ?? null);
            $this->setCandidatEmail($data['candidat_email'] ?? null);
            $this->setCategorieNom($data['categorie_nom'] ?? null);
            $this->setEditionNom($data['edition_nom'] ?? null);
        }
    }

    /**
        * Retrieves the application ID.
     *
     * @return int|null
     */
    public function getIdCandidature(): ?int { return $this->id_candidature; }

    /**
        * Retrieves the application label.
     *
     * @return string
     */
    public function getLibelle(): string { return $this->libelle; }

    /**
        * Retrieves the application platform.
     *
     * @return string
     */
    public function getPlateforme(): string { return $this->plateforme; }

    /**
        * Retrieves the content URL.
     *
     * @return string
     */
    public function getUrlContenu(): string { return $this->url_contenu; }

    /**
        * Retrieves the related image.
     *
     * @return string|null
     */
    public function getImage(): ?string { return $this->image; }

    /**
        * Retrieves the statement.
     *
     * @return string
     */
    public function getArgumentaire(): string { return $this->argumentaire; }

    /**
        * Retrieves the submission date.
     *
     * @return string
     */
    public function getDateSoumission(): string { return $this->date_soumission; }

    /**
        * Retrieves the status.
     *
     * @return string
     */
    public function getStatut(): string { return $this->statut; }

    /**
        * Retrieves the account ID.
     *
     * @return int
     */
    public function getIdCompte(): int { return $this->id_compte; }

    /**
        * Retrieves the category ID.
     *
     * @return int
     */
    public function getIdCategorie(): int { return $this->id_categorie; }

    /**
        * Retrieves the candidate username.
     *
     * @return string|null
     */
    public function getCandidatPseudonyme(): ?string { return $this->candidat_pseudonyme; }

    /**
        * Retrieves the candidate email.
     *
     * @return string|null
     */
    public function getCandidatEmail(): ?string { return $this->candidat_email; }

    /**
        * Retrieves the category name.
     *
     * @return string|null
     */
    public function getCategorieNom(): ?string { return $this->categorie_nom; }

    /**
        * Retrieves the edition name.
     *
     * @return string|null
     */
    public function getEditionNom(): ?string { return $this->edition_nom; }

    /**
        * Sets the application ID.
     *
     * @param int|null $id
     * @return void
     */
    public function setIdCandidature(?int $id): void { $this->id_candidature = $id; }

    /**
        * Sets the application label.
     *
     * @param string $libelle
     * @return void
        * @throws \InvalidArgumentException If the label is invalid.
     */
    public function setLibelle(string $libelle): void
    {
        $libelle = trim($libelle);
        if (empty($libelle)) throw new \InvalidArgumentException('Le libellé est obligatoire.');
        if (strlen($libelle) > 255) throw new \InvalidArgumentException('Le libellé ne peut pas dépasser 255 caractères.');
        $this->libelle = $libelle;
    }

    /**
        * Sets the platform.
     *
     * @param string $plateforme
     * @return void
        * @throws \InvalidArgumentException If the platform is invalid.
     */
    public function setPlateforme(string $plateforme): void
    {
        $allowed = ['TikTok', 'Instagram', 'YouTube', 'Facebook', 'X', 'Twitch', 'Autre'];
        if (!in_array($plateforme, $allowed)) throw new \InvalidArgumentException('Plateforme non autorisée.');
        $this->plateforme = $plateforme;
    }

    /**
        * Sets the content URL.
     *
     * @param string $url
     * @return void
        * @throws \InvalidArgumentException If the URL is invalid.
     */
    public function setUrlContenu(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) throw new \InvalidArgumentException('URL invalide.');
        $this->url_contenu = $url;
    }

    /**
        * Sets the image.
     *
     * @param string|null $image
     * @return void
     */
    public function setImage(?string $image): void { $this->image = $image; }

    /**
        * Sets the statement.
     *
     * @param string $argumentaire
     * @return void
        * @throws \InvalidArgumentException If the statement is invalid.
     */
    public function setArgumentaire(string $argumentaire): void
    {
        $argumentaire = trim($argumentaire);
        if (empty($argumentaire)) throw new \InvalidArgumentException('L\'argumentaire est obligatoire.');
        $this->argumentaire = $argumentaire;
    }

    /**
     * Sets the submission date.
     *
     * @param string $date
     * @return void
     */
    public function setDateSoumission(string $date): void { $this->date_soumission = $date; }

    /**
     * Sets the status.
     *
     * @param string $statut
     * @return void
     * @throws \InvalidArgumentException If the status is invalid.
     */
    public function setStatut(string $statut): void
    {
        $allowed = ['En attente', 'Approuvée', 'Rejetée'];
        if (!in_array($statut, $allowed)) throw new \InvalidArgumentException('Statut invalide.');
        $this->statut = $statut;
    }

    /**
     * Sets the account ID.
     *
     * @param int $id
     * @return void
     * @throws \InvalidArgumentException If the ID is invalid.
     */
    public function setIdCompte(int $id): void
    {
        if ($id <= 0) throw new \InvalidArgumentException('ID compte invalide.');
        $this->id_compte = $id;
    }

    /**
     * Sets the category ID.
     *
     * @param int $id
     * @return void
     * @throws \InvalidArgumentException If the ID is invalid.
     */
    public function setIdCategorie(int $id): void
    {
        if ($id <= 0) throw new \InvalidArgumentException('ID catégorie invalide.');
        $this->id_categorie = $id;
    }

    /**
     * Sets the candidate pseudonym.
     *
     * @param string|null $pseudo
     * @return void
     */
    public function setCandidatPseudonyme(?string $pseudo): void { $this->candidat_pseudonyme = $pseudo; }

    /**
     * Sets the candidate email.
     *
     * @param string|null $email
     * @return void
     */
    public function setCandidatEmail(?string $email): void { $this->candidat_email = $email; }

    /**
     * Sets the category name.
     *
     * @param string|null $nom
     * @return void
     */
    public function setCategorieNom(?string $nom): void { $this->categorie_nom = $nom; }

    /**
     * Sets the edition name.
     *
     * @param string|null $nom
     * @return void
     */
    public function setEditionNom(?string $nom): void { $this->edition_nom = $nom; }
}