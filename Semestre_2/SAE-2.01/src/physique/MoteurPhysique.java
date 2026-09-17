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

package physique;

import java.io.IOException;
import static main.JeuPhysique.*;

//permet de g�rer la physique

/**
 *
 * @author Pierre-Frederic Villard
 */
public class MoteurPhysique {

	// la liste des objets dans le monde

    /**
     *
     */
	public Monde monde;

        
        public boolean gravity=true;
        
        public float gravityValue=-0.05f;
        
        public int current_monster_index=0;
        public int current_wall_index=0;
    /**
     * Construit un moteur par defaut
     * @throws IOException
     */
	public MoteurPhysique() {
	}

	// met a jour le monde

    /**
     *
     */
	public void update(){






		monde.balle.collision=0;
		// mise a jour des objets
		for (Objet o : monde.objets) {
			o.update();
			o.collision=0;
                        		}

		// mise a jour des monstres
		for (ObjetMonstre monstre : monde.monstres) {


			monstre.evolue();
                        if (Collision.typeOfCollision==MONSTRE)
                        {
                            monde.balle.collision=MONSTRE;
                            current_monster_index=monstre.index;
                        }
		}

		// gestion du controleur
		if (monde.c.gauche)
			{
			if (monde.balle.ovx==0)
			{
				monde.balle.sprites.changeEtape("course");
			}
			monde.balle.ax = -0.1;
			if (monde.balle.vx<-2)
					monde.balle.vx=-2;

			}

		else if (monde.c.droite)
		{
			if (monde.balle.ovx==0)
			{
				monde.balle.sprites.changeEtape("course");
			}
			monde.balle.ax = 0.1;
			if (monde.balle.vx>2)
					monde.balle.vx=2;

			}
		else
		{
			if ((monde.balle.vx<0.2)&&(monde.balle.vx>-0.2))
			{
				monde.balle.vx=0;
				monde.balle.ax=0;
				monde.balle.sprites.changeEtape("fixe");
			}
			else
			if (monde.balle.vx>0) monde.balle.ax = -0.1;
			else
			if (monde.balle.vx<0) monde.balle.ax = +0.1;
		}


		//gestion des sauts
                if (gravity)
                {
                    if ((monde.c.haut)&&(!monde.c.enAir))
                    {
                            monde.balle.sprites.changeEtape("saut");
                            monde.balle.vy=3;
                            monde.balle.ay=gravityValue;
                            monde.c.enAir=true;
                    }
                }
                else
                {
                    monde.balle.ay = 0;
                    monde.balle.vy = 0;
                    if (monde.c.haut)
                    {
                        monde.balle.vy = 1;
                    }
                    if (monde.c.bas)
                    {
                        monde.balle.vy = -1;
                    }
                }


		// mise a jour de la balle
		monde.balle.update();

		// test de collision pour chaque mur
		for (Objet obj : monde.objets) {

			if (Collision.collision(monde.balle, obj)) {
				//si collision vient du haut
				if (Collision.collisionHaut(monde.balle,obj))
				{
					monde.balle.py = monde.balle.py - monde.balle.vy;
					monde.balle.vy=-1;
					if (monde.c.enAir)
						{
						monde.c.enAir=false;
						if (monde.balle.vx==0)
							{
							monde.balle.sprites.changeEtape("fixe");
							}
						else
							monde.balle.sprites.changeEtape("course");
						}

				}

				//si collision vient du Bas
				if (Collision.collisionBas(monde.balle,obj))
				{

					monde.balle.py = monde.balle.py - monde.balle.vy;
					monde.balle.vy=-monde.balle.vy;;
				}

                                //si collision vient de la gauche ou droite
				if (Collision.collisionGauche(monde.balle,obj)
						|| (Collision.collisionDroite(monde.balle,obj)))
				{	monde.balle.px = monde.balle.px - monde.balle.vx;
					monde.balle.vx-=monde.balle.ax;
					monde.balle.vx = -monde.balle.vx;
				}
                                current_wall_index=obj.index;
			}

		}
                // Assign the last collision type if not a monster
                if (monde.balle.collision==0)
				{
                   monde.balle.collision=Collision.typeOfCollision;

				}



	}



}
