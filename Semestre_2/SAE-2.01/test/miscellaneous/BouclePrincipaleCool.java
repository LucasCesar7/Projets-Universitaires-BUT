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

import main.BouclePrincipale;
import controle.ControleurClavier;
import main.JeuPhysique;
import static main.JeuPhysique.*;
import static physique.Collision.typeOfCollision;

/**
 * 
 * 
 * @author Pierre-Frederic Villard
 */
public class BouclePrincipaleCool extends BouclePrincipale{
    
        public BouclePrincipaleCool() throws Exception{
            //creation du jeu
	    //jeuPhysique = new JeuPhysique();
        }
    
    @Override
    public void lanceBouclePrincipale() throws Exception {
		

		//ControleurClavier cClavier=new ControleurClavier(true);
		jeuPhysique.affiche.addKeyListener(cClavier);
		//mettre l'acces au controleur dans monde
		jeuPhysique.moteurPhys.monde.c=cClavier.c;
		
		//afficher
		System.out.println("\n\n**************************************************");
		System.out.println("*  "+nom);
		System.out.println("*                                                 *");
		System.out.println("*** touche 'Q' pour arreter jeu                  ***");
		System.out.println("****************************************************");
		System.out.println("\n\n");
		
		//fps
		long dureeBoucle=1000000/fps;
		System.out.println(" ---> duree d'une boucle "+dureeBoucle/1000.);		
		
		//lancement
		Thread.sleep(1000);
		jeuPhysique.affiche.requestFocusInWindow();
		
		// boucle
		long beforeTime = System.nanoTime();
		long l = System.currentTimeMillis();
		//nombre iterations
		int n=0;
		while(!ControleurClavier.fin)
		{	
			n++;
			jeuPhysique.update();
			jeuPhysique.render();
			
                        switch (jeuPhysique.moteurPhys.monde.balle.collision)
                        {
                            case MONSTRE: System.out.println("Aie avec monstre numéro "+jeuPhysique.moteurPhys.current_monster_index);break;
                            //case HERO: System.out.println("Copain !");break;
                            //case DECORS: System.out.println("Pas mal avec mur numéro "+jeuPhysique.moteurPhys.current_wall_index);break;
                            default: break;
                        }
                        
                        
			//apres le render en nanos
			long timafter=System.nanoTime();
				
			//sleep en millisecond
			while(System.nanoTime()-beforeTime-dureeBoucle*1000L<0)
				{
				}
			beforeTime=System.nanoTime();
						
		}
		long l2 = System.currentTimeMillis();


		// statistiques
		System.out.println("\n\n\n************************\n");
		System.out.println("Iterations = "+n);
		System.out.println("FPS = " + (n * 1000.0 / (l2 - l)));
		System.out.println("\n************************");

		
		System.exit(0);
	}

}
