
import main.BouclePrincipale;
import afficheur.Afficheur;
import controle.ControleurClavier;
import main.JeuPhysique;
import miscellaneous.BouclePourDeux;
import miscellaneous.ControleurClavier2;
import miscellaneous.HeroCarreQuandTouche;
import miscellaneous.MondePourDeux;
import miscellaneous.MoteurPourDeux;
import miscellaneous.MurCool;
import physique.Monde;
import physique.MoteurPhysique;
import afficheur.Repere;
import afficheur.SpritesHeros;
import miscellaneous.AfficheurPourDeux;

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

public class testFighter {
        public static void main(String[] args) throws Exception {

                // le moteur physique
                MoteurPourDeux moteurPhys;
                // le rendu
                AfficheurPourDeux affiche;
                // leS controlerS
                ControleurClavier cClavier1 = new ControleurClavier(true);

                ControleurClavier2 cClavier2 = new ControleurClavier2(true);
                // Le monde
                MondePourDeux monMonde;

                // Construction du monde
                monMonde = new MondePourDeux();

                //////////////////////
                // Les MURS
                /////////////////////

                // sol
                monMonde.addMur(-200, -60, 1000, 20);

                monMonde.addMur(-20,00,20,1500);
                monMonde.addMur(630,00,20,1500);
                
                //////////////////////
                // Hero 1
                /////////////////////

                monMonde.addHero(0, 0, 50, 20); // index 0
                monMonde.heros.get(0).c = cClavier1.c;

                // On vérifie que c'est bien un HeroCarreQuandTouche
                if (monMonde.heros.get(0) instanceof HeroCarreQuandTouche) {
                        cClavier1.setHeros(monMonde.heros.get(0));
                }

                monMonde.heros.get(0).sprites = new SpritesHeros(monMonde.heros.get(0), "sprites/Ken");

                cClavier1.setHeros(monMonde.heros.get(0)); // ✅ Associe le héros au contrôleur
                
                //////////////////////
                //Hero 2
                /////////////////////

                monMonde.addHero(0, 0, 500, 20);
                monMonde.heros.get(1).c = cClavier2.c;

                // On vérifie que c'est bien un HeroCarreQuandTouche
                if (monMonde.heros.get(1) instanceof HeroCarreQuandTouche) {
                        cClavier1.setHeros(monMonde.heros.get(1));
                }

                monMonde.heros.get(1).sprites = new SpritesHeros(monMonde.heros.get(1), "sprites/Ryu");

                cClavier2.setHeros(monMonde.heros.get(1));

                // monMonde.heros.get(1).sprites.assignNewImage("sprites/Ryu/attaque-poing1.png");

                // on creer le moteur physique
                moteurPhys = new MoteurPourDeux();
                // On ajoute le monde au moteur
                moteurPhys.monde = monMonde;
                // on creer l'afficheur du monde
                affiche = new AfficheurPourDeux(moteurPhys.monde);
                // Gestion de la boucle principale
                BouclePourDeux maBoucle = new BouclePourDeux();
                // Ajout du jeu physique
                JeuPhysique MonJeuPhysique = new JeuPhysique();
                maBoucle.jeuPhysique = MonJeuPhysique;
                // Ajout du controler à le fenêtre
                maBoucle.cClavier = cClavier1;
                maBoucle.cClavier2 = cClavier2;
                // Ajout de la vue au jeu
                maBoucle.jeuPhysique.affiche = affiche;
                // Ajout du jeu à la boucle
                maBoucle.jeuPhysique.moteurPhys = moteurPhys;
                maBoucle.jeuPhysique.moteurPhys.monde = moteurPhys.monde;

                Repere.isSubjective = false;

                maBoucle.lanceBouclePrincipale();
        }
}
