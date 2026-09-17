#ifndef GAME_H
#define GAME_H

#include "mobile.h"
#include "location.h"
#include "stack.h"
#include "exits.h" //Inclus les définitions des directions et de la fonction strtodir

typedef struct
{
    Mobile *player;     // Joueur du jeu
    Location *location; // Location initial
    Stack locationlist; // Liste des locations
} Game;

extern Game *GameInit();
extern Game *GameShutdown(Game *g);
#endif // GAME_H
