<?php

/**
 * Create a new room (owner only)
 * Also inserts the creator into roomjoin with rank 0.
 */
function createRoom($db, int $ownerId)
{
    try {
        // Get max room_id for this user
        $stmt = $db->prepare("SELECT MAX(room_id) as max_room FROM room WHERE owner_id = :owner");
        $stmt->execute([':owner' => $ownerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextRoomId = ($row && $row['max_room'] !== null) ? intval($row['max_room']) + 1 : 1;

        // Insert into room table
        $sql = "INSERT INTO room (room_id, owner_id) VALUES (:room, :owner)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':room' => $nextRoomId,
            ':owner' => $ownerId
        ]);

        // Insert owner into roomjoin
        $sql2 = "INSERT INTO roomjoin (room_id, user_id, user_rank)
                 VALUES (:room, :owner, 0)";
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([
            ':room' => $nextRoomId,
            ':owner' => $ownerId
        ]);

        return [
            'room_id' => $nextRoomId,
            'owner_id' => $ownerId
        ];

    } catch (PDOException $e) {
        error_log("createRoom error: " . $e->getMessage());
        return false;
    }
}



/**
 * Get all rooms the user belongs to.
 */
function getAllUserRooms(PDO $db, int $userId): array
{
    $sql = "SELECT 
                r.room_id,
                r.owner_id,
                rj.user_rank
            FROM roomjoin rj
            JOIN room r ON r.room_id = rj.room_id
            WHERE rj.user_id = :uid";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Delete a room (only owner can delete)
 */
function deleteRoom(PDO $db, int $userId, int $roomId): bool
{
    // Verify user is owner
    $stmt = $db->prepare("SELECT owner_id FROM room WHERE room_id = :rid");
    $stmt->execute([':rid' => $roomId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || intval($row['owner_id']) !== $userId) {
        return false;
    }

    // Delete room & members
    try {
        $db->beginTransaction();

        $db->prepare("DELETE FROM roomjoin WHERE room_id = :rid")
           ->execute([':rid' => $roomId]);

        $db->prepare("DELETE FROM room WHERE room_id = :rid")
           ->execute([':rid' => $roomId]);

        $db->commit();
        return true;

    } catch (PDOException $e) {
        $db->rollBack();
        error_log("deleteRoom error: " . $e->getMessage());
        return false;
    }
}


/**
 * Get all members of a room with rank + owner flag.
 */
function getRoomMembers(PDO $db, int $roomId, int $ownerId): array
{
    $sql = "SELECT 
                rj.user_id,
                u.username,
                rj.user_rank,
                (r.owner_id = rj.user_id) AS is_owner
            FROM roomjoin rj
            JOIN useraccount u ON u.user_id = rj.user_id
            JOIN room r ON r.room_id = rj.room_id AND r.owner_id = r.owner_id
            WHERE rj.room_id = :rid
              AND r.owner_id = :oid
            ORDER BY rj.user_rank ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':rid' => $roomId,
        ':oid' => $ownerId
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function inviteUsersToRoom(PDO $db, int $ownerId, int $roomId, array $userIds): bool
{
    try {
        // Check if current user is owner of the room
        $stmt = $db->prepare("SELECT owner_id FROM room WHERE room_id = :rid");
        $stmt->execute([':rid' => $roomId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room || intval($room['owner_id']) !== $ownerId) {
            return false; // Not owner, cannot invite
        }

        $db->beginTransaction();

        $insertStmt = $db->prepare("
            INSERT INTO roomjoin (room_id, user_id, user_rank)
            SELECT :room_id, :user_id, 1
            WHERE NOT EXISTS (
                SELECT 1 FROM roomjoin WHERE room_id = :room_id AND user_id = :user_id
            )
        ");

        foreach ($userIds as $userId) {
            $insertStmt->execute([
                ':room_id' => $roomId,
                ':user_id' => $userId
            ]);
        }

        $db->commit();
        return true;

    } catch (PDOException $e) {
        $db->rollBack();
        error_log("inviteUsersToRoom error: " . $e->getMessage());
        return false;
    }
}

?>
