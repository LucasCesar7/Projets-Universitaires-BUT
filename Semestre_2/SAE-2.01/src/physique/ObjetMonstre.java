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

import java.awt.Color;
import java.awt.Graphics;
import java.io.IOException;

import afficheur.Repere;
import afficheur.Sprites;
import afficheur.SpritesMonstre;

//gere l'IA

/**
 *
 * @author Pierre-Frederic Villard
 */

public class ObjetMonstre extends Objet {

	// etat interne
	enum Etat {
		PROMENE, ATTAQUE, COLLISION;
	}
	
	SpritesMonstre sprite;

	// son etat
	Etat etat = Etat.PROMENE;


	// lien vers le monde
	Monde m;
       
	// par defaut

    /**
     *
     * @throws IOException
     */
	public ObjetMonstre() throws IOException {
		// depart
		ax = 0;
		ay = 0;
		px = 200;
		py = 0;
		vx = 0.5;
		vy = 0;
		height = 30;
		width = 20;
		
		sprite=new SpritesMonstre();
	}

	// dessine un monstre

    /**
     *
     * @param g
     */
	public void draw(Graphics g) {
		
		// change de repere
		int[] tab = Repere.changeRepere(this);
		
		if (etat == Etat.ATTAQUE)
			g.setColor(Color.red);
		if (etat == Etat.PROMENE)
			g.setColor(Color.green);
		if (etat == Etat.COLLISION)
			g.setColor(Color.blue);

		//g.fillOval(tab[0], tab[1], tab[2], tab[3]);
		//g.setColor(Color.black);
		//g.fillOval(tab[0]+3, tab[1]+5, 5, 5);
		//g.fillOval(tab[0]+12, 5+tab[1], 5, 5);
		//g.drawLine(tab[0]+ 8, 15 + tab[1], tab[0]+12, 15 + tab[1]);
		
		sprite.affiche(tab[0],tab[1],g);
		sprite.anime();
	}

    /**
     *
     */
    public void evolue() {
		
		if (Collision.collision(m.balle,this))
			etat=Etat.COLLISION;
		
		// en fonction de l'�tat interne
		switch (etat) {
		// si se promene
		case PROMENE:
			// mise � jour
			update();
			// si rencontre un mur, change vitesse
			boolean collision = false;
			for (Objet objet : m.objets) {
				// si collision
				if (Collision.collision(this, objet)) {
					collision = true;
					break;
				}
			}
			// si collision inverse vitesse
			if (collision) {
				px = px - vx;
				vx = -vx;
				if (vx>0) sprite.changeEtape("courseDroite");
				if (vx<0) sprite.changeEtape("courseGauche");
			}

			// test si passe en attaque
			if ((px - m.balle.px < 200) && (px - m.balle.px > -200))
			{
				etat = Etat.ATTAQUE;
				if (vx>0) sprite.changeEtape("volg");
				if (vx<0) sprite.changeEtape("vold");
			}
				
			break;

		case ATTAQUE:
			
			
			// chaneg direction
			if (px < m.balle.px)
			{
				vx = 0.8;
				if (ovx!=vx)
				{
					sprite.changeEtape("vold");
				}
			}
			else
			{
				vx = -0.8;
				if (ovx!=vx)
				{
					sprite.changeEtape("volg");
				}
			}
			// update
			update();
			// si rencontre un mur, change vitesse
			collision = false;
			for (Objet objet : m.objets) {
				// si collision
				if (Collision.collision(this, objet)) {
					collision = true;
					
					break;
				}
			}
			// si collision inverse vitesse
			if (collision) {
				px = px - vx;
			}

			// si loin, promene
			if ((px - m.balle.px >200) || (px - m.balle.px < -200)) {
				etat = Etat.PROMENE;
				vx = 0.5;
			}

			break;
		case COLLISION:
			if (!Collision.collision(m.balle,this))
				etat=Etat.ATTAQUE;
			break;
		}
	}

}
