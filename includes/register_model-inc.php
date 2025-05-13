<?php

declare(strict_types=1);

function get_username(object $pdo, string $username)
{
    $query = "SELECT username FROM app_users WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function register_user(object $pdo, string $username, string $password){
    $query = "BEGIN register_user(:username, :password, :type_id); END;";
    $stmt = $pdo->prepare($query);
    $user_type = 1;
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':type_id', $user_type);
    $stmt->execute();
}