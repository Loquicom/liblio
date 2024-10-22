<?php

$pathJsonOverride = '../lang/en/api.json';

$lang = [
    "common" => [
        "forbidden" => "Access forbidden",
        "serverError" => "Server error",
        "invalidData" => "Invalid data",
    ],
    "users" => [
        "notFound" => "User not found",
        "invalidRole" => "Invalid role",
    ],
    "publishers" => [
        "notFound" => "Publisher not found",
        "defaultDelete" => "Can't delete the default publisher",
    ],
    "books" => [
        "notFound" => "Books not found",
        "isbnUnique" => "ISBN must be unique",
    ],
    "authors" => [
        "notFound" => "Author not found",
        "defaultDelete" => "Can't delete the default author",
    ],
    "members" => [
        "notFound" => "Member not found",
        "idUnique" => "ID must be unique",
    ],
];

if (file_exists($pathJsonOverride)) {
    $json = json_decode(file_get_contents($pathJsonOverride), true);
    return replace_lang($lang, $json);
}

return $lang;