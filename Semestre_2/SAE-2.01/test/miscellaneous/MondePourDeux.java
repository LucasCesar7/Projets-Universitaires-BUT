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
import controle.Controle;
import java.io.IOException;
import physique.Monde;
import physique.ObjetHeros;

/**
 *
 * @author Pierre-Frederic Villard
 */
public class MondePourDeux extends Monde{
    
    public Controle c2;
    
    public MondePourDeux() throws IOException
	{
		
	}    
}
