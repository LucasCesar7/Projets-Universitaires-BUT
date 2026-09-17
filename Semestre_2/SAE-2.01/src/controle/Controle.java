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

package controle;



/**
 *
 * @author Pierre-Frederic Villard
 */

/**
 * Cette classe permet de gerer le controle.
 */
public class Controle {

    /**
     *
     */
     public boolean gauche;

    /**
     *
     */
     public boolean droite;

    /**
     *
     */
     public boolean haut;

    /**
     *
     */
     public boolean bas;
    
    /**
     *
     */
      public boolean enAir;

      public boolean attaque_coup_poing = false;
      public boolean position_defense = false;
      public boolean attaque_coup_pied = false;
}
