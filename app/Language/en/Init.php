<?php

$pathJsonOverride = '../lang/en/init.json';

$lang = [
    "default" => [
        "publisher" => "Unknown publisher",
        "author" => "Unknown author",
    ],
];

if (file_exists($pathJsonOverride)) {
    $json = json_decode(file_get_contents($pathJsonOverride), true);
    return replace_lang($lang, $json);
}

return $lang;