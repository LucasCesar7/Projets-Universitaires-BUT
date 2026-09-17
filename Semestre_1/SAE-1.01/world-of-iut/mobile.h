#ifndef MOBILE_H
#define MOBILE_H
#include "location.h"

typedef struct 
{
    char *name;
    char *desc;
    Location *location;
} Mobile;

extern Mobile *MobileNew(char *name, char *desc);
extern Mobile *MobileDelete(Mobile *m);
extern void MobilePrint(Mobile *m);
extern void MobileMove(Mobile *m, Location *loc); //Déclaration de la fonction MobileMove
#endif // MOBILE_H