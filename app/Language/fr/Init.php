<?php

$pathJsonOverride = '../lang/fr/init.json';

$lang = [
    "default" => [
        "publisher" => "Éditeur inconnu",
        "author" => "Auteur inconnu",
    ],
];

if (file_exists($pathJsonOverride)) {
    $json = json_decode(file_get_contents($pathJsonOverride), true);
    return replace_lang($lang, $json);
}

return $lang;