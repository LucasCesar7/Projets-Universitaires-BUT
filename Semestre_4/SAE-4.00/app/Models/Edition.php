<?php

namespace App\Models;

/**
 * Model representing an edition for the Social Media Awards.
 */
class Edition
{
    private ?int $id_edition = null;
    private int $annee;
    private string $nom;
    private ?string $description = null;
    private ?string $image = null;
    private string $date_debut_candidatures;
    private string $date_fin_candidatures;
    private string $date_debut;
    private string $date_fin;
    private ?string $theme = null;
    private int $nb_categories = 0;
    private int $nb_candidatures = 0;
    private int $nb_votants = 0;

    /**
     * Edition model constructor.
     *
     * @param array $data Data used to initialize the object.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->setIdEdition($data['id_edition'] ?? null);
            $this->setAnnee($data['annee'] ?? 0);
            $this->setNom($data['nom'] ?? '');
            $this->setDescription($data['description'] ?? null);
            $this->setImage($data['image'] ?? null);
            $this->setDateDebutCandidatures($data['date_debut_candidatures'] ?? '');
            $this->setDateFinCandidatures($data['date_fin_candidatures'] ?? '');
            $this->setDateDebut($data['date_debut'] ?? '');
            $this->setDateFin($data['date_fin'] ?? '');
            $this->setTheme($data['theme'] ?? null);
            $this->setNbCategories($data['nb_categories'] ?? 0);
            $this->setNbCandidatures($data['nb_candidatures'] ?? 0);
            $this->setNbVotants($data['nb_votants'] ?? 0);
        }
    }

    /**
     * Returns the edition ID.
     *
     * @return int|null
     */
    public function getIdEdition(): ?int
    {
        return $this->id_edition;
    }

    /**
     * Returns the year.
     *
     * @return int
     */
    public function getAnnee(): int
    {
        return $this->annee;
    }

    /**
     * Returns the edition year.
     *
     * @return int
     */
    public function getEditionAnnee(): int
    {
        return $this->annee;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Returns the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns the image.
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Returns the application start date.
     *
     * @return string
     */
    public function getDateDebutCandidatures(): string
    {
        return $this->date_debut_candidatures;
    }

    /**
     * Returns the application end date.
     *
     * @return string
     */
    public function getDateFinCandidatures(): string
    {
        return $this->date_fin_candidatures;
    }

    /**
     * Returns the start date.
     *
     * @return string
     */
    public function getDateDebut(): string
    {
        return $this->date_debut;
    }

    /**
     * Returns the end date.
     *
     * @return string
     */
    public function getDateFin(): string
    {
        return $this->date_fin;
    }

    /**
     * Returns the theme.
     *
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * Returns the number of categories.
     *
     * @return int
     */
    public function getNbCategories(): int
    {
        return $this->nb_categories;
    }

    /**
     * Returns the number of applications.
     *
     * @return int
     */
    public function getNbCandidatures(): int
    {
        return $this->nb_candidatures;
    }

    /**
     * Returns the number of voters.
     *
     * @return int
     */
    public function getNbVotants(): int
    {
        return $this->nb_votants;
    }

    /**
     * Checks whether the edition is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = new \DateTime();
        $debut = new \DateTime($this->date_debut);
        $fin = new \DateTime($this->date_fin);

        return ($now >= $debut && $now <= $fin);
    }

    /**
     * Sets the edition ID.
     *
     * @param int|null $id_edition
     * @return void
     */
    public function setIdEdition(?int $id_edition): void
    {
        $this->id_edition = $id_edition;
    }

    /**
     * Sets the year.
     *
     * @param int $annee
     * @return void
     * @throws \InvalidArgumentException If the year is invalid.
     */
    public function setAnnee(int $annee): void
    {
        if ($annee < 2000 || $annee > 2100) {
            throw new \InvalidArgumentException('Année invalide.');
        }
        $this->annee = $annee;
    }

    /**
     * Sets the name.
     *
     * @param string $nom
     * @return void
     * @throws \InvalidArgumentException If the name is invalid.
     */
    public function setNom(string $nom): void
    {
        $nom = trim($nom);
        if (empty($nom)) {
            throw new \InvalidArgumentException('Le nom de l\'édition est obligatoire.');
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
     * Sets the application start date.
     *
     * @param string $date
     * @return void
     */
    public function setDateDebutCandidatures(string $date): void
    {
        $this->date_debut_candidatures = $date;
    }

    /**
     * Sets the application end date.
     *
     * @param string $date
     * @return void
     */
    public function setDateFinCandidatures(string $date): void
    {
        $this->date_fin_candidatures = $date;
    }

    /**
     * Sets the start date.
     *
     * @param string $date
     * @return void
     */
    public function setDateDebut(string $date): void
    {
        $this->date_debut = $date;
    }

    /**
     * Sets the end date.
     *
     * @param string $date
     * @return void
     */
    public function setDateFin(string $date): void
    {
        $this->date_fin = $date;
    }

    /**
     * Sets the theme.
     *
     * @param string|null $theme
     * @return void
     */
    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Sets the number of categories.
     *
     * @param int $nb
     * @return void
     */
    public function setNbCategories(int $nb): void
    {
        $this->nb_categories = max(0, $nb);
    }

    /**
     * Sets the number of applications.
     *
     * @param int $nb
     * @return void
     */
    public function setNbCandidatures(int $nb): void
    {
        $this->nb_candidatures = max(0, $nb);
    }

    /**
     * Sets the number of voters.
     *
     * @param int $nb
     * @return void
     */
    public function setNbVotants(int $nb): void
    {
        $this->nb_votants = max(0, $nb);
    }

    /**
     * Returns the current edition status.
     *
     * @return string Current edition status.
     */
    public function getStatus(): string
    {
        $now = date('Y-m-d H:i:s');

        if ($now < $this->date_debut_candidatures) return 'pré-candidatures';
        if ($now <= $this->date_fin_candidatures) return 'candidatures';
        if ($now <= $this->date_fin) return 'votes-en-cours';
        return 'terminée';
    }
    /**
     * Checks whether the edition is active.
     *
     * @return bool
     */
    /**
     * Checks whether the edition is active from the application start to the end of voting.
     *
     * @return bool
     */
    public function getEstActive(): bool
    {
        $now = new \DateTime();
        $debutCandidatures = new \DateTime($this->date_debut_candidatures);
        $finVotos = new \DateTime($this->date_fin);  // Overall end = voting end

        return $now >= $debutCandidatures && $now <= $finVotos;
    }

    /**
     * Checks whether the edition accepts applications.
     *
     * @return bool
     */
    public function isAcceptingCandidatures(): bool
    {
        return $this->getEstActive();
    }
}
