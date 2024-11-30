<?php
function containsProfanity($text) {
    // Convert text to lowercase for case-insensitive matching
    $text = mb_strtolower($text, 'UTF-8');
    
    // List of profanity words (English and Filipino)
    $profanityList = [
        // English profanity
        'ass', 'bastard', 'bitch', 'cunt', 'dick', 'fuck', 'shit', 'whore',
        // Filipino profanity and curse words
        'gago', 'tangina', 'putangina', 'putragis', 'punyeta', 'puta', 
        'ulol', 'bobo', 'tanga', 'inutil', 'tarantado', 'hinayupak',
        'kupal', 'pakyu', 'lintik', 'leche', 'peste', 'hayop', 'hayup',
        'putik', 'gunggong', 'buwisit', 'engot', 'gaga', 'pokpok', 'pakshet',
        // Common variations and leetspeak
        'f*ck', 'sh*t', 'b!tch', 'tang!na', 'tang1na', 'p*ta', 'pu†a'
    ];
    
    // Check for exact matches and partial matches
    foreach ($profanityList as $word) {
        // Check if the word exists in the text
        if (strpos($text, $word) !== false) {
            return true;
        }
    }
    
    return false;
}

function filterProfanity($text) {
    // Convert text to lowercase for checking
    $textLower = mb_strtolower($text, 'UTF-8');
    
    // Same profanity list as above
    $profanityList = [
        // English profanity
        'ass', 'bastard', 'bitch', 'cunt', 'dick', 'fuck', 'shit', 'whore',
        // Filipino profanity and curse words
        'gago', 'tangina', 'putangina', 'putragis', 'punyeta', 'puta', 
        'ulol', 'bobo', 'tanga', 'inutil', 'tarantado', 'hinayupak',
        'kupal', 'pakyu', 'lintik', 'leche', 'peste', 'hayop', 'hayup',
        'putik', 'gunggong', 'buwisit', 'engot', 'gaga', 'pokpok', 'pakshet',
        // Common variations and leetspeak
        'f*ck', 'sh*t', 'b!tch', 'tang!na', 'tang1na', 'p*ta', 'pu†a'
    ];
    
    // Replace profanity with asterisks
    foreach ($profanityList as $word) {
        $replacement = str_repeat('*', mb_strlen($word));
        $pattern = '/'. preg_quote($word, '/') .'/i';
        $text = preg_replace($pattern, $replacement, $text);
    }
    
    return $text;
}
?> 