package main;

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


import afficheur.Afficheur;
import physique.MoteurPhysique;
import java.io.IOException;

public class JeuPhysique{
	
	//le moteur physique
	public MoteurPhysique moteurPhys;
	
	//le rendu
	public Afficheur affiche;

        public final static short MONSTRE=1;
        public final static short HERO=2;
        public final static short DECORS=3;
	
	int i=0;
	
	//separation vue affichage
	public JeuPhysique()
	{
		//on creer le moteur physique
		//moteurPhys=new MoteurPhysique();
		//on creer l'afficheur du monde
		//affiche=new Afficheur(moteurPhys.monde);

	}
	
	
	public void update() throws InterruptedException {
		moteurPhys.update();
	}
	
	public void render()
	{
		affiche.render();
		
	}
}