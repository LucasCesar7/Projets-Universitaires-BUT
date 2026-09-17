#include <stdio.h> /* for printf in Intro() */
#include "game.h"  /* for Game structure and GameInit() */
#include "cmd.h"   /* for processCommand() */

void Intro()
{
    printf("Bienvenue à WorldOfIUT! \n"
           "Soyez préparé parce que vous êtes sur le point de vivre des aventures extraordinaires, effrayantes, mais passionnantes \n"
           "Si vous êtes nouveau dans le jeu, vous pouvez commencer par taper 'help' \n"
           "Adieu, et que l’esprit des Vosges soit avec vous!  \n");
}

int main(int argc, char *argv[])
{
    // Initialisation du jeu
    Intro();
    Game *g = GameInit();
    
    int running = 1; // Indicateur pour contrôler la boucle principale
    
    while (running)
        processCommand(g, &running); // Passez un pointeur vers l'indicateur à processCommand
    
    GameShutdown(g); // Assurez-vous de libérer correctement les ressources avant de quitter
    return 0;
}
