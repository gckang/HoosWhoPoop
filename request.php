<?php 
// header('Access-Control-Allow-Origin: http://localhost:4200');
?>

<!-- put connection at the top to not make the user wait -->
<!-- only let user use the page if the database connects -->
<?php 
require('connect-db.php');    // include
require('request-db.php');

$list_of_requests = getAllRequests();
$edit_request = null;
//var_dump($list_of_requests);
?>
<?php
// $ means variable
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (!empty($_POST['addBtn']))   // post object automatically created
  {
    addRequests($_POST['requestedDate'], $_POST['roomNo'], $_POST['requestedBy'], $_POST['requestDesc'], $_POST['priority_option']);
  }
  else if (!empty($_POST['deleteBtn']))
  {
    deleteRequest($_POST['reqId']);
  }
  else if (!empty($_POST['updateBtn']))
  {
    $edit_request = getRequestById($_POST['reqId']);
  }
  else if(!empty($_POST['cofmBtn']))
  {
    //var_dump($_POST);
    updateRequest($_POST['reqId'], $_POST['requestedDate'], $_POST['roomNo'], $_POST['requestedBy'], $_POST['requestDesc'], $_POST['priority_option']);
  }
  $list_of_requests = getAllRequests(); // refresh table data
} 
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">    
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- always include viewport to make it responsive, initial-scale = 1 rly important! (screensize) -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Grace Kang (kng4jc), Blaire Zhao (zfj4ee), Michelle Lin (ghp9rp)">
  <meta name="description" content="Maintenance request form, a small/toy web app for ISP homework assignment, used by CS 3250 (Software Testing)">
  <meta name="keywords" content="CS 3250, Upsorn, Praphamontripong, Software Testing">
  <link rel="icon" href="https://www.cs.virginia.edu/~up3f/cs3250/images/st-icon.png" type="image/png" />  
  
  <title>Maintenance Services</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
  <link rel="stylesheet" href="maintenance-system.css">  
</head>

<body>
<!-- php is an included language, will only be used where defined -->
<?php include('header.php') ?>

<body>  
<div class="container">
  <div class="row g-3 mt-2">
    <div class="col">
      <h2>Maintenance Request</h2>
    </div>  
  </div>
  
  <!---------------->

  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validateInput()">
    <!-- Hidden reqID field (used when editing) -->
    <input type="hidden" name="reqId" 
           value="<?php echo $edit_request['reqId'] ?? ''; ?>">

    <table style="width:98%">
      <tr>
        <td width="50%">
          <div class='mb-3'>
            Requested date:
            <input type='text' class='form-control' 
                   id='requestedDate' name='requestedDate' 
                   placeholder='Format: yyyy-mm-dd' 
                   pattern="\d{4}-\d{1,2}-\d{1,2}" 
                   value="<?php echo $edit_request['reqDate'] ?? ''; ?>" />
                   <!-- php if ($edit_request !=null) echo $edit_request['reqDate']; -->
          </div>
        </td>
        <td>
          <div class='mb-3'>
            Room Number:
            <input type='text' class='form-control' id='roomNo' name='roomNo' 
                   value="<?php echo $edit_request['roomNumber'] ?? ''; ?>" />
          </div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          <div class='mb-3'>
            Requested by: 
            <input type='text' class='form-control' id='requestedBy' name='requestedBy'
                   placeholder='Enter your name'
                   value="<?php echo $edit_request['reqBy'] ?? ''; ?>" />
          </div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          <div class="mb-3">
            Description of work/repair:
            <input type='text' class='form-control' id='requestDesc' name='requestDesc'
                   value="<?php echo $edit_request['repairDesc'] ?? ''; ?>" />
          </div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          <div class='mb-3'>
            Requested Priority:
            <select class='form-select' id='priority_option' name='priority_option'>
              <option value=''></option>
              <!-- if ($edit_request != null && $edit_request['reqPriority']=='high') echo 'selected="selected" ' -->
              <option value='high'<?php if (($edit_request['reqPriority'] ?? '')=='high') echo 'selected'; ?>>High - Must be done within 24 hours</option>
              <option value='medium'<?php if (($edit_request['reqPriority'] ?? '')=='medium') echo 'selected'; ?>>Medium - Within a week</option>
              <option value='low'<?php if (($edit_request['reqPriority'] ?? '')=='low') echo 'selected'; ?>>Low - When you get a chance</option>
            </select>
          </div>
        </td>
      </tr>
    </table>

    <div class="row g-3 mx-auto">    
      <div class="col-4 d-grid">
        <input type="submit" value="Add" id="addBtn" name="addBtn" class="btn btn-dark"
               title="Submit a maintenance request"
        />                  
      </div>	    
      <div class="col-4 d-grid">
        <input type="submit" value="Confirm update" id="cofmBtn" name="cofmBtn" class="btn btn-primary"
               title="Update a maintenance request"
        />                  
      </div>	    
      <div class="col-4 d-grid">
        <input type="reset" value="Clear form" name="clearBtn" id="clearBtn" class="btn btn-secondary" />
      </div>      
    </div>  
  </form>
</div>


<hr/>
<div class="container">
<h3>List of requests</h3>
<div class="row justify-content-center">  
<table class="w3-table w3-bordered w3-card-4 center" style="width:100%">
  <thead>
  <tr style="background-color:#B0B0B0">
    <th width="30%"><b>ReqID</b></th>
    <th width="30%"><b>Date</b></th>         
    <th width="30%"><b>Room#</b></th> 
    <th width="30%"><b>By</b></th>
    <th width="30%"><b>Description</b></th>        
    <th width="30%"><b>Priority</b></th> 
    <th><b>Update</b></th>
    <th><b>Delete</b></th>
  </tr>
  </thead>
  <?php foreach ($list_of_requests as $req_info): ?>
  <tr>
    <td><?php echo $req_info['reqId']?></td>
    <td><?php echo $req_info['reqDate']?></td>
    <td><?php echo $req_info['roomNumber']?></td>
    <td><?php echo $req_info['reqBy']?></td>
    <td><?php echo $req_info['repairDesc']?></td>
    <td><?php echo $req_info['reqPriority']?></td>
    <td>
      <form action="request.php" method="post">
      <input type="submit" value="Update"
              name="updateBtn" class="btn btn-primary"
              title="Click to update this request"
      />
      <input type="hidden" name="reqId"
              value="<?php echo $req_info['reqId']?>"
      />
    </td>
    <td>
      <!-- post encapsulates as an object, get grabs all the data in the form and add it in the url address -->
      <form action="request.php" method="post">
        <input type="submit" value="Delete"
                name="deleteBtn" class="btn btn-danger"
                title="Click to delete this request"
        />
        <input type="hidden" name="reqId"
                value="<?php echo $req_info['reqId']?>"
        />
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>   


<br/><br/>

<?php // include('footer.html') ?> 

<!-- <script src='maintenance-system.js'></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>