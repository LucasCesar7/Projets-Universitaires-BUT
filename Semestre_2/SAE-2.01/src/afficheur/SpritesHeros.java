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

package afficheur;

import java.awt.Graphics;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.HashMap;

import javax.imageio.ImageIO;

import controle.Controle;

import physique.ObjetHeros;

import java.awt.Graphics2D;

//distributeur de sprites

/**
 *
 * @author Pierre-Frederic Villard
 */
public class SpritesHeros extends Sprites {

	private ObjetHeros heros;

	String imageFile = "sprites/Ken/attaque-poing1.png";

	// constructeur de table de sprites

	/**
	 *
	 * @param b
	 * @throws IOException
	 */

	// Attributs à ajouter dans SpritesHeros
	private BufferedImage[] spritesAttaque;
	private int frameAttaque = 0;

	// Dans le constructeur ou une méthode appelée à l'initialisation
	private void chargerAttaque(String dossier) {
		spritesAttaque = new BufferedImage[4];
		try {
			for (int i = 0; i < 4; i++) {
				spritesAttaque[i] = ImageIO.read(new File(dossier + "/attaque-poing" + (i + 1) + ".png"));
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	private BufferedImage[] spritesAttaquePied;
	private int frameAttaquePied = 0;

	private void chargerAttaquePied(String dossier) {



		spritesAttaquePied = new BufferedImage[2]; 

		try {
			for (int i = 0; i < 2; i++) { 
				spritesAttaquePied[i] = ImageIO.read(new File(dossier + "/attaque-pied" + (i + 1) + ".png"));

			}
		} catch (IOException e) {
			e.printStackTrace();

		}
	}

	private BufferedImage[] spritesDefense;
	private int frameDefense = 0;

	private void chargerDefense(String dossier) {



		spritesDefense = new BufferedImage[1]; 

		try {
			for (int i = 0; i < 1; i++) { 
				spritesDefense[i] = ImageIO.read(new File(dossier + "/defense" + (i + 1) + ".png"));

			}
		} catch (IOException e) {
			e.printStackTrace();

		}
	}

	private long dernierChangement = 0;
	private final int intervalleFrame = 100;

	public void afficherAttaque(Graphics g, int x, int y, int w, int h) {
		if (spritesAttaque != null && spritesAttaque.length > 0) {
			long maintenant = System.currentTimeMillis();

			if (maintenant - dernierChangement >= intervalleFrame) {
				frameAttaque = (frameAttaque + 1) % spritesAttaque.length;
				dernierChangement = maintenant;
			}

			// Cast explicitement Graphics en Graphics2D
			Graphics2D g2d = (Graphics2D) g;

			if (heros.vx < 0) { // Si le personnage se déplace vers la gauche
				g2d.drawImage(spritesAttaque[frameAttaque], x + w, y, -w, h, null);
			} else { // Déplacement normal à droite
				g2d.drawImage(spritesAttaque[frameAttaque], x, y, w, h, null);
			}
		}
	}

	private long dernierChangementPied = 0;
	private final int intervalleFramePied = 130;

	public void afficherAttaquePied(Graphics g, int x, int y, int w, int h) {
		if (spritesAttaquePied == null || spritesAttaquePied.length == 0) {
			return;
		}

		long maintenantPied = System.currentTimeMillis();

		//Mise à jour de la frame uniquement si le temps est écoulé
		if (maintenantPied - dernierChangementPied >= intervalleFramePied) {
			frameAttaquePied = (frameAttaquePied + 1) % spritesAttaquePied.length;
			dernierChangementPied = maintenantPied;
		}

		Graphics2D g2d = (Graphics2D) g;

		BufferedImage imageAttaque = spritesAttaquePied[frameAttaquePied];

		if (heros.vx < 0) { // Si le personnage se déplace vers la gauche
			g2d.drawImage(imageAttaque, x + w, y, -w, h, null);
		} else { // Déplacement normal à droite
			g2d.drawImage(imageAttaque, x, y, w, h, null);
		}


	}

	private long dernierChangementDefense = 0;
	private final int intervalleFrameDefense = 100;

	public void afficherDefense(Graphics g, int x, int y, int w, int h) {
		if (spritesDefense != null && spritesDefense.length > 0) {
			long maintenantDefense = System.currentTimeMillis();

			if (maintenantDefense - dernierChangementDefense >= intervalleFrameDefense) {
				frameDefense = (frameDefense + 1) % spritesDefense.length;
				dernierChangementDefense = maintenantDefense;
			}

			Graphics2D g2d = (Graphics2D) g;

			if (heros.vx < 0) { 
				g2d.drawImage(spritesDefense[frameDefense], x + w, y, -w, h, null);
			} else { 
				g2d.drawImage(spritesDefense[frameDefense], x, y, w, h, null);
			}
		}
	}

	public void resetAttaque() {
		frameAttaque = 0;
		dernierChangement = System.currentTimeMillis();
	}

	public void resetAttaquePied() {
		frameAttaquePied = 0;
		dernierChangementPied = System.currentTimeMillis();
	}

	public void resetDefense() {
		frameDefense = 0;
		dernierChangementDefense = System.currentTimeMillis();
	}

	// Ancien constructeur par défaut
	public SpritesHeros(ObjetHeros b) throws IOException {
		this(b, "sprites/Ken"); // Appelle le nouveau avec dossier par défaut
	}

	private String dossierSprites;

	// Nouveau contructeur avec chemin personnalisable
	public SpritesHeros(ObjetHeros b, String dossierSprites) throws IOException {
		this.heros = b;

		// image par défaut
		imageFile = dossierSprites + "/attaque-poing1.png";
		im = ImageIO.read(new File(imageFile));

		activite = "fixe";
		sprites = new HashMap<String, Sprite>();
		sprites.put("fixe", new Sprite(0, 0, im.getWidth(), im.getHeight()));

		chargerAttaque(dossierSprites);
		chargerAttaquePied(dossierSprites);
		chargerDefense(dossierSprites);
	}

	// afficheur de sprite
	public void affiche(int x, int y, Graphics g) {
		Sprite s = sprites.get("fixe");
		if (s == null)
			s = sprites.get("erreur");

		// regarde la direction du personnage

		if (heros.vx >= 0) {
			// affichage normal
			g.drawImage(im, x, y, x + s.tx, y + s.ty, s.xmin, s.ymin, s.xmax,
					s.ymax, null);
		} else {
			// inverse gauche et droite
			g.drawImage(im, x + s.tx, y, x, y + s.ty, s.xmin, s.ymin, s.xmax,
					s.ymax, null);
		}

	}

	public void changeEtape(String nouvelleActivite) {
		activite = nouvelleActivite;
		num = 0; // Réinitialisation de l'étape d'animation
	}

	public void changeEtapePied(String nouvelleActivite) {
		activite = nouvelleActivite;
		num = 0;
	}

	public void changeEtapeDefense(String nouvelleActivite) {
		activite = nouvelleActivite;
		num = 0;
	}

	public String chaine() {
		return activite + num; // Génère "attaque0", "attaque1", etc.
	}

	/**
	 *
	 */
	@Override
	public void anime() {
		iteration++;

		if (activite.equals("fixe")) {

		}

		if (activite.equals("saut")) {

		}

		if (activite.equals("course")) {
			if (iteration > 9) {
				num++;
				iteration = 0;
			}

			if (num > 9)
				num = 0;
		}

		if (activite.equals("attaque")) {
			num++;
		}

		if (activite.equals("attaque-pied")) {
			num++;
		}

		if (activite.equals("position_defense")) {
			num++;
		}
	}

}
