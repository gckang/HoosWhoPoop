<?php
// This file contains all the database functions (SQL queries)
// related to habits. It is included by api.php.

/**
 * Get all habits for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @return array An array of habits
 */
function getAllHabitsForUser($db, $user_id)
{
    $query = "SELECT user_id, habit_id, category FROM habit WHERE user_id = :user_id ORDER BY habit_id DESC";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $habits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $habits;
}

/**
 * Add a new habit for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @param string $category The text/category of the new habit
 * @return array An array containing the newly created habit's info
 */
function addHabitForUser($db, $user_id, $category)
{
    // --- Find the next available habit_id for this user ---
    // (This logic is required bc of the composite primary key)
    $query_max = "SELECT IFNULL(MAX(habit_id), 0) + 1 AS next_id FROM habit WHERE user_id = :user_id";
    $stmt_max = $db->prepare($query_max);
    $stmt_max->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_max->execute();
    $row = $stmt_max->fetch(PDO::FETCH_ASSOC);
    $next_habit_id = $row['next_id'];

    // --- Insert the new habit ---
    $query_insert = "INSERT INTO habit (user_id, habit_id, category) VALUES (:user_id, :habit_id, :category)";
    $stmt_insert = $db->prepare($query_insert);
    
    $stmt_insert->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':habit_id', $next_habit_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':category', $category, PDO::PARAM_STR);
    
    $stmt_insert->execute();

    // Return the data for the new habit
    return [
        'user_id' => $user_id,
        'habit_id' => $next_habit_id,
        'category' => $category
    ];
}

/**
 * Delete a specific habit for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @param int $habit_id The ID of the habit (per-user)
 * @return bool True if a row was deleted, false otherwise
 */
function deleteHabitForUser($db, $user_id, $habit_id)
{
    $query = "DELETE FROM habit WHERE user_id = :user_id AND habit_id = :habit_id";

    $stmt = $db->prepare($query);

    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':habit_id', $habit_id, PDO::PARAM_INT);

    $stmt->execute();

    // rowCount() returns number of deleted rows, so below return T/F
    return $stmt->rowCount() > 0;
}

/**
 * Edit an existing habit for a user.
 * @param PDO $db
 * @param int $user_id
 * @param int $habit_id
 * @param string $category
 * @return bool
 */
function editHabitForUser($db, $user_id, $habit_id, $category)
{
    $query = "UPDATE habit 
              SET category = :category
              WHERE user_id = :user_id AND habit_id = :habit_id";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':habit_id', $habit_id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->rowCount() > 0;
}

?>