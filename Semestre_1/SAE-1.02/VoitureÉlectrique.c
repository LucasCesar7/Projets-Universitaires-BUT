#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <float.h>

// Caractéristiques du modèle de voiture
#define AUTONOMIE_MAX 250       // Autonomie maximale en km
#define RECHARGE_KM_PAR_MIN 100 // Kilomètres récupérés par minute de recharge
#define TEMPS_RECHARGE_MAX 15   // Temps de recharge maximal en minutes

// Fonction pour calculer la distance entre deux points
double Distance(int A[2], int B[2]) {
    return sqrt(pow(A[0] - B[0], 2) + pow(A[1] - B[1], 2));
}

// Algorithme du plus proche voisin avec gestion de l'autonomie et recharge
void TourneeOptimisee(int Coordonnee[8][2]) {
    int Visite[8] = {0};       // Tableau pour suivre les points visités (0 = non visité, 1 = visité)
    int chemin[8];             // Tableau pour stocker l'ordre des points visités
    double distanceTotale = 0; // Distance totale du chemin
    double autonomieRestante = AUTONOMIE_MAX; // Autonomie restante de la voiture

    int courant = 0; // On commence par le point [0, 0]
    Visite[courant] = 1;
    chemin[0] = courant;

    printf("\nDébut de la tournée avec une autonomie de %.2f km.\n", autonomieRestante);

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

        // Vérifier si l'autonomie restante permet d'atteindre le prochain point
        if (autonomieRestante < distanceMin) {
            // Recharge nécessaire
            double kmManquants = distanceMin - autonomieRestante;
            double tempsRecharge = ceil(kmManquants / RECHARGE_KM_PAR_MIN);

            // Assurer que le temps de recharge ne dépasse pas le maximum permis
            if (tempsRecharge > TEMPS_RECHARGE_MAX) {
                tempsRecharge = TEMPS_RECHARGE_MAX;
            }

            double kmRecharges = tempsRecharge * RECHARGE_KM_PAR_MIN;
            autonomieRestante += kmRecharges;

            printf("Recharge à [%d, %d] pendant %.0f minutes (autonomie actuelle : %.2f km).\n",
                   Coordonnee[courant][0], Coordonnee[courant][1], tempsRecharge, autonomieRestante);
        }

        // Aller au point suivant
        distanceTotale += distanceMin;
        autonomieRestante -= distanceMin;
        courant = suivant;
        Visite[courant] = 1;
        chemin[i] = courant;

        printf("Visite de la ville [%d, %d]. Autonomie restante : %.2f km.\n",
               Coordonnee[courant][0], Coordonnee[courant][1], autonomieRestante);
    }

    // Retour au point de départ
    double distanceRetour = Distance(Coordonnee[courant], (int[]){0, 0});
    if (autonomieRestante < distanceRetour) {
        // Recharge avant le retour
        double kmManquants = distanceRetour - autonomieRestante;
        double tempsRecharge = ceil(kmManquants / RECHARGE_KM_PAR_MIN);

        if (tempsRecharge > TEMPS_RECHARGE_MAX) {
            tempsRecharge = TEMPS_RECHARGE_MAX;
        }

        double kmRecharges = tempsRecharge * RECHARGE_KM_PAR_MIN;
        autonomieRestante += kmRecharges;

        printf("Recharge à [%d, %d] pendant %.0f minutes pour retourner au point de départ.\n",
               Coordonnee[courant][0], Coordonnee[courant][1], tempsRecharge);
    }
    distanceTotale += distanceRetour;

    printf("\nRetour au point de départ. Distance totale parcourue : %.2f km.\n", distanceTotale);
}


int main() {
   int Coordonnee[8][2] = {
    {0, 0}, {200, 0}, {400, 0}, {600, 0},{800, 0}, {1000, 0}, {1200, 0}, {1400, 0}  
};

    TourneeOptimisee(Coordonnee);

    return 0;
}
