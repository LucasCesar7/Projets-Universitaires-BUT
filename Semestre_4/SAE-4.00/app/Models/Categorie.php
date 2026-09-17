<?php

namespace App\Models;

/**
 * Model representing a category in the Social Media Awards.
 */
class Categorie
{
    private ?int $id_categorie = null;
    private string $nom;
    private ?string $description = null;
    private ?string $image = null;
    private ?string $plateforme_cible = 'Toutes';
    private ?string $date_debut_votes = null;
    private ?string $date_fin_votes = null;
    private ?string $date_fin_candidatures = null;
    private int $id_edition;
    private ?string $edition_nom = null;
    private int $limite_nomines = 10;
    private int $nb_candidatures = 0;
    private int $nb_nominations = 0;

    /**
        * Categorie model constructor.
     *
        * @param array $data Data used to initialize the object.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->setIdCategorie($data['id_categorie'] ?? null);
            $this->setNom($data['nom'] ?? '');
            $this->setDescription($data['description'] ?? null);
            $this->setImage($data['image'] ?? null);
            $this->setPlateformeCible($data['plateforme_cible'] ?? 'Toutes');
            $this->setDateDebutVotes($data['date_debut_votes'] ?? null);
            $this->setDateFinVotes($data['date_fin_votes'] ?? null);
            $this->setDateFinCandidatures($data['date_fin_candidatures'] ?? null);
            $this->setIdEdition($data['id_edition'] ?? 0);
            $this->setLimiteNomines($data['limite_nomines'] ?? 10);
            $this->setEditionNom($data['edition_nom'] ?? null);
            $this->setNbCandidatures($data['nb_candidatures'] ?? 0);
            $this->setNbNominations($data['nb_nominations'] ?? 0);
        }
    }

    // ======================
    // GETTERS
    // ======================
    /**
     * Retrieves the category ID.
     *
     * @return int|null
     */
    public function getIdCategorie(): ?int
    {
        return $this->id_categorie;
    }

    /**
        * Retrieves the category name.
     *
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
        * Retrieves the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
        * Retrieves the image.
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
        * Retrieves the target platform.
     *
     * @return string|null
     */
    public function getPlateformeCible(): ?string
    {
        return $this->plateforme_cible ?: 'Toutes';
    }

    /**
        * Retrieves the voting start date.
     *
     * @return string|null
     */
    public function getDateDebutVotes(): ?string
    {
        return $this->date_debut_votes;
    }

    /**
        * Retrieves the voting end date.
     *
     * @return string|null
     */
    public function getDateFinVotes(): ?string
    {
        return $this->date_fin_votes;
    }

    /**
        * Retrieves the application end date.
     *
     * @return string|null
     */
    public function getDateFinCandidatures(): ?string
    {
        return $this->date_fin_candidatures;
    }

    /**
        * Retrieves the edition ID.
     *
     * @return int
     */
    public function getIdEdition(): int
    {
        return $this->id_edition;
    }

    /**
        * Retrieves the edition name.
     *
     * @return string|null
     */
    public function getEditionNom(): ?string
    {
        return $this->edition_nom;
    }

    /**
        * Retrieves the nominees limit.
     *
     * @return int
     */
    public function getLimiteNomines(): int
    {
        return $this->limite_nomines;
    }

    /**
        * Retrieves the number of applications.
     *
     * @return int
     */
    public function getNbCandidatures(): int
    {
        return $this->nb_candidatures;
    }

    /**
        * Retrieves the number of nominations.
     *
     * @return int
     */
    public function getNbNominations(): int
    {
        return $this->nb_nominations;
    }

    // ======================
    // SETTERS
    // ======================
    /**
     * Sets the category ID.
     *
     * @param int|null $id_categorie
     * @return void
     */
    public function setIdCategorie(?int $id_categorie): void
    {
        $this->id_categorie = $id_categorie;
    }

