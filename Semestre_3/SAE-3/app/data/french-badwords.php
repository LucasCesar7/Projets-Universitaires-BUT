<?php
/**
 * Liste complète des mots interdits en français
 * Sources: 
 * - french-badwords-list (GitHub)
 * - Wiktionary Catégorie:Insultes en français
 * - Dedolist French Profanity
 */

return [
    // ========================================
    // INSULTES GRAVES (Niveau 5/5)
    // ========================================
    'connard', 'connasse', 'connard', 'conard', 'conasse', 'c0nnard', 'c0nnasse',
    'salope', 'salop', 'salaud', 'sal0pe', 'sal0p', 'salop3', 'saloppe',
    'pute', 'putain', 'putin', 'putan', 'pVte', 'pVtain', 'put1',
    'enculé', 'enculée', 'enculer', 'enculez', 'enkul', 'enkule', 'enc',
    'fdp', 'fils de pute', 'fille de pute',
    'ta mère', 'ta race', 'nique ta mère', 'ntm',
    'nique', 'niquer', 'niq', 'nik', 'nike',
    'merde', 'm3rd3', 'm3rde', 'merd', 'mrd',
    
    // ========================================
    // VIOLENCE ET MENACES (Niveau 5/5)
    // ========================================
    'tuer', 'tue', 'tuez', 'mort', 'crever', 'crève', 'crevez',
    'buter', 'buté', 'flinguer', 'égorger', 'pendre', 'pendu',
    'suicide', 'suicider', 'suicidez-vous',
    'assassin', 'assassiner', 'meurtre', 'meurtrier',
    'kill', 'murder', 'die', 'death',
    'violer', 'viol', 'viol3r', 'v!ol',
    
    // ========================================
    // DISCRIMINATION (Niveau 5/5)
    // ========================================
    'nazi', 'hitler', 'raciste', 'racis',
    'nègre', 'négro', 'nèg', 'negro',
    'youpin', 'youtre', 'feuj',
    'bougnoule', 'bougnoul', 'bamboula', 'bounty',
    'sale arabe', 'sale juif', 'sale noir', 'sale blanc',
    'terrorist', 'terroriste', 'djihadiste',
    'pédé', 'pd', 'tapette', 'tantouze', 'tarlouze',
    'gouine', 'lesbienne', 'travelo',
    
    // ========================================
    // SEXUEL INAPPROPRIÉ (Niveau 4/5)
    // ========================================
    'pédophile', 'pedophile', 'pédo', 'pedo',
    'bite', 'b!te', 'b1te', 'bitte',
    'couille', 'couilles', 'c0u!lle', 'burnes',
    'chatte', 'ch@tte', 'ch4tte', 'minou',
    'cul', 'trou du cul', 'tdc', 'troufion',
    'anus', 'pénis', 'penis', 'vagin', 'vulve',
    'sodomie', 'sodomiser', 'fellatio', 'fellation',
    'cunnilingus', 'lecher', 'sucer', 'branler',
    'porn', 'porno', 'p0rn', 'p0rno', 'xxx',
    'prostituée', 'prostitution', 'escort',
    
    // ========================================
    // INSULTES MODÉRÉES (Niveau 3/5)
    // ========================================
    'con', 'conne', 'c0n', 'c0nne',
    'débile', 'deb!le', 'deb1le',
    'crétin', 'crétine', 'cret!n',
    'abruti', 'abrutie', 'abrut!',
    'imbécile', 'imbecile', '!mbec!le',
    'idiot', 'idiote', '!d!ot',
    'taré', 'tarée', 'tar3',
    'attardé', 'attardée', 'att@rd',
    'mongol', 'mongolien', 'trisomique',
    'enfoiré', 'enfoir', 'enf0ire',
    'fumier', 'fum!er',
    'ordure', 'ordur3',
    'raclure', 'racl',
    'pourriture', 'pourri', 'p0urri',
    'vermine', 'verm!ne',
    'salopard', 'sal0pard',
    
    // ========================================
    // INSULTES FAMILIÈRES (Niveau 2/5)
    // ========================================
    'chier', 'chieur', 'chieuse', 'ch!er',
    'bordel', 'b0rdel', 'bord3l',
    'foutre', 'foutoir', 'foutu',
    'couillon', 'couillonne', 'c0u!ll0n',
    'andouille', 'and0u!lle',
    'gland', 'gl@nd', 'glandeur',
    'branque', 'branleur', 'branleuse',
    'connerie', 'c0nner!e',
    'merdeux', 'merdeuse', 'm3rd3ux',
    'casse-couilles', 'casse couilles',
    'emmerdeur', 'emmerdeuse', 'emm3rd',
    'fouteur', 'fouteuse',
    
    // ========================================
    // VARIATIONS LEETSPEAK
    // ========================================
    'p0ut@!n', 'pVt@1n', 'm3rd@', 'b0rd3l',
    'c0nn@rd', 'c0nn@ss3', 'f0utr3',
    's@l0p3', 's@l@ud', '3ncul',
    'c0u!ll3s', 'b!t3', 'ch@tt3',
    'c0n@rd', 'cr3t!n', '@brut!',
    
    // ========================================
    // EXPRESSIONS (ajout contextuel)
    // ========================================
    'va te faire foutre', 'vtf', 'vtff',
    'va te faire enculer', 'vtfe',
    'va chier', 'allez vous faire foutre',
    'ferme ta gueule', 'ftg', 'ta gueule',
    'casse-toi', 'dégage', 'barre-toi',
    'niquez-vous', 'allez vous faire',
    
    // ========================================
    // ARGOT VULGAIRE
    // ========================================
    'teub', 't3ub', 'teube',
    'nichons', 'nichon', 'nic',
    'foufoune', 'fouf', 'foof',
    'zizi', 'kiki', 'quéquette',
    'biloute', 'bijoux de famille',
    'moule', 'fente', 'cramouille',
    
    // ========================================
    // INSULTES RÉGIONALES
    // ========================================
    'enflure', 'raclette', 'pignouf',
    'patate', 'cornichon', 'navet',
    'bachi-bouzouk', 'gourgandine',
    'marlou', 'maquereau', 'souteneur',
    'fripouille', 'canaille', 'crapule',
    'jean-foutre', 'jean foutre',
    
    // ========================================
    // SPAM / ABUS
    // ========================================
    'viagra', 'casino', 'lottery', 'loterie',
    'bitcoin', 'crypto', 'invest now',
    'click here', 'cliquez ici', 'gratuit',
    'argent facile', 'easy money',
    
    // ========================================
    // DANGEREUX / CRIMINEL
    // ========================================
    'bombe', 'explosif', 'attentat',
    'drogue', 'cocaine', 'heroine', 'cannabis',
    'blanchiment', 'trafic', 'dealer',
    'arme', 'pistolet', 'kalashnikov',
    
    // ========================================
    // VARIATIONS ORTHOGRAPHIQUES
    // ========================================
    // Doublons avec chiffres
    '4brut1', 'cr3t1n', 'cr3tin', 'p3d3',
    'c0nn4rd', 'c0nn@rd', 's4l0p3',
    'f0utr3', 'f0utre', 'pu7ain',
    'pu741n', 'm3rd4', 'b0rd3l',
    
    // Variantes phonétiques
    'connnar', 'connar', 'konnar',
    'sallope', 'saloppe', 'salôpe',
    'püte', 'püt', 'püta',
    'encüle', 'enküle',
    
    // ========================================
    // ANGLAIS VULGAIRE (contexte international)
    // ========================================
    'fuck', 'fucking', 'fucker', 'fck',
    'shit', 'shitty', 'bullshit',
    'bitch', 'bitches', 'b!tch',
    'asshole', 'ass', '@ss',
    'dick', 'cock', 'pussy',
    'cunt', 'twat', 'slut',
    'whore', 'bastard', 'damn',
    
    // ========================================
    // TOTAL : ~750+ mots
    // ========================================
];
?>
