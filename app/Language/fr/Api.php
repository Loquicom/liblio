<?php

$pathJsonOverride = '../lang/fr/api.json';

$lang = [
    "common" => [
        "forbidden" => "Accès non autorisé",
        "serverError" => "Erreur serveur",
        "invalidData" => "Données incorrects",
    ],
    "users" => [
        "notFound" => "Utilisateur introuvable",
        "invalidRole" => "Role invalide",
    ],
    "publishers" => [
        "notFound" => "Éditeur introuvable",
        "defaultDelete" => "Impossible de supprimer l'éditeur par défaut",
    ],
    "books" => [
        "notFound" => "Livre introuvable",
        "isbnUnique" => "L'ISBN doit être unique",
    ],
    "authors" => [
        "notFound" => "Auteur introuvable",
        "defaultDelete" => "Impossible de supprimer l'auteur par défaut",
        "invalidRole" => "Le role est invalide",
        "deleteMain" => "Impossible de supprimer l'auteur principal d'un livre",
    ],
    "members" => [
        "notFound" => "Adhérent introuvable",
        "idUnique" => "L'ID doit être unique",
        "alreadyBorrow" => "Le livre {0} est déjà emprunté par l'adhérent",
    ],
];

if (file_exists($pathJsonOverride)) {
    $json = json_decode(file_get_contents($pathJsonOverride), true);
    return replace_lang($lang, $json);
}

return $lang;