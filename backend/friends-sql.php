<?php
// friends-sql.php
// Small PDO helper library for friends operations:
// - addFriend($pdo, $userId, $friendId)
// - deleteFriend($pdo, $userId, $friendId)
// - getUserFriends($pdo, $userId)
// Assumes a PDO instance ($pdo) is passed in and a "friends" table exists (see createFriendsTable).

/**
 * Add a friend relationship (user -> friend).
 * @param PDO $db The database connection object
 * Returns inserted row id on success, false on failure or if relationship already exists.
 */
function addFriend($db, int $userId, int $friendId)
{
    try {
        $sql = "CALL AddFriend(:userId, :friendId)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch username
        $query = "SELECT username FROM useraccount WHERE user_id = :friendId";
        $stmt2 = $db->prepare($query);
        $stmt2->bindValue(':friendId', $friendId, PDO::PARAM_INT);
        $stmt2->execute();
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);

        return [
            'friend_id' => $friendId,
            'username' => $row['username'] ?? ''
        ];

    } catch (PDOException $e) {
        // Procedure throws SQLSTATE '45000' for invalid cases
        if ($e->getCode() === '45000' || $e->getCode() === '23000') return false;
        error_log("addFriend error: " . $e->getMessage());
        throw $e;
    }
}



/**
 * Delete a friend relationship (user -> friend).
 *  @param PDO $db The database connection object
 * Returns true if a row was deleted, false if nothing deleted.
 */
function deleteFriend($db, int $userId, int $friendId): bool
{
    $sql = "DELETE FROM friend WHERE user_id_1 = :user_id AND user_id_2 = :friend_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':friend_id' => $friendId]);
    return ($stmt->rowCount() > 0);
}

/**
 * Get all habits for a specific user.
 * @param PDO $db The database connection object
 * @param int $user_id The ID of the user
 * @return array An array of habits
 */
function getAllUserFriends($db, int $userId): array
{
    $query = "SELECT 
                f.user_id_2 AS friend_id,
                u.username
              FROM friend f
              JOIN useraccount u ON u.user_id = f.user_id_2
              WHERE f.user_id_1 = :userId";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $friends;
}
?>