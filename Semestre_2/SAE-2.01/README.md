# SAÉ 2.01 - Développement d'une application : Jeu de combat 2D

## Présentation du projet
Ce projet a été réalisé dans le cadre de la SAÉ 2.01 lors de notre première année de BUT Informatique. L'objectif principal était de concevoir et de développer une application complète dotée d'une interface graphique interactive. Le projet s'est concrétisé par la création d'un jeu de combat en 2D, inspiré des classiques du genre comme Street Fighter.

## Objectifs pédagogiques
Le développement de ce jeu a permis d'approfondir les concepts de la Programmation Orientée Objet (POO) et de relever plusieurs défis techniques majeurs :
* L'intégration, la gestion et l'animation des sprites pour assurer la fluidité visuelle des personnages.
* L'implémentation d'un système mathématique de détection des collisions (hitboxes) entre les entités.
* La synchronisation stricte et la séparation des responsabilités entre le moteur logique (physique, calcul des dégâts) et l'interface graphique (rendu, capture des événements clavier).
* La structuration d'une architecture logicielle propre, maintenable et évolutive.

## Contenu du projet
L'archive de ce dépôt contient l'ensemble des éléments permettant de comprendre et de faire tourner le jeu :
* Le code source de l'application classé par paquets (modèles, vues, contrôleurs).
* Le dossier des ressources (assets) regroupant les spritesheets des personnages, les décors et potentiellement les effets sonores.
* La documentation de conception, incluant généralement les diagrammes de classes (UML) illustrant l'architecture du code.

## Compilation et Exécution
Ce projet reposant sur une interface graphique, son exécution dépend de l'environnement de développement utilisé (généralement Java). 

**1. Via un IDE (Méthode recommandée)**
* Importez le dossier du projet dans votre environnement de développement habituel (IntelliJ IDEA, Eclipse, VS Code).
* Assurez-vous que le dossier des ressources (assets) est bien défini comme un dossier source dans votre configuration.
* Lancez la classe principale contenant la méthode `main`.