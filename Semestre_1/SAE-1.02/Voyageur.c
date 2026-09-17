#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <math.h>
#include <float.h>

// Fonction pour générer et afficher des coordonnées aléatoires
void genererCoordonnees(int Coordonnee[8][2])
{
    int x, y;          // Variables pour stocker temporairement les coordonnées générées
    srand(time(NULL)); // Initialisation de la fonction rand() avec une graine basée sur le temps actuel

    // Génération des coordonnées aléatoires
    for (int i = 0; i < 8; i++)
    {
        x = rand() % 50;      // Génère une coordonnée x entre 0 et 49
        y = rand() % 20;      // Génère une coordonnée y entre 0 et 19
        Coordonnee[i][0] = x; // Stocke la coordonnée x dans le tableau
        Coordonnee[i][1] = y; // Stocke la coordonnée y dans le tableau

        // Vérification des conditions de validité
        for (int j = 0; j < i; j++) // Parcourt les coordonnées déjà générées
        {
            // Si la coordonnée (x, y) est déjà utilisée ou se trouve sur les bords
            if ((x == Coordonnee[j][0] && y == Coordonnee[j][1]) || x == 0 || x == 49 || y == 0 || y == 19)
            {
                i--;   // Réduit i pour regénérer une coordonnée valide
                break; // Sort de la boucle pour regénérer
            }
        }
    }

    // Affichage des coordonnées générées
    printf("Coordonnées générées:\n");
    for (int i = 0; i < 8; i++)
    {
        printf("Point %i : (%d, %d)\n", i + 1, Coordonnee[i][0], Coordonnee[i][1]); // Affiche chaque coordonnée
    }
}

void affichage_grille(int Coordonnee[8][2])
{
    char grille[50][20]; // Création d'une grille de 50 x 20

    // Initialisation de la grille avec des espaces vides
    for (int i = 0; i < 50; i++)
    {
        for (int j = 0; j < 20; j++)
        {
            grille[i][j] = ' ';
        }
    }

    // Placement des coordonnées générées dans la grille
    for (int k = 0; k < 8; k++)
    {
        int x = Coordonnee[k][0];
        int y = Coordonnee[k][1];
        grille[x][y] = 'X'; 
    }

    // Affichage de la grille
    printf("----------------------------------------------------\n");
    for (int j = 0; j < 20; j++)
    {
        printf("|");
        for (int i = 0; i < 50; i++)
        {
            printf("%c", grille[i][j]); // Affiche chaque cellule de la grille
        }
        printf("|\n");
    }
    printf("----------------------------------------------------\n");
}

// Fonction distance permettant de calculer la distance d entre deux villes
double Distance(int A[2], int B[2])
{
    // Calcul de la distance entre deux points A et B
    return sqrt(pow(A[0] - B[0], 2) + pow(A[1] - B[1], 2));
}

void MatriceDistances(int Coordonnee[8][2], double Matrice[8][8])
{
    for (int i = 0; i < 8; i++)
    {
        for (int j = 0; j < 8; j++)
        {
            if (i == j)
            {
                Matrice[i][j] = 0; // Distance à l'origine (0,0)
            }
            else
            {
                Matrice[i][j] = Distance(Coordonnee[i], Coordonnee[j]); // Calcul dela distance entre 2 points
            }
        }
    }
}

void AfficherMatriceDistances(double Matrice[8][8])
{
    printf("Matrices des distances : \n");
    printf("    "); // Espace pour les étiquettes des colonnes
    for (int i = 0; i < 8; i++)
    {
        printf(" %d    ", i); // Affiche les étiquettes des colonnes
    }
    printf("\n");
    for (int i = 0; i < 8; i++)
    {
        printf("%d ", i); // Afficheles étiquettes des lignes
        for (int j = 0; j < 8; j++)
        {
            //%lf permet de spécifier comment les nombres à virgule flottante (de type double) doivent être affichés ou lus.
            printf("%6.2lf ", Matrice[i][j]); // Affiche les distances avec 2 décimales
        }
        printf("\n");
    }
}

