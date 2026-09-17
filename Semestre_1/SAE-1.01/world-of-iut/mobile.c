#include "mobile.h"
#include "location.h"
#include <string.h> /* strdup */
#include <stdio.h> /* printf */
#include <stdlib.h> /* malloc, free */

//Fonction pour créer un nouveau mobile
Mobile *MobileNew(char *name, char *desc)
{
    Mobile *ret=(Mobile*)NULL; //Initialisation du mobile à NULL
    if (name && desc) //Si le nom et la description sont valides
    {
        ret=malloc(sizeof(Mobile)); //Allouer de la mémoire pour le mobile 
        ret->name=strdup(name); //Duplication du nom
        ret->desc=strdup(desc); //Duplication de la description
        ret->location = (Location*)NULL; // Initialisation de la localisation à NULL
    }
    return ret;
}

//Fonction pour supprimer le mobile
Mobile *MobileDelete(Mobile *m)
{
    if (m) //Si le mobile est valide
    {
        if (m->name) free(m->name); // Libérer la mémoire allouée pour le nom
        if (m->desc) free(m->desc); // Libérer la mémoire allouée pour la description
        free(m); //Libérer la mémoire pour le mobile
    }
    return (Mobile *)NULL;
}

//Fonction permettant d'afficher les détails du mobile
void MobilePrint(Mobile *m)
{
    if (m) printf("%s\n%s\n",m->name,m->desc); //Si le mobile est valide, afficher nom et description
}

//Fonction pour déplacer un mobile
void MobileMove(Mobile *m, Location *loc)
{
    if (m && loc) //Si le mobile et la localisation sont valides
    {
        m->location = loc; // Mettre à jour la localisation du mobile
    }
}