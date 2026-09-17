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

import afficheur.Repere;

//un objet de type mur

/**
 *
 * @author Pierre-Frederic Villard
 */
public class ObjetMur extends Objet {

    /**
     *
     */
    public ObjetMur() {
		// taille de mur diff�rente
		height = 50;
		width = 50;
		px = 100;
		py = 20;
		sauveAnterieur();
	}

    /**
     *
     * @param x
     * @param y
     * @param w
     * @param h
     */
    public ObjetMur(int x, int y, int w, int h) {
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
		// else
		// 	g.setColor(Color.gray);
		// int[] tab =Repere.changeRepere(this);
		// g.fillRect(tab[0], tab[1], tab[2], tab[3]);
	}

}
