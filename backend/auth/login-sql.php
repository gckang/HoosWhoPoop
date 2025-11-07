<?php
// This file contains all the database functions (SQL queries)
// related to user authentication.

/**
 * Finds a user in the useraccount table by their username.
 * @param PDO $db The database connection object
 * @param string $username The user's username
 * @return array|false The user's data as an array, or false if not found.
 * @throws PDOException if the query fails
 */
function findUserByUsername($db, $username)
{
    $stmt = $db->prepare("SELECT * FROM useraccount WHERE username = :username");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Returns false if no user is found
}

/**
 * Creates a new user in the useraccount table.
 * @param PDO $db The database connection object
 *img
 * @param string $username The new user's username
 * @param string $hashed_password The *already hashed* password
 * @return array An array containing the new user's ID and username
 * @throws PDOException if the query fails
 */
function createUser($db, $username, $hashed_password)
{
    $stmt = $db->prepare(
        "INSERT INTO useraccount (username, password) VALUES (:username, :password)"
    );
    
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
    
    $stmt->execute();
    
    $new_user_id = $db->lastInsertId();

    return [
        'user_id' => $new_user_id,
        'username' => $username
    ];
}

?>