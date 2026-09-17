#include "location.h"
#include "exits.h"
#include "stack.h"
#include <string.h> /* strdup, strcmp */
#include <stdio.h>  /* printf */
#include <stdlib.h> /* malloc, free */

//Fonction pour créer une nouvelle localisation
Location *LocationNew(char *name, char *desc)
{
    Location *ret = (Location *)NULL;
    if (name && desc) //test si le nom et la description sont valide
    {
        ret = malloc(sizeof(Location)); //Alloue de la mémoire pour la nouvelle localisation
        ret->name = strdup(name); //Duplique le nom de la localisation
        ret->desc = strdup(desc); //Duplique la description de la localisation
        for (int i = 0; i < DIRECTIONS; i++) //Initialise toutes les directions à NULL
        {
            ret->directions[i] = NULL;
        }
    }
    return ret; //Retourne le pointeur vers la nouvelle localisation
}

//Fonction pour détruire une localisation
Location *LocationDelete(Location *m)
{
    if (m) //Si lalocalisation est validée
    {
        if (m->name) // Libérer la mémoire allouée pour le nom
            free(m->name);
        if (m->desc) // Libérer la mémoire allouée pour la description
            free(m->desc);
        free(m);
    }
    return (Location *)NULL; //Retourne NULL
}

//Fonction permettant d'afficher les détails d'une localisation
void LocationPrint(Location *m)
{
    if (m) //Si la localisation est validée
    {
        printf("%s\n%s\n", m->name, m->desc); //Affiche nom et description
        for (int i = 0; i < DIRECTIONS; i++) //Parcours toutes les directions
        {
            if (m->directions[i]) //Si une direction est valide
            {
                char *directionName = dirtostr(i); // Convertir l'indice de direction en nom de direction
                if (directionName)
                {
                    printf("Il y a une issue vers %s : %s\n", directionName, m->directions[i]->name);
                }
            }
        }
    }
}

// Fonction permetant d'établir une connexion entre deux localisations
static void LocationSetExit(Location *loc, char *directions, Location *destination)
{

    if (loc == NULL || directions == NULL || destination == NULL) //Si un des paramètres est invalide
    {
        printf("INVALIDE"); //Afficher message d'erreur
        return;
    }

    int index = -1;
     // Correspondance des noms de direction avec les indices
    if (strcmp(directions, "nord") == 0)
        index = 0;
    else if (strcmp(directions, "est") == 0)
        index = 1;
    else if (strcmp(directions, "sud") == 0)
        index = 2;
    else if (strcmp(directions, "ouest") == 0)
        index = 3;
    else if (strcmp(directions, "haut") == 0)
        index = 4;
    else if (strcmp(directions, "bas") == 0)
        index = 5;

    if (index != -1) //Si l'indice est validé
    {
        loc->directions[index] = destination; //Faire la connexion entre les 2 localisations
    }
    else
    {
        printf("Direction invalide"); //Message d'erreur
    }
}

