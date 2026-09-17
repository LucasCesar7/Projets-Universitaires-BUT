# SAÉ 1.02 - Comparaison d'approches algorithmiques

## Présentation du projet
Ce projet a été réalisé dans le cadre de la SAÉ 1.02 lors de notre première année de BUT Informatique. L'objectif de cette ressource était d'appréhender la notion d'efficacité algorithmique en comparant plusieurs approches pour résoudre un même problème. 

Notre étude s'est concentrée sur deux axes majeurs :
* L'analyse et l'implémentation d'algorithmes de tri classiques.
* La résolution du célèbre "problème du voyageur de commerce" (recherche du plus court chemin passant par un ensemble de villes pour revenir à son point de départ).

## Objectifs pédagogiques
Plutôt que de simplement écrire un code fonctionnel, cette SAÉ nous a poussés à réfléchir à l'optimisation :
* Comprendre et calculer la complexité algorithmique.
* Mettre en place des protocoles d'expérimentation rigoureux pour mesurer les temps d'exécution.
* Argumenter et justifier le choix d'un algorithme plutôt qu'un autre en fonction du volume de données à traiter et des contraintes de ressources.

## Contenu du projet
Ce dépôt contient les sources liées au projet "sae-voyageur" :
* Les codes sources des différents algorithmes étudiés et comparés.
* Les jeux de données utilisés pour tester l'efficacité de nos implémentations.
* L'historique de développement montrant l'évolution et les corrections apportées au code.

## Compilation et Exécution
Pour tester les algorithmes et reproduire nos expérimentations, le programme doit être compilé et exécuté depuis un terminal.

**1. Compilation**
Ouvrez votre terminal à la racine du projet et utilisez la commande de compilation appropriée :

**gcc *.c -o voyageur**

**2. Exécution**

Sous Linux / macOS :

**./voyageur**

Sous Windows :

**voyageur.exe**