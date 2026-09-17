#include "exits.h"
#include <strings.h> /* strcasecmp */
#include <stddef.h> /* NULL */
#include <string.h>

static char *dir_str[]={"nord","est","sud","ouest","haut","bas"};

Direction strtodir(const char *name)
{
    for (int i=NORD; i<=BAS; i++)
        if (!strcasecmp(name, dir_str[i])) return i;
    return WRONGDIR;
}

char *dirtostr(Direction d)
{
    if (d >= NORD && d <= BAS) return dir_str[d];
    return (char *)NULL;
}