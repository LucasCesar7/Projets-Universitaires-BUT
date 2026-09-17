#ifndef EXITS_H
#define EXITS_H

/* 
    directions encoded as int. Used as indices in the array of exits for each location 
    NORTH==0
    EAST==1
    SOUTH==2
    WEST==3
    UP==4
    DOWN==5
    anything other than a correct direction can be encoded as WRONGDIR==-1
    The number of directions can be computed as DOWN-NORTH+1
    A look over all directions can be written as
    for (int i=NORTH; i<=DOWN; i++)
*/
typedef enum {WRONGDIR=-1,NORD=0,EST,SUD,OUEST,HAUT,BAS} Direction;
/* translate a string into a DIRECTION. Letter case does not matter */
extern Direction strtodir(const char *Direction);

/* return the direction as a string. Return NULL if the direction is invalid */
extern char *dirtostr(Direction d);

#endif // EXITS_H