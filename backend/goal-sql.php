<?php
// This file contains all the database functions (SQL queries)
// related to GOALS.

/**
 * Get all goals for a specific user, joining with the habit category.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @return array An array of goals
 * @throws PDOException if the query fails
 */
function getAllGoalsForUser($db, $user_id)
{
    // Joins 'goal' with 'habit' to get the category name
    $stmt = $db->prepare(
        "SELECT g.goal_id, g.habit_id, g.deadline, g.goal_time, h.category
         FROM goal g
         JOIN habit h ON g.user_id = h.user_id AND g.habit_id = h.habit_id
         WHERE g.user_id = :user_id
         ORDER BY g.deadline DESC"
    );
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add a new goal for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @param int $habit_id The ID of the habit this goal is for
 * @param string $deadline The deadline date (e.g., '2025-12-31')
 * @param int $goal_time The target time (e.g., 10 hours)
 * @return array An array containing the newly created goal's info
 * @throws PDOException if the query fails
 */
function addGoalForUser($db, $user_id, $habit_id, $deadline, $goal_time)
{
    // Find the next available goal_id for this user
    $stmt_max = $db->prepare("SELECT IFNULL(MAX(goal_id), 0) + 1 AS next_id FROM goal WHERE user_id = :user_id");
    $stmt_max->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_max->execute();
    $row = $stmt_max->fetch(PDO::FETCH_ASSOC);
    $next_goal_id = $row['next_id'];

    // Insert the new goal
    $stmt_insert = $db->prepare(
        "INSERT INTO goal (user_id, goal_id, habit_id, deadline, goal_time) 
         VALUES (:user_id, :goal_id, :habit_id, :deadline, :goal_time)"
    );
    
    $stmt_insert->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':goal_id', $next_goal_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':habit_id', $habit_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':deadline', $deadline, PDO::PARAM_STR);
    $stmt_insert->bindValue(':goal_time', $goal_time, PDO::PARAM_INT);
    
    $stmt_insert->execute();

    // Return the data for the new goal
    return [
        'user_id' => $user_id,
        'goal_id' => $next_goal_id,
        'habit_id' => $habit_id,
        'deadline' => $deadline,
        'goal_time' => $goal_time
    ];
}

/**
 * Delete a specific goal for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @param int $habit_id The ID of the goal (per-user)
 * @return bool True if a row was deleted, false otherwise
 */
function deleteGoalForUser($db, $user_id, $goal_id)
{
    $query = "DELETE FROM goal WHERE user_id = :user_id AND goal_id = :goal_id";

    $stmt = $db->prepare($query);

    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':goal_id', $goal_id, PDO::PARAM_INT);

    $stmt->execute();

    // rowCount() returns number of deleted rows, so below return T/F
    return $stmt->rowCount() > 0;
}

/**
 * Edit an existing habit for a user.
 * @param PDO $db
 * @param int $user_id
 * @param int $goal_id
 * @param int $habit_id The habit this goal belongs to
 * @param string $deadline The goal deadline (YYYY-MM-DD)
 * @param int $goal_time The required hours for the goal
 * @return bool
 */
function editGoalForUser($db, $user_id, $goal_id, $habit_id, $deadline, $goal_time)
{
    $query = "UPDATE goal
              SET habit_id = :habit_id,
                  deadline = :deadline,
                  goal_time = :goal_time
              WHERE user_id = :user_id AND goal_id = :goal_id";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':habit_id', $habit_id, PDO::PARAM_INT);
    $stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
    $stmt->bindValue(':goal_time', $goal_time, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':goal_id', $goal_id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->rowCount() > 0;
}

?>