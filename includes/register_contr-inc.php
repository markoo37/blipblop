<?php

declare(strict_types=1);

function is_input_empty(string $username, string $password, string $password_again){
    if(empty($username) || empty($password) || empty($password_again)){
        return true;
    }
    else {return false;}
}

function is_username_invalid(string $username){
    // Example rules for invalid usernames:
    // - Less than 3 characters
    // - More than 20 characters
    // - Contains anything other than letters, numbers, underscores
    if (strlen($username) < 3 || strlen($username) > 20) {
        return true;
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return true;
    }

    return false; // it's valid
}

function is_username_taken(object $pdo, string $username){
    if(get_username($pdo, $username)){
        return true;
    }
    else {return false;}
}

function is_password_tooShort($password){
    if(strlen($password) < 8){
        return true;
    }
    else{return false;}
}

function passwords_dont_match($password, $passwordAgain){
    if ($password != $passwordAgain){
        return true;
    }
    else{return false;}
}

function registerUser(object $pdo, string $username, string $password){
    register_user($pdo, $username, $password);
}
