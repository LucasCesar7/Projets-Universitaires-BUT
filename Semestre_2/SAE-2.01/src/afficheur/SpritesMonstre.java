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

//distributeur de sprites

/**
 *
 * @author Pierre-Frederic Villard
 */
public class SpritesMonstre extends Sprites {

    String imageFile="monstre.png";

	// constructeur de table de sprites

    /**
     *
     * @throws IOException
     */
	public SpritesMonstre() throws IOException
	{
		im=ImageIO.read(new File(imageFile));
		activite="fixe";
		
		sprites=new HashMap<String, Sprite>();
		
		sprites.put("fixe",new Sprite(0,0, im.getWidth(), im.getHeight()));

		
	}

	@Override
	public void anime() {
		iteration++;
		
		if (activite.equals("fixe"))
		{
			
		}

		if (activite.equals("courseGauche"))
		{
			if (iteration>10)
			{
				num++;
				iteration=0;
			}
			
			if (num>7) num=0;
		}
		
		if (activite.equals("courseDroite"))
		{
			if (iteration>10)
			{
				num++;
				iteration=0;
			}
			
			if (num>7) num=0;
		}
		
		if (activite.equals("vold"))
		{
			if (iteration>10)
			{
				num++;
				iteration=0;
			}
			
			if (num>5) num=0;
		}

		if (activite.equals("volg"))
		{
			if (iteration>10)
			{
				num++;
				iteration=0;
			}
			
			if (num>5) num=0;
		}
		
	}

	
}
