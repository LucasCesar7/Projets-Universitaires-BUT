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

// permet de mod�liser un objet

/**
 *
 * @author Pierre-Frederic Villard
 */
public class Objet {
	


	// modele de l'objet
	// position

    /**
     *
     */
	public  double px = 15;

    /**
     *
     */
    public double py = 0;


	// vitesse

    /**
     *
     */
	public double vx = 0;
	public double vy = 0;
	// acceleration
	public double ax = 0, ay = 0;
	
	
	//positions precedentes (
	//utile pour colision

    /**
     *
     */
	public double opx,

    /**
     *
     */
    opy,

    /**
     *
     */
    ovx,

    /**
     *
     */
    ovy,

    /**
     *
     */
    oax,

    /**
     *
     */
    oay;

	// boundingbox

    /**
     *
     */
	public double width = 10;

    /**
     *
     */
    public double height = 10;
	
	//collision
	//permet de savoir si un objet est en collision
	//TODO debug a supprimer

    /**
     *
     */
	public int collision=0;

	//permet de sauver les positions anterieur

    // Index de l'élément parmis son type (Monstre , Mur, etc...)    
    public int index;
        
    /**
     *
     */
	public void sauveAnterieur()
	{
		opx=px;
		opy=py;
		ovx=vx;
		ovy=vy;
		oax=ax;
		oay=ay;
	}
	
	// mise � jour avec des equations physiques

    /**
     *
     */
	public void update() {
		sauveAnterieur();		
		px = px + vx;
		py = py + vy;
		vx = vx + ax;
		vy = vy + ay;
	}
	
	//permet de dessiner l'objet

    /**
     *
     * @param g
     */
	public void draw(Graphics g)
	{
		g.setColor(Color.green);
		int[]tab=Repere.changeRepere(this);
		g.fillRect(tab[0], tab[1], tab[2], tab[3]);
	}

    /**
     *
     * @return
     */
    public double getPx() {
		return px;
	}

    /**
     *
     * @return
     */
    public double getPy() {
		return py;
	}

}
