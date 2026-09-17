#ifndef LOCATION_H
#define LOCATION_H

#define DIRECTIONS 6 // Par exemple, Nord, Sud, Est, Ouest, Haut, Bas

#include "stack.h"

typedef struct Location
{
    char *name; //Nom de la localiation
    char *desc; //Description de la localisation
    struct Location *directions[DIRECTIONS]; //Tableau de pointeurs vers les localisations
} Location;

extern Location *LocationNew(char *name, char *desc);
extern Location *LocationDelete(Location *m);
extern void LocationPrint(Location *m);
extern Stack LocationInit(); // Déclaration de la fonction pour initialiser les localisations
extern Stack LocationDestroy(Stack s);
#endif // MOBILE_H