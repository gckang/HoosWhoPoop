<?php
// This file contains all the database functions (SQL queries)
// related to EVENTS.

/**
 * Get all events for a specific user, joining with habit category.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @return array An array of events
 * @throws PDOException if the query fails
 */
function getAllEventsForUser($db, $user_id)
{
    // This is a 3-table join to get the habit category from the event
    $stmt = $db->prepare(
        "SELECT e.event_id, e.day, e.start_time, e.end_time, h.category
         FROM event e
         JOIN goal g ON e.user_id = g.user_id AND e.goal_id = g.goal_id
         JOIN habit h ON g.user_id = h.user_id AND g.habit_id = h.habit_id
         WHERE e.user_id = :user_id
         ORDER BY e.day DESC, e.start_time DESC"
    );
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add a new event for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @param int $goal_id The ID of the goal this event is for
 * @param string $day The date of the event
 * @param string $start_time The start time
 * @param string $end_time The end time
 * @return array An array containing the newly created event's info
 * @throws PDOException if the query fails
 */
function addEventForUser($db, $user_id, $goal_id, $day, $start_time, $end_time)
{
    // Find the next available event_id for this user
    $stmt_max = $db->prepare("SELECT IFNULL(MAX(event_id), 0) + 1 AS next_id FROM event WHERE user_id = :user_id");
    $stmt_max->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_max->execute();
    $row = $stmt_max->fetch(PDO::FETCH_ASSOC);
    $next_event_id = $row['next_id'];

    // Insert the new event
    $stmt_insert = $db->prepare(
        "INSERT INTO event (user_id, event_id, day, start_time, end_time, goal_id) 
         VALUES (:user_id, :event_id, :day, :start_time, :end_time, :goal_id)"
    );
    
    $stmt_insert->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':event_id', $next_event_id, PDO::PARAM_INT);
    $stmt_insert->bindValue(':day', $day, PDO::PARAM_STR);
    $stmt_insert->bindValue(':start_time', $start_time, PDO::PARAM_STR);
    $stmt_insert->bindValue(':end_time', $end_time, PDO::PARAM_STR);
    $stmt_insert->bindValue(':goal_id', $goal_id, PDO::PARAM_INT);
    
    $stmt_insert->execute();

    // Return the data for the new event
    return [
        'user_id' => $user_id,
        'event_id' => $next_event_id,
        'day' => $day,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'goal_id' => $goal_id
    ];
}

function filterEvents($db, $user_id, $goal_id, $day) {
    $sql = "SELECT e.event_id, e.goal_id, e.day, e.start_time, e.end_time,
                   h.category
            FROM event e
            JOIN goal g ON e.user_id = g.user_id AND e.goal_id = g.goal_id
            JOIN habit h ON g.user_id = h.user_id AND g.habit_id = h.habit_id
            WHERE e.user_id = :uid";

    $params = [":uid" => $user_id];

    if (!empty($goal_id)) {
        $sql .= " AND e.goal_id = :gid";
        $params[":gid"] = $goal_id;
    }

    if (!empty($day)) {
        $sql .= " AND e.day = :day";
        $params[":day"] = $day;
    }

    $sql .= " ORDER BY e.day DESC, e.start_time ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>