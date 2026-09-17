#include "stack.h"
#include <stdlib.h>
#include <stdio.h>
#include "location.h"

// Constante représentant une pile vide
const Bool EMPTY_STACK = vrai;

Stack StackNew(){
    return NULL;
}

// Vérifie si la pile est vide
Bool StackIsEmpty(Stack stack){
    if (stack == NULL)
    {
        return(EMPTY_STACK); // La pile est vide si elle est égale à empty_stack
    }
    return faux;

}

// Insère une Location sur la pile et retourne la nouvelle pile
Stack StackPush(Stack stack, Location *location){

    // Créer une nouvelle cellule de pile
    StackCell *newCell;
    newCell = malloc(sizeof(*newCell));

    if (newCell == NULL) {
        printf("Problème d'allocation dynamique");    // En cas d'erreur d'allocation mémoire, on quitte le programme
        exit(EXIT_FAILURE); // Permet de quitter le problème
    }

    // Initialiser la cellule avec le pointeur vers la Location et le pointeur vers la pile actuelle
    newCell->location = location;
    newCell->next = stack;

    // Retourner la nouvelle pile
    return newCell;
}

// Renvoie le pointeur vers la Location en tête de la pile
Location *StackHead(Stack stack){
    if (StackIsEmpty(stack)) {
        printf("Aucun sommet, la pile est vide\n"); // Si la pile est vide, renvoyer NULL
        return (Location *)NULL; //renvoyer NULL
    }
    return stack->location; // La première cellule de la pile contient la Location
}

// Détruit la première cellule de la pile et renvoie le reste de la pile
Stack StackPop(Stack stack){
    if (StackIsEmpty(stack)) {
        return StackNew(); // Si la pile est vide, retourner une pile vide
    }

    // Conserver la cellule à retirer
    StackCell *topCell;
    topCell = stack->next;

    // Libérer la cellule retirée sans supprimer la Location
    free(topCell);

    // Retourner la nouvelle pile
    return stack;
}

void StackPrint(Stack stack){
    if (StackIsEmpty(stack))
    {
        printf("La pile est vide");
        return;
    }
    printf("\n La pile contient des éléments : \n");
    while (!StackIsEmpty(stack))
    {
        printf("[%s]\n",stack->location->name);
        stack = stack->next;
    }

}