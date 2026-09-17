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

import afficheur.Repere;
import java.io.IOException;
import physique.Monde;

/**
 *
 * @author Pierre-Frederic Villard
 */
public class MonMondeCool extends Monde{
    	public MonMondeCool() throws IOException
	{
		//getsion du controleur
		//balle=new ObjetHeros();
                balle=new HeroCarreQuandTouche();
            
		
		//gere la vision subjective
		Repere.h=balle;
                
		
	}
}
