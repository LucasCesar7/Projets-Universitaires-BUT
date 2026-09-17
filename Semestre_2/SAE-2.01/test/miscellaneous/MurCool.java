package miscellaneous;

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

import physique.ObjetMur;
import java.awt.Color;
import java.awt.Graphics;
import afficheur.Repere;
/**
 *
 * @author Pierre-Frederic Villard
 */
public class MurCool extends ObjetMur{
    /**
     *
     * @param x
     * @param y
     * @param w
     * @param h
     */
    public MurCool(int x, int y, int w, int h) {
		// taille de mur diff�rente
		height = h;
		width = w;
		px = x;
		py = y;
	}

    /**
     *
     * @param g
     */
    public void draw(Graphics g) {
		if (collision==1)
			g.setColor(Color.red);
		else
			g.setColor(Color.blue);
		int[] tab =Repere.changeRepere(this);
		g.fillRect(tab[0], tab[1], tab[2], tab[3]);
	}
   
}
