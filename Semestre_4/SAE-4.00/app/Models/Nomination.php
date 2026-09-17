<?php

namespace App\Models;

/**
 * Model representing a nomination in the Social Media Awards.
 */
class Nomination
{
    private ?int $id_nomination = null;
    private string $libelle;
    private ?string $categorie_nom = null;
    private string $plateforme;
    private string $url_contenu;
    private ?string $url_image = null;
    private string $argumentaire;
    private ?string $date_approbation = null;
    private int $id_candidature;
    private int $id_categorie;
    private ?int $id_compte = null;
    private ?int $id_admin = null;

    /**
        * Nomination model constructor.
     *
        * @param array $data Data used to initialize the object.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->setIdNomination($data['id_nomination'] ?? null);
            $this->setLibelle($data['libelle'] ?? '');
            $this->setCategorieNom($data['categorie_nom'] ?? null);
            $this->setPlateforme($data['plateforme'] ?? '');
            $urlContenu = $data['url_contenu'] ?? $data['url_content'] ?? '';
            $this->setUrlContenu($urlContenu);
            $this->setUrlImage($data['url_image'] ?? null);
            $this->setArgumentaire($data['argumentaire'] ?? '');
            $this->setDateApprobation($data['date_approbation'] ?? null);
            $this->setIdCandidature($data['id_candidature'] ?? 0);
            $this->setIdCategorie($data['id_categorie'] ?? 0);
            $this->setIdCompte($data['id_compte'] ?? null);
            $this->setIdAdmin($data['id_admin'] ?? null);
        }
    }

    /**
        * Retrieves the nomination ID.
     *
     * @return int|null
     */
    public function getIdNomination(): ?int
    {
        return $this->id_nomination;
    }

    /**
        * Retrieves the label.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function getCategorieNom(): ?string
    {
        return $this->categorie_nom;
    }

    /**
        * Retrieves the platform.
     *
     * @return string
     */
    public function getPlateforme(): string
    {
        return $this->plateforme;
    }

    /**
        * Retrieves the content URL.
     *
     * @return string
     */
    public function getUrlContenu(): string
    {
        return $this->url_contenu;
    }

    /**
        * Retrieves the image URL.
     *
     * @return string|null
     */
    public function getUrlImage(): ?string
    {
        return $this->url_image;
    }

    /**
        * Retrieves the statement.
     *
     * @return string
     */
    public function getArgumentaire(): string
    {
        return $this->argumentaire;
    }

    /**
        * Retrieves the approval date.
     *
     * @return string|null
     */
    public function getDateApprobation(): ?string
    {
        return $this->date_approbation;
    }

    /**
        * Retrieves the application ID.
     *
     * @return int
     */
    public function getIdCandidature(): int
    {
        return $this->id_candidature;
    }

    /**
        * Retrieves the category ID.
     *
     * @return int
     */
    public function getIdCategorie(): int
    {
        return $this->id_categorie;
    }

    /**
        * Retrieves the account ID.
     *
     * @return int|null
     */
    public function getIdCompte(): ?int
    {
        return $this->id_compte;
    }

    /**
        * Retrieves the admin ID.
     *
     * @return int|null
     */
    public function getIdAdmin(): ?int
    {
        return $this->id_admin;
    }

    /**
        * Sets the nomination ID.
     *
     * @param int|null $id
     * @return void
     */
    public function setIdNomination(?int $id): void
    {
        $this->id_nomination = $id;
    }

    /**
        * Sets the label.
     *
     * @param string $libelle
     * @return void
        * @throws \InvalidArgumentException If the label is invalid.
     */
    public function setLibelle(string $libelle): void
    {
        $libelle = trim($libelle);
        if (empty($libelle)) throw new \InvalidArgumentException('Le libellé est obligatoire.');
        $this->libelle = $libelle;
    }

    public function setCategorieNom(?string $categorieNom): void
    {
        $this->categorie_nom = $categorieNom !== null ? trim($categorieNom) : null;
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
        $plateforme = trim($plateforme);
        if (empty($plateforme)) throw new \InvalidArgumentException('La plateforme est obligatoire.');
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
        $url = trim($url);
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('URL du contenu invalide.');
        }
        $this->url_contenu = $url;
    }

    /**
        * Sets the image URL.
     *
     * @param string|null $url
     * @return void
     */
    public function setUrlImage(?string $url): void
    {
        $this->url_image = $url;
    }

    /**
     * Sets the supporting statement.
     *
     * @param string $argumentaire
     * @return void
     * @throws \InvalidArgumentException If the supporting statement is invalid.
     */
    public function setArgumentaire(string $argumentaire): void
    {
        $argumentaire = trim($argumentaire);
        if (strlen($argumentaire) < 20) {
            throw new \InvalidArgumentException('L\'argumentaire doit faire au moins 20 caractères.');
        }
        $this->argumentaire = $argumentaire;
    }

    /**
     * Sets the approval date.
     *
     * @param string|null $date
     * @return void
     */
    public function setDateApprobation(?string $date): void
    {
        $this->date_approbation = $date;
    }

    /**
     * Sets the application ID.
     *
     * @param int $id
     * @return void
     */
    public function setIdCandidature(int $id): void
    {
        $this->id_candidature = $id;
    }

    /**
     * Sets the category ID.
     *
     * @param int $id
     * @return void
     */
    public function setIdCategorie(int $id): void
    {
        $this->id_categorie = $id;
    }

    /**
     * Sets the account ID.
     *
     * @param int|null $id
     * @return void
     */
    public function setIdCompte(?int $id): void
    {
        $this->id_compte = $id;
    }

    /**
     * Sets the administrator ID.
     *
     * @param int|null $id
     * @return void
     */
    public function setIdAdmin(?int $id): void
    {
        $this->id_admin = $id;
    }
}
