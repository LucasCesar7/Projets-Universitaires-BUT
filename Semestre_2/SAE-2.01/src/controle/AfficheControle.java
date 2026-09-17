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

import java.awt.Color;
import java.awt.Dimension;
import java.awt.Graphics;

import javax.swing.JFrame;
import javax.swing.JPanel;

/**
 * permet d'afficher le controleur
 * 
 * @author vthomas
 * 
 */
public class AfficheControle {

	// panel du controleur
	JPanel p = new JPanel();
	Controle c;

	// constructeur
	// creer une JFrame

    /**
     *
     * @param c
     */
	public AfficheControle(Controle c) {
		JFrame f = new JFrame("controleur");
		f.setLocation(600, 00);
		p.setPreferredSize(new Dimension(100, 100));
		f.setContentPane(p);

		// lien vers le Controle
		this.c = c;
		
		
		f.pack();
		f.setVisible(true);
		dessin();
	}
	

	// permet de dessiner bourinnement

    /**
     *
     */
	public void dessin() {
		Graphics g = p.getGraphics();
		g.setColor(Color.lightGray);
		g.fillRect(0, 0, 100, 100);

		// affiche des commandes
		g.setColor(Color.black);
		if (c.gauche)
			g.fillRect(10, 60, 20, 20);
		else
			g.drawRect(10, 60, 20, 20);

		if (c.droite)
			g.fillRect(70, 60, 20, 20);
		else
			g.drawRect(70, 60, 20, 20);
		
		if (c.haut)
			g.fillRect(40, 20, 20, 20);
		else
			g.drawRect(40, 20, 20, 20);
	}

}