// Fonction pour trouver le chemin le plus court en utilisant ta fonction CheminsPossibles
void TrouverCheminPlusCourt(int Coordonnee[8][2])
{
    int Chemin[8] = {0, 1, 2, 3, 4, 5, 6, 7}; // Indices des points
    double distanceMin = __DBL_MAX__;         // Distance minimale initialisée à la valeur maximale représentable pour un nombre à virgule de type double

    int meilleurChemin[8]; // Tableau pour stocker le meilleur chemin

    // Parcours de toutes les chemins possibles (8!) (c'était la fonctions CheminsPossibles)
    for (int i = 0; i < 8; i++)
    {
        for (int j = 0; j < 8; j++)
        {
            if (j == i)
                continue;
            for (int k = 0; k < 8; k++)
            {
                if (k == i || k == j)
                    continue;
                for (int l = 0; l < 8; l++)
                {
                    if (l == i || l == j || l == k)
                        continue;
                    for (int m = 0; m < 8; m++)
                    {
                        if (m == i || m == j || m == k || m == l)
                            continue;
                        for (int n = 0; n < 8; n++)
                        {
                            if (n == i || n == j || n == k || n == l || n == m)
                                continue;
                            for (int o = 0; o < 8; o++)
                            {
                                if (o == i || o == j || o == k || o == l || o == m || o == n)
                                    continue;
                                for (int p = 0; p < 8; p++)
                                {
                                    if (p == i || p == j || p == k || p == l || p == m || p == n || p == o)
                                        continue;

                                    // Calcul de la distance totale pour ce chemin
                                    double distanceTotale = 0.0; // Initialisation de la distance totale à 0

                                    // Distance entre [0, 0] et le premier point
                                    distanceTotale += Distance((int[]){0, 0}, Coordonnee[Chemin[i]]);

                                    // Distances entre les points successifs
                                    distanceTotale += Distance(Coordonnee[Chemin[i]], Coordonnee[Chemin[j]]);
                                    distanceTotale += Distance(Coordonnee[Chemin[j]], Coordonnee[Chemin[k]]);
                                    distanceTotale += Distance(Coordonnee[Chemin[k]], Coordonnee[Chemin[l]]);
                                    distanceTotale += Distance(Coordonnee[Chemin[l]], Coordonnee[Chemin[m]]);
                                    distanceTotale += Distance(Coordonnee[Chemin[m]], Coordonnee[Chemin[n]]);
                                    distanceTotale += Distance(Coordonnee[Chemin[n]], Coordonnee[Chemin[o]]);
                                    distanceTotale += Distance(Coordonnee[Chemin[o]], Coordonnee[Chemin[p]]);

                                    // Retour au point de départ [0, 0]
                                    distanceTotale += Distance(Coordonnee[Chemin[p]], (int[]){0, 0});

                                    // Mise à jour si le chemin est plus court
                                    if (distanceTotale < distanceMin) // Si il y a une chemins plus court de trouvé alors distanceMin est mis à jour, jusqu'à trouver le chemin le plus court possible.
                                    {
                                        distanceMin = distanceTotale;
                                        meilleurChemin[0] = Chemin[i];
                                        meilleurChemin[1] = Chemin[j];
                                        meilleurChemin[2] = Chemin[k];
                                        meilleurChemin[3] = Chemin[l];
                                        meilleurChemin[4] = Chemin[m];
                                        meilleurChemin[5] = Chemin[n];
                                        meilleurChemin[6] = Chemin[o];
                                        meilleurChemin[7] = Chemin[p];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Affichage du chemin optimal
    printf("\nLe chemin le plus court est :\n");
    printf("[0, 0] -> ");

    // Affiche tous les points successivement dans le bon ordre du meilleur chemin pour avoir le chemin le plus court
    for (int i = 0; i < 8; i++)
    {
        printf("Point %d : [%d, %d] -> ", meilleurChemin[i], Coordonnee[meilleurChemin[i]][0], Coordonnee[meilleurChemin[i]][1]);
    }
    printf("[0, 0]\n");
    printf("\nDistance minimale : %.2f\n", distanceMin);
}

void affichage_grille_avec_ordre(int Coordonnee[8][2], int meilleurChemin[8])
{
    char grille[50][20][3]; // Création d'une grille étendue pour afficher 2 caractères par cellule

    // Initialisation de la grille avec des espaces vides
    for (int i = 0; i < 50; i++)
    {
        for (int j = 0; j < 20; j++)
        {
            sprintf(grille[i][j], "  "); //sprintf permet  de stocker une chaîne de caractères dans un tableau (ou une variable de type char[])
        }
    }

    // Placement des coordonnées générées dans la grille
    for (int k = 0; k < 8; k++)
    {
        int x = Coordonnee[meilleurChemin[k]][0];
        int y = Coordonnee[meilleurChemin[k]][1];
        sprintf(grille[x][y],"X%d", k); 
    }

    // Affichage de la grille
    printf("------------------------------------------------------------------------------------------------------\n");
    for (int j = 0; j < 20; j++)
    {
        printf("|");
        for (int i = 0; i < 50; i++)
        {
            printf("%s", grille[i][j]); // Affiche chaque cellule de la grille
        }
        printf("|\n");
    }
    printf("------------------------------------------------------------------------------------------------------\n");
}

// Fonction récursive pour générer les permutations et trouver le chemin le plus court
void TrouverCheminRecursif(int Coordonnee[8][2], int Chemin[], int visiter[], int niveau, double distanceTotale, double *distanceMin, int meilleurChemin[])
{

    // Si tous les points ont été ajoutés au chemin
    if (niveau == 8)
    {
        // Retour au point de départ [0, 0]
        distanceTotale += Distance(Coordonnee[Chemin[7]], (int[]){0, 0});

        // Vérifie si le chemin trouvé est plus court que le chemin minimal actuel
        if (distanceTotale < *distanceMin)
        {
            *distanceMin = distanceTotale;
            for (int i = 0; i < 8; i++) // met à jour le tableau meilleurChemin avec les indices du chemin actuel si celui-ci est plus court que le chemin précédemment trouvé.
            {
                meilleurChemin[i] = Chemin[i];
            }
        }
        return;
    }

    // Parcourt tous les points pour trouver les chemins possibles (8!)
    for (int i = 0; i < 8; i++)
    {
        // Si le point n'a pas encore été visité
        if (!visiter[i])
        {
            visiter[i] = 1;     // Marque le point (la ville) comme visité
            Chemin[niveau] = i; // Ajoute le point (la ville) au chemin

            double nouvelleDistanceTotale = distanceTotale;
            // Si c'est le premier point, calcule la distance depuis le point de départ
            if (niveau == 0) // Si le niveau est à 0 cela veut dire qu'aucun point n'a encore été ajouté au chemin
            {
                // Ajoute la distance entre le point de départ [0, 0] et le premier point du chemin
                nouvelleDistanceTotale += Distance((int[]){0, 0}, Coordonnee[i]);
            }
            else
            {
                // Ajoute la distance entre le dernier point ajouté au chemin et le point actuel
                nouvelleDistanceTotale += Distance(Coordonnee[Chemin[niveau - 1]], Coordonnee[i]);
            }

            // Appel récursif pour le niveau suivant
            TrouverCheminRecursif(Coordonnee, Chemin, visiter, niveau + 1, nouvelleDistanceTotale, distanceMin, meilleurChemin);

            // Démarque le point comme visité pour explorer d'autres chemins
            visiter[i] = 0;
        }
    }
}

// Fonction principale pour trouver le chemin le plus court
void TrouverCheminPlusCourtRecursif(int Coordonnee[8][2], int meilleurChemin[])
{
    int chemin[8];                // Tableau pour stocker le chemin actuel
    int visited[8] = {0};         // Tableau pour suivre les points visités
    double distanceMin = DBL_MAX; // Distance minimale initialisée à la valeur maximale possible pour un nombre à virgule flottante

    // Appel de la fonction récursive pour trouver le chemin le plus court
    TrouverCheminRecursif(Coordonnee, chemin, visited, 0, 0.0, &distanceMin, meilleurChemin);

    // Affichage des résultats
    printf("\nLe chemin le plus court en RECURSIF est : \n");
    printf("[0, 0] -> ");

    for (int i = 0; i < 8; i++)
    {
        printf("Point %d : [%d, %d] -> ", meilleurChemin[i], Coordonnee[meilleurChemin[i]][0], Coordonnee[meilleurChemin[i]][1]);
    }
    printf("[0, 0]\n");
    printf("\nDistance minimale : %.2f\n", distanceMin);
}


int main()
{
    int Coordonnee[8][2]; // Tableau permettant de stocker les coordonnées des points
    double Matrice[8][8]; // Tableau pour stocker la matrice des distances
    int meilleurChemin[8];

    genererCoordonnees(Coordonnee); // Appel de la fonction pour générer et afficher les coordonnées

    printf("\n");

    affichage_grille(Coordonnee); // Appel de la fonction pour afficher la grille avec les coordonnées

    MatriceDistances(Coordonnee, Matrice); // Permet de Générer la matrice des distances

    AfficherMatriceDistances(Matrice); // Permet d'afficher la matrice des diférentes distances

    // CheminsPossibles();     // Génération et affichage de tous les chemins possibles

    // La fonction CheminsPossibles est en commentaire car il y a 8! possibilités soit 40 320. Donc si on l'affiche dans le terminal, on ne voit plus les fonctions précédentes

    TrouverCheminPlusCourt(Coordonnee); //Permet de trouver le chemins le plus court possible

    //Même fonction que la fonction ci-dessus mais en récursif
    // Stockage du meilleur chemin pour pouvoir l'afficher
    TrouverCheminPlusCourtRecursif(Coordonnee, meilleurChemin); 

    // Affichage de la grille avec l'ordre des points selon le meilleur chemin
    affichage_grille_avec_ordre(Coordonnee, meilleurChemin);

    return 0;
}
