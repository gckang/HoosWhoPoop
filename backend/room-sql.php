<?php

/**
 * Create a new room (owner only)
 * Also inserts the creator into roomjoin with rank 10.
 */
function createRoom(PDO $db, int $ownerId)
{
    try {
        $db->beginTransaction();

        // Insert into room
        $sql = "INSERT INTO room (owner_id) VALUES (:ownerId)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':ownerId', $ownerId, PDO::PARAM_INT);
        $stmt->execute();

        $room_id = $db->lastInsertId();

        // Insert creator into roomjoin
        $sql2 = "INSERT INTO roomjoin (room_id, user_id, user_rank)
                 VALUES (:room_id, :user_id, 10)";
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([
            ':room_id' => $room_id,
            ':user_id' => $ownerId
        ]);

        $db->commit();

        return [
            "room_id" => $room_id,
            "owner_id" => $ownerId
        ];

    } catch (PDOException $e) {
        $db->rollBack();
        throw $e;
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
    // Check if user is owner
    $stmt = $db->prepare("SELECT owner_id FROM room WHERE room_id = :rid");
    $stmt->execute([':rid' => $roomId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || intval($row['owner_id']) !== $userId) {
        return false;
    }

    // Delete room + joins
    $db->beginTransaction();

    $db->prepare("DELETE FROM roomjoin WHERE room_id = :rid")
       ->execute([':rid' => $roomId]);

    $db->prepare("DELETE FROM room WHERE room_id = :rid")
       ->execute([':rid' => $roomId]);

    $db->commit();

    return true;
}

function getRoomMembers(PDO $db, int $roomId): array
{
    $sql = "SELECT 
                rj.user_id,
                u.username,
                rj.user_rank,
                (r.owner_id = rj.user_id) AS is_owner
            FROM roomjoin rj
            JOIN useraccount u ON u.user_id = rj.user_id
            JOIN room r ON r.room_id = rj.room_id
            WHERE rj.room_id = :rid
            ORDER BY rj.user_rank DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute([':rid' => $roomId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>
