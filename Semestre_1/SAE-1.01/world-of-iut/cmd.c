#include "cmd.h"
#include "mobile.h"  /* Mobile type for player management */
#include "exits.h"   /* getDir and DIRECTION type */
#include <string.h>  /* strtok, strlen */
#include <strings.h> /* strcasecmp */
#include <stdio.h>   /* printf and stdin */
#include <stdlib.h>  /* exit and free */

static const char prompt[] = "-->";
static const char sep[] = "     \n";

/* react to command "help": print the help message and return */
void cmdHelp()
{
    printf(
        "\n Aide générale \n"
        "Pour jouer, il suffit d’entrer des commandes après l’invite '-->' \n"
        "Les commandes disponibles sont : \n"
        "- help : obtenir cette aide \n"
        "- quit : quitter ce jeu \n"
        "- look <direction> : pour regarder des lieux, des objets, etc. Utilisez 'look autour' pour regarder la salle actuelle, look'moi' pour vous regarder \n"
        "   'look <direction>' pour regarder dans la direction donnée (par exemple: 'look est' pour \n"
        "   regardez vers l’est de la salle actuelle). Les directions autorisées sont nord, est, \n"
        "   sud, ouest, haut et bas.  \n"
        "- go <direction> : pour aller dans la direction donnée.  \n"
        "Les directions possibles sont les mêmes que pour la commande look, c'est à dire : nord, est, sud, ouest, haut et bas \n"
        "\n BONNE PARTIE !!!\n");
}

/* Réagit à la commande "quit": termine correctement le jeu en utilisant GameShutdown et exit */
void cmdQuit(Game *game, int *running)
{
    printf("Quitter le jeu...\n");
    *running = 0; // Mettre à jour l'indicateur pour arrêter la boucle principale
}

/* Réagit à la commande "look". Ce qu'il faut regarder est fourni par args. Cela peut être :
 - moi : regarde vous-mêmes
 - autour : regarde l'endroit où vous êtes
 - <direction> : jette un coup d'œil dans la direction donnée
 - sinon, affiche simplement "You look at <args>"
 */

void cmdLook(Game *game, char *args)
{
    Direction dir = strtodir(args); // Convertion de la direction
    if (!args || strlen(args) == 0) // Vérifie si les arguments sont nuls ou vides
    {
        printf("Vous devez fournir un élément à regarder (cf 'Help')\n"); // Affiche un message d'erreur si aucun argument n'est passé
        return;                                                           // sortie de la fonction
    }

    Mobile *player = game->player; // Récupère le joueur du jeu
    if (!strcasecmp(args, "moi"))
        MobilePrint(player);              // Vérifie si l'argument est "moi" et affiche les informations du joueur
    else if (!strcasecmp(args, "autour")) // Vérifie si l'argument est "autour"

        // Affiche les informations sur la localisation courante du joueur
        if (player->location)
        {
            printf("Localisation actuelle : ", player->location);
            LocationPrint(player->location);
        }
        else
        {
            printf("Vous êtes nulle part ! Vous (Dieu) devriez créer des localisations...\n");
        }

    else if (player->location->directions[dir] != NULL && dirtostr(dir) != NULL)
    {
        Location *temp = player->location->directions[dir];  // récupération du nom de la cellule ciblé
        printf("Vous semblez apercevoir %s \n", temp->name); // affichage du nom
    }

    else
        printf("Vous regardez %s. Eh bien, il n'y a rien là. Vous (Dieu) devriez créer des localisations...\n", args); // default message
}

/* Réagit à la commande "go". La direction à suivre est donnée par args */
void cmdGo(Game *game, char *args)
{
    if (!args || strlen(args) == 0) // Vérifie si des arguments sont passés
    {
        printf("Vous devez fournir une direction pour bouger (cf 'Help')\n");
        return;
    }

    Direction dir = strtodir(args); // Convertion de l'argument en direction
    Location *currentLocation = game->player->location;
    Location *nextlocation = currentLocation->directions[dir];

    if (dir == WRONGDIR) // Vérifie si la direction est valide
    {
        printf("Vous ne pouvez pas aller dans cette direction : %s\n, args");
        return;
    }
    else if (nextlocation) // Si la prochaine localisaion existe
    {
        MobileMove(game->player, nextlocation);
        printf("Vous vous déplacez vers %s\n", args);
        printf("Localisation actuelle : ", nextlocation);
        LocationPrint(nextlocation);
    }

    else
    {
        printf("Il n'y a pas d'issues vers %s\n", args);
    }
}

/* Lit une ligne de l'entrée standard et la retourne.
La chaîne retournée est allouée en utilisant malloc et doit être libérée avec free */

char *getInput()
{
    char *line = NULL;
    size_t linecap = 0;
    printf("%s ", prompt);           /* Affiche le prompt avant de lire la ligne */
    getline(&line, &linecap, stdin); /* Alloue dynamiquement la mémoire pour stocker la ligne */
    return line;
}

/* Analyse la ligne d'entrée en deux mots : le premier mot est la commande et le second est l'argument
La ligne et le jeu doivent être des pointeurs non nuls pour que quelque chose se passe */

void parseAndExecute(char *line, Game *game, int *running)
{
    if (line && game) // Vérifie la validité des pointeurs
    {
        char *name = strtok(line, sep); /* Extrait le premier mot = commande */
        if (name)                       // Pointeur nul si la ligne contient juste un retour (ligne vide)
        {
            char *args = strtok(NULL, sep); /* extract the second word = argument. The rest of the line is disregarded */
            /* call the appropriate function, depending on the command */
            if (!strcasecmp(name, "help"))
                cmdHelp(); /* cmd=="help" -> appel cmdHelp() */
            else if (!strcasecmp(name, "quit"))
                cmdQuit(game, running); /* cmd=="quit" -> appel cmdQuit(). Termine correctement le jeu */
            else if (!strcasecmp(name, "look"))
                cmdLook(game, args); /* cmd=="look" -> appel cmdLook(). args spécifie où/quand regarder. game contient les infos nécessaires */
            else if (!strcasecmp(name, "go"))
                cmdGo(game, args); /* cmd="go" -> appel cmdGo(). args spécifie où aller. game contient les infos sur les localisations et met à jour la localisation du joueur */
            else
                printf("Command not found. Try 'help'\n");
        }
        else
            printf("Command not found. Try 'help'\n");
    }
}

/* La fonction processCommand lit une ligne de commande de l'utilisateur et la traite pour progresser dans le jeu */
void processCommand(Game *g, int *running)
{
    // Appelle la fonction getInput pour lire une ligne de commande de l'utilisateur
    // La fonction getline (appelée dans getInput) alloue dynamiquement de la mémoire pour stocker cette ligne
    char *s = getInput();

    // Appelle la fonction parseAndExecute pour traiter la ligne de commande et faire avancer le jeu
    // La fonction parseAndExecute reçoit la ligne de commande, le jeu (game) et un indicateur (running) pour gérer la boucle principale
    parseAndExecute(s, g, running); /* process it, and move in the game */

    // Vérifie si la ligne de commande (s) n'est pas NULL
    if (s)

        // Libère la mémoire allouée pour la ligne de commande
        // Cela évite les fuites de mémoire en s'assurant que la mémoire allouée par getline est correctement désallouée
        free(s);
}
