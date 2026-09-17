all: WorldOfIUT

exits.o: exits.h
cmd.o: cmd.h game.h exits.h mobile.h
mobile.o: mobile.h exits.h
location.o: location.h exits.h
stack.o: stack.h location.h exits.h
game.o: game.h mobile.h exits.h
WorldOfIUT.o: cmd.h exits.h mobile.h game.h
WorldOfIUT: WorldOfIUT.o cmd.o mobile.o game.o exits.o location.o stack.o
clean:
	/bin/rm *.o WorldOfIUT
