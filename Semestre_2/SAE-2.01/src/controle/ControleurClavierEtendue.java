package controle;

import java.awt.event.KeyEvent;

public class ControleurClavierEtendue extends ControleurClavier {

    public Controle c2;

    public ControleurClavierEtendue(boolean affiche) {
        super(affiche);
        c2 = new Controle();
    }

    @Override
    public void keyPressed(KeyEvent e) {

        switch (e.getKeyCode()) {
            case KeyEvent.VK_D:
                c2.droite = true;
                break;
            case KeyEvent.VK_Q:
                c2.gauche = true;
                break;
            case KeyEvent.VK_Z:
                c2.haut = true;
                break;
            case KeyEvent.VK_W:
                fin = true;
                break;

        }

        if (affiche)
            afficheur.dessin();

    }

    @Override
    public void keyReleased(KeyEvent e) {
        switch (e.getKeyCode()) {
            case KeyEvent.VK_D:
                c2.droite = false;
                break;
            case KeyEvent.VK_Q:
                c2.gauche = false;
                break;
            case KeyEvent.VK_Z:
                c2.haut = false;
                break;
            case KeyEvent.VK_W:
                fin = false;
                break;

        }

        if (affiche)
            afficheur.dessin();
    }
}