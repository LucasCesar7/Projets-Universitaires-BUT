package afficheur;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class FinDePartieMenu extends JFrame {
    public FinDePartieMenu() {
        setTitle("Fin de Partie");
        setSize(600, 200);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        JLabel label = new JLabel("Le combat est terminé !", SwingConstants.CENTER);
        label.setFont(new Font("Arial", Font.BOLD, 24));
        label.setForeground(Color.BLACK);
        add(label, BorderLayout.NORTH);
        label.setBorder(BorderFactory.createEmptyBorder(20, 0, 35, 0));


        JButton rejouer = new JButton("Rejouer");
        JButton quitter = new JButton("Quitter");

        rejouer.setFont(new Font("Arial", Font.BOLD, 20));
        rejouer.setBackground(Color.green);
        quitter.setFont(new Font("Arial", Font.BOLD, 20));
        quitter.setBackground(Color.red);

        JPanel panelBoutons = new JPanel();
        panelBoutons.setLayout(new FlowLayout());
        panelBoutons.add(rejouer);
        panelBoutons.add(quitter);
        add(panelBoutons, BorderLayout.CENTER);

        rejouer.addActionListener(e -> {
            dispose();
            try {
                String javaCommand = "java -cp " + System.getProperty("java.class.path") + " testFighter";
                Runtime.getRuntime().exec(javaCommand);
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        });

        quitter.addActionListener(e -> System.exit(0));
    }

    public static void afficherMenu() {
        SwingUtilities.invokeLater(() -> new FinDePartieMenu().setVisible(true));
    }
}