    /**
        * Sets the category name.
     *
     * @param string $nom
     * @return void
        * @throws \InvalidArgumentException If the name is invalid.
     */
    public function setNom(string $nom): void
    {
        $nom = trim($nom);
        if (empty($nom)) {
            throw new \InvalidArgumentException('Le nom de la catégorie est obligatoire.');
        }
        if (strlen($nom) > 100) {
            throw new \InvalidArgumentException('Le nom ne peut pas dépasser 100 caractères.');
        }
        $this->nom = $nom;
    }

    /**
        * Sets the description.
     *
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description ? trim($description) : null;
    }

    /**
        * Sets the image.
     *
     * @param string|null $image
     * @return void
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
        * Sets the target platform.
     *
     * @param string|null $plateforme_cible
     * @return void
        * @throws \InvalidArgumentException If the platform is invalid.
     */
    /**
        * Sets the target platform.
     *
     * @param string|null $plateforme_cible
     * @return void
        * @throws \InvalidArgumentException If the platform is invalid.
     */
    public function setPlateformeCible(?string $plateforme_cible): void
    {
        $allowed = [
            'Toutes',
            'TikTok',
            'Instagram',
            'YouTube',
            'Twitch',
            'Spotify',
            'X',
            'Facebook',
            'Snapchat',
            'Autre'
        ];

        if ($plateforme_cible !== null && !in_array($plateforme_cible, $allowed)) {
            $this->plateforme_cible = $plateforme_cible;
            return;
        }

        $this->plateforme_cible = $plateforme_cible ?: 'Toutes';
    }

    /**
        * Sets the voting start date.
     *
     * @param string|null $date_debut_votes
     * @return void
     */
    public function setDateDebutVotes(?string $date_debut_votes): void
    {
        $this->date_debut_votes = $date_debut_votes;
    }

    /**
        * Sets the voting end date.
     *
     * @param string|null $date_fin_votes
     * @return void
     */
    public function setDateFinVotes(?string $date_fin_votes): void
    {
        $this->date_fin_votes = $date_fin_votes;
    }

    /**
        * Sets the application end date.
     *
     * @param string|null $date_fin_candidatures
     * @return void
     */
    public function setDateFinCandidatures(?string $date_fin_candidatures): void
    {
        $this->date_fin_candidatures = $date_fin_candidatures;
    }

    /**
        * Sets the edition ID.
     *
     * @param int $id_edition
     * @return void
        * @throws \InvalidArgumentException If the ID is invalid.
     */
    public function setIdEdition(int $id_edition): void
    {
        if ($id_edition <= 0) {
            throw new \InvalidArgumentException('ID édition invalide.');
        }
        $this->id_edition = $id_edition;
    }

    /**
        * Sets the edition name.
     *
     * @param string|null $edition_nom
     * @return void
     */
    public function setEditionNom(?string $edition_nom): void
    {
        $this->edition_nom = $edition_nom;
    }

    /**
        * Sets the nominees limit.
     *
     * @param int $limite_nomines
     * @return void
        * @throws \InvalidArgumentException If the limit is invalid.
     */
    public function setLimiteNomines(int $limite_nomines): void
    {
        if ($limite_nomines < 1 || $limite_nomines > 50) {
            throw new \InvalidArgumentException('La limite doit être entre 1 et 50.');
        }
        $this->limite_nomines = $limite_nomines;
    }

    /**
        * Sets the number of applications.
     *
     * @param int $nb_candidatures
     * @return void
     */
    public function setNbCandidatures(int $nb_candidatures): void
    {
        $this->nb_candidatures = max(0, $nb_candidatures);
    }

    /**
        * Sets the number of nominations.
     *
     * @param int $nb_nominations
     * @return void
     */
    public function setNbNominations(int $nb_nominations): void
    {
        $this->nb_nominations = max(0, $nb_nominations);
    }

    /**
        * Checks whether the category is active (voting in progress).
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if (!$this->date_fin_votes) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $debut = $this->date_debut_votes ?? $now;

        return $now >= $debut && $now <= $this->date_fin_votes;
    }
}
