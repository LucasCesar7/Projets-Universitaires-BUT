package main;

import controle.ControleurClavier;

public class BouclePrincipale {

	public String nom = "StreetFighterIUT";
	public int fps = 100;
	public JeuPhysique jeuPhysique;
	// creation du controleur
	public ControleurClavier cClavier;

	public void setName(String nom) {
		this.nom = nom;
	}

	public void setFPS(int fps) {
		this.fps = fps;
	}

	public BouclePrincipale() throws Exception {
		// creation du jeu
		// jeuPhysique = new JeuPhysique();
	}

	public void lanceBouclePrincipale() throws Exception {

		// ControleurClavier cClavier=new ControleurClavier(true);
		jeuPhysique.affiche.addKeyListener(cClavier);
		// mettre l'acces au controleur dans monde
		jeuPhysique.moteurPhys.monde.c = cClavier.c;

		// afficher
		System.out.println("\n\n**************************************************");
		System.out.println("*  " + nom);
		System.out.println("*                                                 *");
		System.out.println("*** touche 'Q' pour arreter jeu                  ***");
		System.out.println("****************************************************");
		System.out.println("\n\n");

		// fps
		long dureeBoucle = 1000000 / fps;
		System.out.println(" ---> duree d'une boucle " + dureeBoucle / 1000.);

		// lancement
		Thread.sleep(1000);
		jeuPhysique.affiche.requestFocusInWindow();

		// boucle
		long beforeTime = System.nanoTime();
		long l = System.currentTimeMillis();
		// nombre iterations
		int n = 0;
		while (!ControleurClavier.fin) {
			n++;
			jeuPhysique.update();
			jeuPhysique.render();

			// apres le render en nanos
			long timafter = System.nanoTime();

			// sleep en millisecond
			while (System.nanoTime() - beforeTime - dureeBoucle * 1000L < 0) {
			}
			beforeTime = System.nanoTime();

		}
		long l2 = System.currentTimeMillis();

		// statistiques
		System.out.println("\n\n\n************************\n");
		System.out.println("Iterations = " + n);
		System.out.println("FPS = " + (n * 1000.0 / (l2 - l)));
		System.out.println("\n************************");

		System.exit(0);
	}

}