Stack LocationInit()
{
    // Création d'un Stack vide pour stocker les locations
    Stack locationStack = StackNew();
    // Création des locations puis ajout à la pile

    Location *Garage = LocationNew("Garage", "Suffisamment spacieux pour deux voitures, avec des étagères pour le rangement d’outils et un établi pour bricoler.");
    locationStack = StackPush(locationStack, Garage);

    Location *Maison = LocationNew("Maison", "Une villa moderne aux lignes épurées, avec façade en verre et bois, entourée d’un jardin, un patio, une piscine et un garage.");
    locationStack = StackPush(locationStack, Maison);

    Location *Salon = LocationNew("Salon", "Un espace chaleureux avec un grand canapé en L, une table basse en verre, un tapis douillet, une télévision murale et des étagères décorées.");
    locationStack = StackPush(locationStack, Salon);

    Location *Cuisine = LocationNew("Cuisine", "Une cuisine moderne avec des plans de travail en quartz, des appareils encastrés, un îlot central avec des tabourets, un grand réfrigérateur, et une hotte élégante.");
    locationStack = StackPush(locationStack, Cuisine);

    Location *SalleaManger = LocationNew("Salle à Manger", "Une grande table en bois entourée de chaises confortables, un lustre moderne au-dessus, et des baies vitrées donnant sur le jardin.");
    locationStack = StackPush(locationStack, SalleaManger);

    Location *ChambrePrincipale = LocationNew("Chambre Pincipale", "Un grand lit avec une tête de lit en tissu, deux tables de chevet, un dressing intégré, et des rideaux épais pour plus d’intimité.");
    locationStack = StackPush(locationStack, ChambrePrincipale);

    Location *SalleDeBain = LocationNew("Salle de bain", "Une baignoire îlot, une douche à l’italienne, un double lavabo, et des finitions en marbre.");
    locationStack = StackPush(locationStack, SalleDeBain);

    Location *ChambreSecondaire = LocationNew("Chambre secondaire", "Lit double, bureau pour étudier, petite armoire et décor minimaliste pour plus de fonctionnalité.");
    locationStack = StackPush(locationStack, ChambreSecondaire);

    Location *Bureau = LocationNew("Bureau", "Un grand bureau en bois avec une chaise ergonomique, des étagères remplies de fournitures et une grande fenêtre pour une lumière naturelle.");
    locationStack = StackPush(locationStack, Bureau);

    Location *Bibliotheque = LocationNew("Bibliothèque", "Des étagères murales remplies de livres, un fauteuil confortable pour lire, une petite table pour poser une tasse de thé.");
    locationStack = StackPush(locationStack, Bibliotheque);

    Location *SalleCinema = LocationNew("Salle de cinéma", "Un grand écran avec un projecteur, plusieurs sièges inclinables en cuir, et un éclairage tamisé pour l’ambiance.");
    locationStack = StackPush(locationStack, SalleCinema);

    Location *SalleJeux = LocationNew("Salle de jeux", "Une table de billard, des jeux d’arcade, un baby-foot, et un espace avec des consoles de jeux vidéo et un canapé.");
    locationStack = StackPush(locationStack, SalleJeux);

    Location *SalleSport = LocationNew("Salle de sport", "Équipée d’un tapis de course, de poids, d’un vélo elliptique, et de tapis pour le yoga ou les étirements.");
    locationStack = StackPush(locationStack, SalleSport);

    Location *Piscine = LocationNew("Piscine", "Une grande piscine avec des lumières LED sous l’eau, des transats au bord, et des plantes pour un décor relaxant.");
    locationStack = StackPush(locationStack, Piscine);

    Location *Cave = LocationNew("Cave", "Une cave sobre et fonctionnelle avec des murs en pierre apparente, des étagères en bois pour le rangement de bouteilles, un éclairage tamisé, et une température contrôlée pour conserver les vins.");
    locationStack = StackPush(locationStack, Cave);

    Location *startlocation = LocationNew("Route", "La route continue vers le nord et le sud. Vous pouvez voir une maison à l’ouest.");
    locationStack = StackPush(locationStack, startlocation);

    printf("\n");
    StackPrint(locationStack);

    // Établissement des connexions entre les locations

    LocationSetExit(startlocation, "ouest", Maison);
    LocationSetExit(Maison, "est", startlocation);

    LocationSetExit(Maison, "sud", Salon);
    LocationSetExit(Salon, "nord", Maison);

    LocationSetExit(Salon, "est", Piscine);
    LocationSetExit(Piscine, "ouest", Salon);

    LocationSetExit(Salon, "ouest", Cuisine);
    LocationSetExit(Cuisine, "est", Salon);

    LocationSetExit(Salon, "sud", Garage);
    LocationSetExit(Garage, "nord", Salon);

    LocationSetExit(Salon, "bas", Cave);
    LocationSetExit(Cave, "haut", Salon);

    LocationSetExit(Salon, "haut", ChambrePrincipale);
    LocationSetExit(ChambrePrincipale, "bas", Salon);

    LocationSetExit(ChambrePrincipale, "ouest", SalleSport);
    LocationSetExit(SalleSport, "est", ChambrePrincipale);

    LocationSetExit(ChambrePrincipale, "est", SalleCinema);
    LocationSetExit(SalleCinema, "ouest", ChambrePrincipale);

    LocationSetExit(ChambrePrincipale, "nord", ChambreSecondaire);
    LocationSetExit(ChambreSecondaire, "sud", ChambrePrincipale);

    LocationSetExit(ChambreSecondaire, "est", Bibliotheque);
    LocationSetExit(Bibliotheque, "ouest", ChambreSecondaire);

    LocationSetExit(Cuisine, "nord", SalleaManger);
    LocationSetExit(SalleaManger, "sud", Cuisine);

    LocationSetExit(SalleaManger, "nord", SalleDeBain);
    LocationSetExit(SalleDeBain, "sud", SalleaManger);

    LocationSetExit(SalleaManger, "ouest", Bureau);
    LocationSetExit(Bureau, "est", SalleaManger);

    return locationStack;
}

Stack LocationDestroy(Stack s)
{
    // Tant que la pile n'est pas vide faire
    while (!StackIsEmpty(s)) {

        // Détruire la location en tête de la pile
        Location *currentLocation = StackHead(s);  // Récupère la location au sommet de la pile
        if (currentLocation != NULL)
        {
            free(currentLocation);
        }
        s = StackPop(s);
    }
    // Retourner la pile vide
    return s;
}