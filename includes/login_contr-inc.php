<?php

declare(strict_types=1);

function is_input_empty(string $username, string $password,){
    if(empty($username) || empty($password)){
        return true;
    }
    else {return false;}
}

function does_user_exist(object $pdo, string $username){
    if(get_username($pdo, $username)){
        return true;
    }
    else {return false;}
}

