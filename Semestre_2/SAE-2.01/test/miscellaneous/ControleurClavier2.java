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

import controle.ControleurClavier;
import physique.ObjetHeros;

import java.awt.event.KeyEvent;


/**
 *
 * @author Pierre-Frederic Villard
 */
public class ControleurClavier2 extends ControleurClavier{

    public ControleurClavier2(boolean affiche) {
        super(affiche);
    }
	private ObjetHeros heros;

	public void setHeros(ObjetHeros heros) {
		this.heros = heros;
	}

    /**
     *
     * @param affiche
     */      
    @Override
	public void keyPressed(KeyEvent e) {
		//touche gauche
		if (e.getKeyCode()==KeyEvent.VK_Q)
		{
			c.gauche=true;			
		}
		//touche droite
		if (e.getKeyCode()==KeyEvent.VK_D)
		{
			c.droite=true;
		}
		//touche up
		if (e.getKeyCode()==KeyEvent.VK_Z)
		{
			c.haut=true;
		}
		//touche down
		if (e.getKeyCode()==KeyEvent.VK_S)
		{
			c.bas=true;
		}		
		//touche up
		if (e.getKeyCode()==KeyEvent.VK_P)
		{
			fin=true;
		}

		// attaque
		if (e.getKeyCode()==KeyEvent.VK_T){

			c.attaque_coup_poing = true;


		}

		if(e.getKeyCode()==KeyEvent.VK_R)
		{
			c.position_defense = true;
		}

		if(e.getKeyCode()==KeyEvent.VK_F)
		{
			c.attaque_coup_pied = true;
		}
	}

	@Override
	public void keyReleased(KeyEvent e) {
		//touche gauche
		if (e.getKeyCode()==KeyEvent.VK_Q)
		{
			c.gauche=false;
		}
		//touche droite
		if (e.getKeyCode()==KeyEvent.VK_D)
		{
			c.droite=false;
		}
		//touche up
		if (e.getKeyCode()==KeyEvent.VK_Z)
		{
			c.haut=false;
		}
                //touche down
		if (e.getKeyCode()==KeyEvent.VK_S)
		{
			c.bas=false;
		}

		if (e.getKeyCode()==KeyEvent.VK_T) c.attaque_coup_poing = false;

		if(e.getKeyCode()==KeyEvent.VK_R)
		{
			c.position_defense = false;
		}

		if(e.getKeyCode()==KeyEvent.VK_F)
		{
			c.attaque_coup_pied = false;
		}
	}
	
}
