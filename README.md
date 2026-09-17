# Projets Universitaires – BUT Informatique

Bienvenue sur ce dépôt ! Il rassemble les différents projets et SAÉ (Situations d'Apprentissage et d'Évaluation) que j'ai réalisés durant mes deux premières années de **BUT Informatique** à l'**IUT de Saint-Dié-des-Vosges** (Université de Lorraine).

Ces travaux couvrent plusieurs domaines : développement logiciel (C, Java), développement web (PHP, JavaScript, MySQL), algorithmique avancée, administration système / réseaux (Linux, Apache, Docker) et gestion de projet.

---

## Sommaire

- [Organisation du dépôt](#organisation-du-dépôt)
- [Semestre 1 — Fondamentaux & découverte](#semestre-1--fondamentaux--découverte)
- [Semestre 2 — POO, graphes et réseaux](#semestre-2--poo-graphes-et-réseaux)
- [Semestre 3 — Architecture web & projet CineVote](#semestre-3--architecture-web--projet-cinevote)
- [Semestre 4 — Industrialisation, Docker & Social Media Awards](#semestre-4--industrialisation-docker--social-media-awards)
- [Technologies utilisées](#technologies-utilisées)
- [Comment explorer les projets ?](#comment-explorer-les-projets-)
- [Auteur](#auteur)

---

## Organisation du dépôt

```text
├── Semestre_1/
│   ├── SAE-1.01/   # Jeu textuel "World of IUT" (C)
│   ├── SAE-1.02/   # Algorithmes de tri & Voyageur de commerce (C)
│   ├── SAE-1.03/   # Installation d'un poste de dev (Linux Debian)
│   ├── SAE-1.04/   # Conception BDD d'un club de voile (MySQL)
│   ├── SAE-1.05/   # Recueil de besoins sur l'assistance robotisée
│   └── SAE-1.06/   # Analyse d'organisation (OpenAI) & site web
│
├── Semestre_2/
│   ├── SAE-2.01/   # Jeu de combat 2D type Street Fighter (Java Swing)
│   ├── SAE-2.02/   # Théorie des graphes & coloration (Welsh-Powell)
│   ├── SAE-2.03/   # Configuration de services réseau (Linux, Apache)
│   ├── SAE-2.04/   # Base de données d'un supermarché & analyses SQL
│   ├── SAE-2.05/   # Gestion de projet agile — App "CultureJam"
│   └── SAE-2.06/   # Travail en équipe, communication & soutenance
│
├── Semestre_3/
│   └── SAE-3/      # "CineVote" : plateforme de vote cinéma (PHP MVC, MySQL)
│
└── Semestre_4/
    └── SAE-4.00/   # "Social Media Awards" : app web conteneurisée (PHP, Docker, MySQL, PHPUnit)
```

---

## Semestre 1 — Fondamentaux & découverte

| Projet       | Sujet                                | Technologies             | Description rapide                                                                                                                       |
| ------------ | ------------------------------------ | ------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------- |
| **SAÉ 1.01** | World of IUT                         | C, Makefile              | Jeu d'aventure textuel en console. Gestion d'inventaire, déplacement entre salles et interactions textuelles.                            |
| **SAÉ 1.02** | Algorithmique & Voyageur de commerce | C, GCC                   | Comparaison de méthodes de tri et résolution approchée du problème du voyageur de commerce (analyse de complexité et temps d'exécution). |
| **SAÉ 1.03** | Poste de développement               | Linux Debian, VirtualBox | Comparatif de systèmes d'exploitation, virtualisation et mise en place d'un environnement de dev complet sous Debian.                    |
| **SAÉ 1.04** | Base de données club de voile        | MySQL, Workbench         | Modélisation relationnelle (diagramme EER), création du schéma SQL avec contraintes d'intégrité et requêtes de test.                     |
| **SAÉ 1.05** | Recueil de besoins                   | Enquêtes, Rédaction      | Étude de la demande et du marché pour l'assistance robotisée (interviews, formalisation des besoins utilisateurs).                       |
| **SAÉ 1.06** | Analyse d'organisation & Web         | HTML5, CSS3, Figma       | Étude économique d'OpenAI, conception de maquettes UX/UI sur Figma et réalisation du site vitrine associé.                               |

---

## Semestre 2 — POO, graphes et réseaux

| Projet       | Sujet                         | Technologies                    | Description rapide                                                                                                                      |
| ------------ | ----------------------------- | ------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------- |
| **SAÉ 2.01** | Jeu de combat 2D              | Java, Swing                     | Jeu inspiré de Street Fighter. Programmation orientée objet, gestion des spritesheets, moteur de collision (hitboxes) et boucle de jeu. |
| **SAÉ 2.02** | Graphes & Optimisation        | Théorie des graphes, C / Python | Modélisation sous forme de graphes, algorithme de coloration (Welsh-Powell) et heuristiques gloutonnes d'optimisation.                  |
| **SAÉ 2.03** | Services réseau               | Linux Debian, Apache, DNS       | Mise en place et configuration de services d'infrastructure réseau (serveur web Apache, virtual hosts, adressage).                      |
| **SAÉ 2.04** | Base de données supermarché   | SQL, Python                     | Exploitation d'une base relationnelle avec gros volume de données, requêtes d'analyse décisionnelle et restitution statistique.         |
| **SAÉ 2.05** | Gestion de projet agile       | Scrum, Trello                   | Cadrage du projet d'application mobile _CultureJam_ (backlog, user stories, cycles de sprints et découpage agile).                      |
| **SAÉ 2.06** | Communication professionnelle | Présentation orale              | Travail d'équipe, préparation des supports de présentation et soutenance finale devant jury.                                            |

---

## Semestre 3 — Architecture web & projet CineVote

Le semestre 3 s'est concentré sur un projet web d'envergure : **CineVote**, une plateforme complète de vote pour un festival de cinéma.

- **Stack technique :** PHP 8.2+ (architecture MVC en PHP natif sans framework lourd), base de données MySQL, JavaScript Vanilla, CSS3.
- **Fonctionnalités clés :**
  - Vote anonymisé avec empreinte cryptographique (SHA-256) et limitation stricte par utilisateur/catégorie.
  - Espace membres avec 3 rôles (Électeur, Candidat, Administrateur).
  - Sécurité renforcée : hachage de mot de passe en `bcrypt`, protection contre les failles CSRF, proxy PHP pour sécuriser l'accès aux uploads.
  - Emails transactionnels (SendGrid) pour confirmation de compte et réinitialisation de mot de passe.
  - Back-office complet pour modérer les propositions et gérer les périodes de vote.

> Le dossier [`Semestre_3/SAE-3`](./Semestre_3/SAE-3) contient le code source complet ainsi que son propre README avec les consignes de configuration.

---

## Semestre 4 — Industrialisation, Docker & Social Media Awards

La SAÉ 4.00 a permis de franchir un cap sur les bonnes pratiques de développement, l'automatisation et l'industrialisation avec le projet **Social Media Awards**.

- **Stack technique :** PHP 8.3+, Docker Compose (Apache, MySQL, phpMyAdmin), Composer (autoloading PSR-4), PHPUnit.
- **Points forts du projet :**
  - Environnement de développement conteneurisé prêt à l'emploi (`docker compose up -d`).
  - Architecture MVC claire avec contrôleur frontal et gestion des routes sans extension.
  - Tests automatisés (tests unitaires et d'intégration via PHPUnit).
  - Outils de minification maison pour les fichiers CSS et JS.
  - Schéma de base de données complet avec triggers, procédures stockées et données de test (seeds).

> Rendez-vous dans [`Semestre_4/SAE-4.00`](./Semestre_4/SAE-4.00) pour consulter le code et le guide de démarrage Docker détaillé.

---

## Technologies utilisées

- **Langages :** C, Java, PHP, SQL, HTML5, CSS3, JavaScript
- **Bases de données :** MySQL, phpMyAdmin, MySQL Workbench
- **DevOps & Environnement :** Docker, Docker Compose, Linux (Debian), Apache, Git, Composer, PHPUnit
- **Méthodologies & Outils :** Architecture MVC, POO, Programmation procédurale, Algorithmique (graphes, complexité), Agile (Scrum), Figma

---

## Comment explorer les projets ?

Chaque sous-dossier de SAÉ contient :

1. Le code source des applications ou scripts.
2. Les livrables associés quand ils sont applicables (rapports PDF/Word, diagrammes, schémas relationnels).
3. Pour les projets web (Semestre 3 et 4), des fichiers de configuration d'exemple (`.env.exemple`) et des instructions détaillées dans leurs READMEs respectifs.

---

## Auteur

**Lucas Cesar**  
Étudiant en BUT Informatique — IUT de Saint-Dié-des-Vosges (Université de Lorraine)
