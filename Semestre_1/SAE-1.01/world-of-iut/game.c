#include "game.h"
#include "mobile.h"
#include "location.h"
#include "cmd.h"
#include "stack.h"
#include <stdlib.h> /* malloc, free, NULL */
#include <stdio.h>
#include <string.h>
#include "exits.h" // Pour la fonction strtodir et les définitions des directions

/* Initialize everything that makes up the game: player and locations */
Game *GameInit()
{
    Game *ret = malloc(sizeof(Game));
    if (ret)
    {
        // Création du joueur
        ret->player = MobileNew("Moi", "Un jeune homme très dynamique et aventureux!");

        // Initialisation des Localisations et stockage dans la pile 
        ret->locationlist = LocationInit();

        // Association du joueur à la Location de départ
        MobileMove(ret->player, StackHead(ret->locationlist));
    }
    return ret;
}

/* correctly deallocate everythig that was dynamically allocated in GameInit */
Game *GameShutdown(Game *g)
{
    if (g)
    {
        // Suppression du joueur
        g->player = MobileDelete(g->player);

        // Destruction de la pile ds locations
        g->locationlist = LocationDestroy(g->locationlist);

        free(g);
    }
    return (Game *)NULL;
}