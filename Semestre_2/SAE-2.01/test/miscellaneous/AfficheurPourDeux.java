/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package miscellaneous;

import afficheur.Afficheur;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Toolkit;
import physique.Monde;
import physique.Objet;
import physique.ObjetHeros;
import physique.ObjetMonstre;


/**
 *
 * @author Pierre-Frederic Villard
 */
public class AfficheurPourDeux extends Afficheur {
    
    
    MondePourDeux monde;
    
    public AfficheurPourDeux(MondePourDeux monde) {
        super(monde);
    }
    
     /**
     *
     */
	public void render() {
		Graphics2D g = (Graphics2D)bs.getDrawGraphics();

		g.setColor(Color.black);

		//affiche le décor
		//decor.affiche( g);
                
                //decor.affiche((int)m.balle.px, g);
                decor.affiche((int)m.heros.get(0).px,g);
                decor.affiche((int)m.heros.get(1).px,g);
                
		// affiche les objets
		for (Objet obj : m.objets) {
			obj.draw(g);
		}
		
		//affiche les monstres
		for (ObjetMonstre monstre : m.monstres)
		{
			monstre.draw(g);
		}

		// affiche la balle
		//ObjetHeros b = m.balle;
                ObjetHeros b1 = m.heros.get(0);
		b1.draw(g);
                ObjetHeros b2 = m.heros.get(1);
                b2.draw(g);
                
		
		bs.show();
        Toolkit.getDefaultToolkit().sync();
        g.dispose();
		
		

	}
    
    
}
