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

package controle;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;


import physique.ObjetHeros;

//permet de faire un controleur de clavier

/**
 *
 * @author Pierre-Frederic Villard
 */
public class ControleurClavier implements KeyListener {

	// fin du jeu

	/**
	 *
	 */
	public static boolean fin = false;
	private ObjetHeros heros;

	// afficheur
	boolean affiche = false;
	AfficheControle afficheur;

	// la variable de controle

	/**
	 *
	 */
	public Controle c;

	// constructeur avec affichage du controleur ou non.

	/**
	 *
	 * @param affiche
	 */
	public ControleurClavier(boolean affiche) {
		c = new Controle();
		this.affiche = affiche;
		if (affiche)
			afficheur = new AfficheControle(c);
	}

	public void setHeros(ObjetHeros heros) {
		this.heros = heros;
	}

	@Override
	public void keyTyped(KeyEvent e) {
		// vide
	}

	@Override
	public void keyPressed(KeyEvent e) {
		// touche gauche
		if (e.getKeyCode() == KeyEvent.VK_LEFT) {
			c.gauche = true;
		}
		// touche droite
		if (e.getKeyCode() == KeyEvent.VK_RIGHT) {
			c.droite = true;
		}
		// touche up
		if (e.getKeyCode() == KeyEvent.VK_UP) {
			c.haut = true;
		}
		// touche down
		if (e.getKeyCode() == KeyEvent.VK_DOWN) {
			c.bas = true;
		}
		// touche up
		if (e.getKeyCode() == KeyEvent.VK_P) {
			fin = true;
		}

		if (affiche)
			afficheur.dessin();

		// attaque
		if (e.getKeyCode() == KeyEvent.VK_NUMPAD0) {
			c.attaque_coup_poing = true;

		}
		;

		if (e.getKeyCode() == KeyEvent.VK_NUMPAD1) {
			c.attaque_coup_pied = true;


		}

		// Defense
		if (e.getKeyCode() == KeyEvent.VK_NUMPAD2) {
			c.position_defense = true;
		}
	}

	@Override
	public void keyReleased(KeyEvent e) {
		// touche gauche
		if (e.getKeyCode() == KeyEvent.VK_LEFT) {
			c.gauche = false;
		}
		// touche droite
		if (e.getKeyCode() == KeyEvent.VK_RIGHT) {
			c.droite = false;
		}
		// touche up
		if (e.getKeyCode() == KeyEvent.VK_UP) {
			c.haut = false;
		}
		// touche down
		if (e.getKeyCode() == KeyEvent.VK_DOWN) {
			c.bas = false;
		}
		if (affiche)
			afficheur.dessin();

		if (e.getKeyCode() == KeyEvent.VK_NUMPAD0)
			c.attaque_coup_poing = false;

		if (e.getKeyCode() == KeyEvent.VK_NUMPAD2) {
			c.position_defense = false;
		}

		if (e.getKeyCode() == KeyEvent.VK_NUMPAD1) {
			c.attaque_coup_pied = false;
		}

	}

}
