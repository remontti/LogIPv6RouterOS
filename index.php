<?php
  require("func.php");
  session_start();
  $today = date("Y-m");
  if ((!isset($_SESSION['auth'])) and ($_SESSION['auth'] != md5("$token"))) {
    $form_toke = true;
    $form_search = false;
  } 
  else { 
    $form_toke = false;
    $form_search = true;
  }
  if(isset($_POST["token"]) && !empty($_POST["token"])){
    if ($_POST["token"] ==  $token ){
      $_SESSION['auth'] = md5("$token");
      header('location: ' . dirname( $_SERVER['SERVER_NAME'] ) . '/');
    }
    else {
      session_destroy();
    }
  }

?>
<!DOCTYPE html>
<html lang="<?= _LANGAGE ?>">
<head>
  <title>Mikrotik LOGs IPv6</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <a class="navbar-brand" href="#">
    <img src="icon.png" width="24" height="24" class="d-inline-block align-top" alt="">
    Mikrotik LOGs IPv6
  </a>      
  <?php if ($form_search == true) : ?>
  <a class="btn btn-outline-danger my-2 my-sm-0" href="logout.php"><?= _LOGOUT ?></a>
  <?php endif; ?>
</nav>
<div class="container-fluid" style="padding: 40px;">
<?php if ($form_toke == true) : ?>
  <form method="POST">
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <label for="token">Token:</label>
          <input type="password" class="form-control" id="token" name="token" placeholder="<?= _TOKENCODE ?>">
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          <button type="submit" class="btn btn-default"><?= _AUTHENTICATE ?></button>
        </div>
      </div>
    </div>
  </form>
<?php 
  endif;
  if ($form_search == true) :
?>
  <form method="POST">
    <div class="row">
      <div class="col-sm-4">
        <div class="form-group">
          <label for="ip">IP</label>
          <input type="text" class="form-control" id="ip" name="ip" placeholder="2001:db8:abcd:100::/56">
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <label for="start"><?= _DATESTART ?></label>
          <input type="text" class="form-control" id="start" name="start" placeholder="<?php echo $today; ?>-01 00:00:00" value="<?php echo $today; ?>-01 00:00:00">
        </div>
      </div>
      <div class="col-sm-4">
        <div class="form-group">
          <label for="stop"><?= _DATESTOP ?></label>
          <input type="text" class="form-control" id="stop" name="stop" placeholder="<?php echo $today; ?>-31 23:59:59" value="<?php echo $today; ?>-31 23:59:59">
        </div>
      </div>
      <div class="col-sm-8 justify-content-center">
        <div class="checkbox">          
          <label class="col-md-3"><input type="radio" name="column" checked="" value="dhcpv6pd"> DHCPv6 PD Pool</label>
          <label class="col-md-3"><input type="radio" name="column" value="remoteipv6"> Remote IPv6 Prefix Pool</label>
          <label class="col-md-3"><input type="radio" name="column" value="ipv4"> Remote IPv4</label>          
        </div>
      </div>
      <div class="col-sm-4">
        <button type="submit" class="btn btn-default btn-primary"><?= _SEARCH ?></button>
      </div>
    </div>
  </form>
  <div class="row">
    <?php 
    if(isset($_POST["ip"]) && !empty($_POST["ip"])) : 
    $ipaddr = $_POST["ip"];
    $column = $_POST["column"];
    $start = $_POST["start"];
    $stop = $_POST["stop"];
    selectall("$column LIKE '%$ipaddr%' AND start > '$start' AND  stop < '$stop' OR $column LIKE '%$ipaddr%' AND start > '$start' AND stop IS NULL ORDER BY id DESC");
    if ($selectall->lin >= '1') 
    {
    ?>
    <table class="table" style="margin-top: 20px;">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Start</th>
          <th>Stop</th>
          <th>Stop Cause</th>
          <th>User</th>
          <th>MAC</th>
          <th>NAS</th>
          <th>PPP Service</th>
          <th>IPv4</th>
          <th>IPv6 WAN</th>
          <th>IPv6 LAN</th>
        </tr>
      </thead>
      <tbody>
      <?php

          foreach ($selectall->res as $n)
          {                
            if ($n['stopcause'] == "failure"){ 
              $class = "class=\"danger\"";
              $text = "text-danger font-weight-bold";
            } 
            else { 
              $class = null;
              $text = null;
            } 
            echo "<tr $class>";
            echo "<td>". $n['id'] ."</td>";
            echo "<td>". $n['start'] ."</td>";
            echo "<td class=\"$text\">". (($n['stop'] == NULL) ? _CONNECTED : $n['stop']) ."</td>";
            echo "<td class=\"$text\">". $n['stopcause'] ."</td>";
            echo "<td>". $n['user'] ."</td>";
            echo "<td>". $n['mac'] ."</td>";
            echo "<td>". $n['nas'] ."</td>";
            echo "<td>". $n['service'] ."</td>";
            echo "<td>". $n['ipv4'] ."</td>";
            echo "<td>". $n['remoteipv6'] ."</td>";
            echo "<td>". $n['dhcpv6pd'] ."</td>";
            echo "</tr>";
          }
    
      ?>
      </tbody>
    </table>
    <?php 
    } 
    else {
      echo '<div class="alert alert-danger">';
      echo _NOFOUND;
      echo '</div>';
    }
    endif; 
    ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
