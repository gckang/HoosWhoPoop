<?php
function addRequests($reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db; // use the same database (the one defined in connect-db.php)
    
    // if you don't want to skip columns, can just do requests VALUES
    // concat inputs as strings
    //$query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES ('" . $reqDate . "', '". $roomNumber ."', '". $reqBy ."', '". $repairDesc ."', '". $reqPriority ."')";
    
    // template
        $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority)
                  VALUES (:reqDate, :roomNumber, :reqBy, :repairDesc, :reqPriority)";
    
    try {
        //$statement = $db->query($query);  // bad, prone to sql injection

        // good way
        $statement = $db->prepare($query);  // compile, leaves fill-in-the-blank / template
        $statement->bindValue(':reqDate', $reqDate);
        $statement->bindValue(':roomNumber', $roomNumber);
        $statement->bindValue(':reqBy', $reqBy);
        $statement->bindValue(':repairDesc', $repairDesc);
        $statement->bindValue(':reqPriority', $reqPriority);
        $statement->execute();  // run

        $statement->closeCursor();
    }
    catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();  // be careful, don't want to show ppl the real error message (make it more generic)
    }
    catch (Exception $e) {
        echo "General error: " . $e->getMessage();
    }

}

function getAllRequests()
{
   global $db;

   $query = "SELECT * FROM requests";
   $statement = $db->prepare($query);
   $statement->execute();
   $results = $statement->fetchAll();   // fetch()
   $statement->closeCursor();

   return $results;
}

function getRequestById($id)  
{
    global $db;

    $query = "SELECT * FROM requests WHERE reqId = :reqId";
    $statement = $db->prepare($query);
    $statement->bindValue(':reqId', $id);
    $statement->execute();
    $result = $statement->fetch(); 
    $statement->closeCursor();

    return $result;
}

function updateRequest($reqId, $reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;

    $query = "UPDATE requests
              SET reqDate = :reqDate, roomNumber = :roomNumber, reqBy = :reqBy, repairDesc = :repairDesc, reqPriority = :reqPriority
              WHERE reqId = :reqId";
    $statement = $db->prepare($query);
    $statement->bindValue(':reqDate', $reqDate);
    $statement->bindValue(':roomNumber', $roomNumber);
    $statement->bindValue(':reqBy', $reqBy);
    $statement->bindValue(':repairDesc', $repairDesc);
    $statement->bindValue(':reqPriority', $reqPriority);
    $statement->bindValue(':reqId', $reqId);
    $statement->execute();
    $statement->closeCursor();

}

function deleteRequest($reqId)
{
    global $db;

    $query = "DELETE FROM requests WHERE reqId=:fillin";
    $statement = $db->prepare($query);
    $statement->bindValue(':fillin', $reqId);
    $statement->execute();
    $statement->closeCursor();
    
}

?>
