package afficheur;

import java.awt.Graphics;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import java.util.HashMap;

import javax.imageio.ImageIO;


//distributeur de sprites
public class DecorFixe {
	
	//IMage
	BufferedImage im;
	
	//taille
	int wx,wy;
	
	//construit le sprite
	public DecorFixe() 
	{
		try {
			im=ImageIO.read(new File("background-Japon.jpg"));
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println("erreur lecture background");
		}
		wx=im.getWidth();
		wy=im.getHeight();
	}
	
        public void changeImage(String image) 
	{
		try {
			im=ImageIO.read(new File(image));
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println("erreur lecture background");
		}
		wx=im.getWidth();
		wy=im.getHeight();
	}	
        
	//afficheur de sprite
	public void affiche(Graphics g)
	{
		g.drawImage(im,0 ,0 , wx, wy, 0, 0, wx, wy,null);
	}
	
	
}
