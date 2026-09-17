#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <float.h>

// Fonction pour calculer la distance entre deux points
double Distance(int A[2], int B[2]) {
    return sqrt(pow(A[0] - B[0], 2) + pow(A[1] - B[1], 2));
}


void Affichage_grille(int Coordonnee[8][2], int chemin[8]) {
    char grille[50][20]; // Création d'une grille de 50 x 20

    // Initialisation de la grille avec des espaces vides
    for (int i = 0; i < 50; i++) {
        for (int j = 0; j < 20; j++) {
            grille[i][j] = ' ';
        }
    }

    // Placement des points du chemin sur la grille avec l'ordre des visites
    for (int i = 0; i < 8; i++) {
        int x = Coordonnee[chemin[i]][0];
        int y = Coordonnee[chemin[i]][1];
        grille[x][y] = '0' + i; // Affiche le numéro de visite (de '0' à '7')
    }

    // Affichage de la grille
    printf("\nChemin affiché sur la grille :\n");
    printf("----------------------------------------------------\n");
    for (int j = 0; j < 20; j++) {
        printf("|");
        for (int i = 0; i < 50; i++) {
            printf("%c", grille[i][j]); // Affiche chaque cellule de la grille
        }
        printf("|\n");
    }
    printf("----------------------------------------------------\n");
}

// Fonction heuristique : Algorithme du plus proche voisin
void PlusProcheVoisin(int Coordonnee[8][2]) {
    int Visite[8] = {0};       // Tableau pour suivre les points visités (0 = non visité, 1 = visité)
    int chemin[8];             // Tableau pour stocker l'ordre des points visités
    double distanceTotale = 0; // Distance totale du chemin

    int courant = 0; 
    Visite[courant] = 1;
    chemin[0] = courant;

    for (int i = 1; i < 8; i++) {
        int suivant = -1;
        double distanceMin = DBL_MAX;

        // Recherche du point le plus proche non visité
        for (int j = 0; j < 8; j++) {
            if (!Visite[j]) { // Si le point n'a pas encore été visité
                double dist = Distance(Coordonnee[courant], Coordonnee[j]);
                if (dist < distanceMin) {
                    distanceMin = dist;
                    suivant = j;
                }
            }
        }

        // Aller au point suivant
        distanceTotale += distanceMin;
        courant = suivant;
        Visite[courant] = 1;
        chemin[i] = courant;
    }

    // Retour au point de départ
    distanceTotale += Distance(Coordonnee[courant], (int[]){0, 0});

    // Affichage du chemin
    printf("\nChemin trouvé avec l'algorithme heuristique :\n");
    printf("[0, 0] -> ");
    for (int i = 0; i < 8; i++) {
        printf("Point %d : [%d, %d] -> ", chemin[i], Coordonnee[chemin[i]][0], Coordonnee[chemin[i]][1]);
    }
    printf("[0, 0]\n");
    printf("\nDistance totale : %.2f\n", distanceTotale);

    // Afficher le chemin sous forme de grille
    Affichage_grille(Coordonnee, chemin);
}

int main() {
    int Coordonnee[8][2] = {
        {10, 10}, {15, 5}, {20, 20}, {25, 5},
        {30, 15}, {35, 25}, {40, 10}, {45, 20}
    };

    PlusProcheVoisin(Coordonnee);

    return 0;
}
