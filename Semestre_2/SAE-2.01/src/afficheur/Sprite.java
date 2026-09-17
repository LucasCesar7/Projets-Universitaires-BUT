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

package afficheur;

/**
 *
 * @author Pierre-Frederic Villard
 */
public class Sprite {

	
	int xmin,ymin;
	int xmax,ymax;

    /**
     *
     */
    public int tx;

    /**
     *
     */
    public int ty;
	
    /**
     *
     * @param xmin
     * @param ymin
     * @param xmax
     * @param ymax
     */
    public Sprite(int xmin, int ymin, int xmax, int ymax) {
		super();
		this.xmin = xmin;
		this.ymin = ymin;
		this.xmax = xmax;
		this.ymax = ymax;
		this.tx=xmax-xmin;
		this.ty=ymax-ymin;
		
	}

}
