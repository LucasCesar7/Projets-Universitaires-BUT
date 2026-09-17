#ifndef CMD_H
#define CMD_H

#include "game.h" /* for Game structure */
#include "exits.h" //Inclus les définitions des directions et de la fonction strtodir

extern void processCommand(Game *g, int *running);
extern void cmdGo(Game *game, char *args);

#endif // CMD_H

