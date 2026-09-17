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

import controle.Controle;
import controle.ControleurClavier;
import afficheur.FinDePartieMenu;
import afficheur.Repere;
import afficheur.Sprites;
import afficheur.SpritesHeros;

//un objet de type balle

/**
 *
 * @author Pierre-Frederic Villard
 */
public class ObjetHeros extends Objet {

	private int pv = 8000;

	// distributeur de sprite
	public Sprites sprites;

	// lien vers son controleur
	public Controle c;

	// fait une balle par d faut

	Monde m;

	/**
	 *
	 * @throws IOException
	 */
	public ObjetHeros() throws IOException {

		sprites = new SpritesHeros(this);
		height = 10;
		width = 10;
		height = sprites.sprites.get("fixe").ty;
		width = sprites.sprites.get("fixe").tx;
		vx = 1;
		vy = 3;
		ax = 0;
		ay = -0.04;
	}

	/**
	 *
	 * @throws IOException
	 */
	public ObjetHeros(int x, int y) throws IOException {
		sprites = new SpritesHeros(this);
		height = 10;
		width = 10;
		height = sprites.sprites.get("fixe").ty;
		width = sprites.sprites.get("fixe").tx;
		px = x;
		py = y;
		vx = 1;
		vy = 3;
		ax = 0;
		ay = -0.04;
	}

	/**
	 *
	 * @param g
	 */
	@Override
	public void draw(Graphics g) {
		g.setColor(Color.black);

		// Change de repère
		int[] tab = Repere.changeRepere(this);
		// sprites.affiche(tab[0], tab[1], g);
		sprites.anime();

		if (!c.attaque_coup_poing && !c.attaque_coup_pied && !c.position_defense) {
			sprites.affiche(tab[0], tab[1], g); // Affiche le sprite normal SEULEMENT si pas d'attaque et pas de défense
		}

		if (c.attaque_coup_poing) {
			((SpritesHeros) sprites).afficherAttaque(g, tab[0], tab[1], tab[2], tab[3]);

		}

		if (c.attaque_coup_pied) {
			((SpritesHeros) sprites).afficherAttaquePied(g, tab[0], tab[1], tab[2], tab[3]);
		}

		if (c.position_defense) {
			((SpritesHeros) sprites).afficherDefense(g, tab[0], tab[1], tab[2], tab[3]);
		}

		if (pv <= 0) {
			System.out.println("Un héros est KO, affichage du menu !");

			FinDePartieMenu.afficherMenu(); // Affiche le menu avant fin de partie

			try {
				// Pause pour laisser le temps au menu de s'afficher avant la fermeture
				Thread.sleep(20000);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}

			// Ferme le jeu SEULEMENT après que le joueur ait choisi
			ControleurClavier.fin = true;
		}

		int maxPv = 8000;
		int largeurBarre = 80;
		int hauteurBarre = 10;
		int pvRestant = (pv * largeurBarre) / maxPv;

		if (pvRestant > 60) {
			g.setColor(Color.green);
		} else if (pvRestant > 30) {
			g.setColor(Color.orange);
		} else {
			g.setColor(Color.red);
		}

		g.fillRect(tab[0], tab[1] - 20, pvRestant, hauteurBarre);

		g.setColor(Color.black);
		g.drawRect(tab[0], tab[1] - 20, largeurBarre, hauteurBarre);
	}

	public int getPv() {
		return pv;
	}

	public void setPv(int pv) {
		this.pv = pv;
	}

	public void retirerPv(int degats) {
		this.pv -= degats;
		if (this.pv <= 0)
			this.pv = 0;

	}

	public void evolue(ObjetHeros m, ObjetHeros l) {
		if (Collision.collision(m, l) && m.c.attaque_coup_poing && !l.c.position_defense) {
			l.retirerPv(10);

		} else if ((Collision.collision(m, l) && m.c.attaque_coup_pied && !l.c.position_defense)) {
			l.retirerPv(20);

		} else if (Collision.collision(m, l) && l.c.attaque_coup_poing && !m.c.position_defense) {
			m.retirerPv(10);

		} else if (Collision.collision(m, l) && l.c.attaque_coup_pied && !m.c.position_defense) {
			m.retirerPv(20);

		}

	}
}