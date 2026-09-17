<?php

class ModerationHelper {
    private $apiKey;
    private $apiEndpoint = 'https://api.mistral.ai/v1/chat/moderations';
    private $bannedWords = [];
    
    public function __construct() {
        require_once dirname(dirname(__DIR__)) . '/app/config/Env.php';
        $this->apiKey = Env::get('MISTRAL_API_KEY');
        
        // Charger la liste de mots interdits
        $this->bannedWords = require dirname(__DIR__) . '/data/french-badwords.php';
        
        if (!$this->apiKey) {
            error_log("ERREUR: Clé Mistral API manquante dans .env");
        }
    }
    
    public function moderateContent($text) {
        if (empty($text)) {
            return [
                'is_safe' => true,
                'flagged' => false,
                'categories' => [],
                'category_scores' => [],
                'error' => null
            ];
        }
        
        // Vérifier les mots interdits
        $localCheck = $this->checkBannedWords($text);
        if ($localCheck['flagged']) {
            error_log("Filtre local : mot interdit détecté");
            return $localCheck;
        }
        
        // Vérifier patterns suspects
        $patternCheck = $this->checkSuspiciousPatterns($text);
        if ($patternCheck['flagged']) {
            error_log("Pattern suspect détecté");
            return $patternCheck;
        }
        
        // API MISTRAL
        try {
            $data = [
                'model' => 'mistral-moderation-latest',
                'input' => [
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ]
            ];
            
            $ch = curl_init($this->apiEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                throw new Exception('Erreur cURL: ' . curl_error($ch));
            }
            
            $ch = null;
            
            if ($httpCode !== 200) {
                throw new Exception("Erreur API Mistral (HTTP $httpCode): $response");
            }
            
            $result = json_decode($response, true);
            
            if (!isset($result['results'][0])) {
                throw new Exception("Format de réponse inattendu");
            }
            
            $moderation = $result['results'][0];
            
            // Extraire les catégories
            $categories = [];
            $scores = [];
            $flagged = false;
            
            if (isset($moderation['categories'])) {
                foreach ($moderation['categories'] as $category => $data) {
                    $score = $data['score'] ?? 0;
                    $violated = $data['violated'] ?? false;
                    
                    $categories[$category] = $violated;
                    $scores[$category] = $score;
                    
                    // Score > 0.1 = flaggé 
                    if ($score > 0.1 || $violated) {
                        $flagged = true;
                    }
                }
            }
            
            return [
                'is_safe' => !$flagged,
                'flagged' => $flagged,
                'categories' => $categories,
                'category_scores' => $scores,
                'error' => null
            ];
            
        } catch (Exception $e) {
            error_log("Erreur de modération Mistral: " . $e->getMessage());
            
            // Fallback
            return [
                'is_safe' => true,
                'flagged' => false,
                'categories' => [],
                'category_scores' => [],
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérification locale des mots interdits
     */
    private function checkBannedWords($text) {
        $textLower = mb_strtolower($text);
        
        // Normaliser le texte
        $textNormalized = $this->normalizeLeetspeak($textLower);
        
        foreach ($this->bannedWords as $word) {
            // Vérifier mot exact
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/u', $textNormalized)) {
                error_log("Mot interdit détecté : $word");
                return [
                    'is_safe' => false,
                    'flagged' => true,
                    'categories' => ['local_filter' => true],
                    'category_scores' => ['local_filter' => 1.0],
                    'error' => null,
                    'matched_word' => $word
                ];
            }
        }
        
        return ['flagged' => false];
    }
    
    /**
     * Vérifier patterns suspects
     */
    private function checkSuspiciousPatterns($text) {
        // Pattern 1 : MAJUSCULES EXCESSIVES
        $uppercaseRatio = $this->getUppercaseRatio($text);
        if ($uppercaseRatio > 0.7 && mb_strlen($text) > 20) {
            return [
                'is_safe' => false,
                'flagged' => true,
                'categories' => ['spam' => true],
                'category_scores' => ['spam' => 0.9],
                'error' => null
            ];
        }
        
        // Pattern 2 : Répétitions excessives (!!!!!!, ?????)
        if (preg_match('/(.)\1{4,}/', $text)) {
            return [
                'is_safe' => false,
                'flagged' => true,
                'categories' => ['spam' => true],
                'category_scores' => ['spam' => 0.7],
                'error' => null
            ];
        }
        
        return ['flagged' => false];
    }
    
    /**
     * Normaliser le leetspeak (m3rd3 -> merde)
     */
    private function normalizeLeetspeak($text) {
        $replacements = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '8' => 'b',
            '@' => 'a',
            '$' => 's'
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
    
    private function getUppercaseRatio($text) {
        $uppercase = preg_match_all('/[A-ZÀ-ÖØ-Þ]/u', $text);
        $total = preg_match_all('/[A-Za-zÀ-ÖØ-öø-ÿ]/u', $text);
        
        return $total > 0 ? $uppercase / $total : 0;
    }
    
    public function getModerationReport($moderationResult) {
        if ($moderationResult['is_safe']) {
            return "Contenu validé automatiquement";
        }
        
        // Vérifier filtre
        if (isset($moderationResult['categories']['local_filter'])) {
            $word = $moderationResult['matched_word'] ?? 'mot interdit';
            return "Contenu problématique: Langage inapproprié détecté ($word)";
        }
        
        if (isset($moderationResult['categories']['spam'])) {
            return "Contenu problématique: Spam ou abus détecté";
        }
        
        $flaggedCategories = [];
        foreach ($moderationResult['categories'] as $category => $isFlagged) {
            if ($isFlagged) {
                $score = $moderationResult['category_scores'][$category] ?? 0;
                $flaggedCategories[] = $this->translateCategory($category) . 
                    " (" . round($score * 100, 1) . "%)";
            }
        }
        
        return "Contenu problématique: " . implode(", ", $flaggedCategories);
    }
    
    private function translateCategory($category) {
        $translations = [
            'sexual' => 'Contenu sexuel',
            'hate_and_discrimination' => 'Discours haineux',
            'violence_and_threats' => 'Violence et menaces',
            'dangerous_and_criminal_content' => 'Contenu dangereux',
            'selfharm' => 'Auto-mutilation',
            'health' => 'Conseils médicaux',
            'financial' => 'Conseils financiers',
            'law' => 'Conseils juridiques',
            'pii' => 'Données personnelles',
            'local_filter' => 'Langage inapproprié',
            'spam' => 'Spam'
        ];
        
        return $translations[$category] ?? $category;
    }
    
    public function shouldAutoReject($moderationResult, $threshold = 0.5) {
        if (!$moderationResult['flagged']) {
            return false;
        }
        
        // Filtre local ou spam -> rejet immédiat
        if (isset($moderationResult['categories']['local_filter']) || 
            isset($moderationResult['categories']['spam'])) {
            return true;
        }
        
        // Catégories graves -> rejet immédiat
        $severeCategories = [
            'violence_and_threats',
            'dangerous_and_criminal_content',
            'selfharm',
            'hate_and_discrimination'
        ];
        
        foreach ($severeCategories as $category) {
            if (isset($moderationResult['categories'][$category]) && 
                $moderationResult['categories'][$category]) {
                return true;
            }
        }
        
        // Score modéré -> rejet
        foreach ($moderationResult['category_scores'] as $score) {
            if ($score >= $threshold) {
                return true;
            }
        }
        
        return false;
    }
}
?>
