#ifndef STACK_H
#define STACK_H

struct Location;
typedef struct Location Location;

typedef enum
{
    faux,
    vrai
} Bool;

// Définition d'une cellule de la pile
typedef struct StackCell {
    struct Location *location;          // Pointeur vers une Location
    struct StackCell *next;      // Pointeur vers la cellule suivante
} StackCell;

// Définition du type Stack comme un pointeur vers la première cellule de la pile
typedef StackCell *Stack;


// Déclaration des fonctions

Stack StackNew();

// Vérifie si une pile est vide
Bool StackIsEmpty(Stack stack);

// Insère une Location sur la pile et retourne la nouvelle pile
Stack StackPush(Stack stack, Location *location);

// Renvoie le pointeur vers la Location en tête de la pile
Location *StackHead(Stack stack);

// Détruit la première cellule de la pile et renvoie le reste de la pile
Stack StackPop(Stack stack);

void StackPrint(Stack stack);

#endif
