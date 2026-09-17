/* ========================================================== */
 /*                  Bibliotheque MoteurDeJeu                  */
 /* --------------------------------------------               */
 /* Bibliotheque pour aider la création de jeu video comme :   */
 /* - Jeux de role                                             */
 /* - Jeux de plateforme                                       */
 /* - Jeux de combat                                           */
 /* - Jeux de course                                           */
 /* - Ancien jeu d'arcade (Pac-Man, Space Invider, Snake, ...) */
 /* ========================================================== */
package miscellaneous;

import controle.Controle;
import controle.ControleurClavier;

import static main.JeuPhysique.HERO;
import static main.JeuPhysique.MONSTRE;
import physique.Collision;
import physique.MoteurPhysique;
import physique.Objet;
import physique.ObjetHeros;
import physique.ObjetMonstre;

/**
 *
 * @author Pierre-Frederic Villard
 */
public class MoteurPourDeux extends MoteurPhysique{
    
    public MondePourDeux monde;
	public int current_wall_index=0;

    
    public MoteurPourDeux() {
	
    }
    public void update() {
            
        
        Controle[] controle={monde.c,monde.c2};
        
        for (int index=0;index<2;index++){
            
            //index=1;
            ObjetHeros hero=monde.heros.get(index);
            Controle c=controle[index];
            
            hero.collision=0;
        
		// mise a jour des objets
		for (Objet o : monde.objets) {
			o.update();
			o.collision=0;
                        		}

		// gestion du controleur
		if (controle[index].gauche)
			{
			if (hero.ovx==0)
			{
				hero.sprites.changeEtape("course");
			}
			hero.ax = -0.1;
			if (hero.vx<-2)
					hero.vx=-2;
			
			}
			
		else if (controle[index].droite)
		{
			if (hero.ovx==0)
			{
				hero.sprites.changeEtape("course");
			}
			hero.ax = 0.1;
			if (hero.vx>2)
					hero.vx=2;
			
			}
		else
		{
			if ((hero.vx<0.2)&&(hero.vx>-0.2))
			{
				hero.vx=0;
				hero.ax=0;
				hero.sprites.changeEtape("fixe");
			}
			else
			if (hero.vx>0) hero.ax = -0.1;
			else
			if (hero.vx<0) hero.ax = +0.1;
		}
		
                
		//gestion des sauts
                if (gravity)
                {        
                    if ((controle[index].haut)&&(!controle[index].enAir))
                    {
                            hero.sprites.changeEtape("saut");
                            hero.vy=3;
                            hero.ay=gravityValue;
                            controle[index].enAir=true;
                    }
                }
                else
                {
                    hero.ay = 0;
                    hero.vy = 0;
                    if (controle[index].haut)
                    {
                        hero.vy = 1;
                    }
                    if (controle[index].bas)
                    {
                        hero.vy = -1;
                    }                                      
                }
		

		// mise a jour de la balle
		hero.update();


				// test de collision pour chaque mur
		for (Objet obj : monde.objets) {

			if (Collision.collision(hero, obj)) {
				//si collision vient du haut
				if (Collision.collisionHaut(hero,obj))
				{
					hero.py = hero.py - hero.vy;
					hero.vy=-1;
					if (controle[index].enAir)
						{
						controle[index].enAir=false;
						if (hero.vx==0)
							{
							hero.sprites.changeEtape("fixe");
							}
						else
							hero.sprites.changeEtape("course");
						}
					
				}

				//si collision vient du Bas
				if (Collision.collisionBas(hero,obj))
				{
                                   
					hero.py = hero.py - hero.vy;
					hero.vy=-hero.vy;;
				}
                                
                                //si collision vient de la gauche ou droite
				if (Collision.collisionGauche(hero,obj)
						|| (Collision.collisionDroite(hero,obj)))
				{	hero.px = hero.px - hero.vx;
					hero.vx-=hero.ax;
					hero.vx = -hero.vx;
				}   
                                current_wall_index=obj.index;
			}
                                              
		}
                // Assign the last collision type if not a monster
                if (hero.collision==0)
                   hero.collision=Collision.typeOfCollision;

	}


		for (ObjetHeros hero : monde.heros) {

			hero.evolue(monde.heros.get(0), monde.heros.get(1));
			if (Collision.typeOfCollision==HERO)
			{
				monde.balle.collision=HERO;
				current_wall_index=hero.index;
			}
		}



	}
}
