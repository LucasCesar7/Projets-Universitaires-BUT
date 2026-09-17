# CineVote - Plateforme de Votes Cinéma

**CineVote** est une application web complète permettant de gérer un festival de cinéma avec système de vote anonyme, gestion de contenu, modération et tableau de bord administrateur.

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/votre-repo/cinevote)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://www.mysql.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)


## Fonctionnalités

### **Système de Vote**
-  Vote anonyme via empreinte cryptographique (hash SHA-256)
-  Un vote par utilisateur et par catégorie
-  Périodes de vote configurables
-  Visualisation des résultats en temps réel
-  Statistiques détaillées par film/acteur/réalisateur

### **Gestion des Utilisateurs**
-  Authentification sécurisée (session + hash bcrypt)
-  3 rôles : Électeur, Candidat, Administrateur
-  Vérification email obligatoire
-  Réinitialisation de mot de passe par email
-  Système de tokens sécurisés (expiration 1h)

### **Gestion du Contenu**
-  Ajout de films, acteurs, réalisateurs
-  Upload d'images sécurisé (validation type/taille)
-  Protection des images (accès via proxy PHP)
-  Modération des propositions (validation/refus)
-  Commentaires sur les films/acteurs/réalisateurs
-  Filtrage par genres

### **Administration**
-  Tableau de bord complet
-  Gestion des utilisateurs (activation/suspension)
-  Modération des commentaires
-  Configuration des périodes (ajout/vote)
-  Logs d'audit (actions, IP, timestamps)
-  Statistiques globales

### **Sécurité**
-  Protection CSRF (tokens)
-  Validation des entrées (XSS, SQL injection)
-  Rate limiting (tentatives connexion)
-  `.htaccess` pour blocage accès direct
-  Logs d'audit complets)
-  Images servies via proxy PHP

### **Notifications**
-  Emails de vérification
-  Emails de réinitialisation mot de passe

### **Responsive Design**
-  Interface adaptée mobile/tablette/desktop
-  Design moderne (CSS Grid, Flexbox)
-  Thème personnalisé (variables CSS)

---

## Technologies

### **Backend**
- **PHP 8.2+** 
- **MySQL 8.0+**

### **Frontend**
- **HTML5** (Sémantique)
- **CSS3** (Grid, Flexbox, Variables)
- **JavaScript** (Vanilla JS, Fetch API)

### **Sécurité**
- **bcrypt** (Hachage mots de passe)
- **SHA-256** (Empreintes anonymes)
- **CSRF Tokens** (Protection formulaires)
- **`.htaccess`** (Blocage accès direct)

### **Outils**
- **SENDGrid** (Envoi emails)
- **Git** (Versioning)
- **Composer**

---

## Prérequis

| Logiciel | Version minimale | Téléchargement |
|----------|------------------|----------------|
| **PHP** | 8.2+ | [php.net](https://www.php.net/downloads) |
| **MySQL** | 8.0+ | [mysql.com](https://dev.mysql.com/downloads/) |
| **Apache** | 2.4+ | Inclus dans WAMP/XAMP |


---


Par TAS Tom & CESAR Lucas