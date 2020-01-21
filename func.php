<?php
/* Load file config */
require("config.php");

/* Langage */
if(isset($lang)){
  include 'lang/lang_'.$lang.'.php';
}else{
  include 'lang/lang_en.php';
}

/** Connect data base */
function connectDB() 
{
  require("config.php");
  $con = mysqli_connect("$db_host","$db_user","$db_password","$db_name")
  or die(mysqli_connect_error($con));
  return $con;
}

/** Disconnect data base  */
function disconnectDB($con) 
{
  mysqli_close($con);
}

/** Find Logs WHERE? */
class findlog 
{
  var $res;
  var $lin;
}
function findlog($filter) 
{
  $dataBD = connectDB();
  $located = new findlog;

  $sql = "SELECT * FROM logs WHERE $filter"; 
  $result = $dataBD->query($sql) or die ("Error: $sql");
  $located->lin = $result->num_rows;

  if ($result->num_rows > 0) 
  {
    $located->res = $result->fetch_all(MYSQLI_ASSOC);
  }

  disconnectDB($dataBD);
  return $located;
}

function selectall($filter) 
{
  global $selectall;
  $selectall = findlog($filter);
}


/** connected */
function startlog($user, $mac, $nas, $service, $ipv4, $remoteipv6, $dhcpv6pd) 
{
  $dataBD = connectDB();
  if ($dhcpv6pd == null)
  {
    $sql = "INSERT INTO logs (user, mac, nas, service, ipv4, remoteipv6) VALUES ('$user','$mac','$nas','$service','$ipv4','$remoteipv6');";
  } 
  else 
  {
    $sql = "INSERT INTO logs (user, mac, nas, service, ipv4, remoteipv6, dhcpv6pd) VALUES ('$user','$mac','$nas','$service','$ipv4','$remoteipv6','$dhcpv6pd');";
  }
  $query = mysqli_query($dataBD, $sql) or die ("Error: $sql");
  disconnectDB($dataBD);
}

/** disconnected */
function stoplog($user, $mac, $nas) 
{
  $dataBD = connectDB();
  //$sql = "UPDATE logs SET stop = DATE_SUB(current_timestamp(), INTERVAL 60 SECOND) WHERE stop IS NULL AND user='$user' AND mac='$mac' AND nas='$nas' ORDER BY id ASC LIMIT 1";
  $sql = "UPDATE logs SET stop = current_timestamp() WHERE stop IS NULL AND user='$user' AND mac='$mac' AND nas='$nas' ORDER BY id ASC LIMIT 1";
  $query = mysqli_query($dataBD, $sql) or die ("Error: $sql");
  disconnectDB($dataBD);
}

/** disconnected failure*/
function stopfail($id) 
{
  $dataBD = connectDB();
  $sql = "UPDATE logs SET stop = current_timestamp(), stopcause = 'failure' WHERE id='$id'";
  $query = mysqli_query($dataBD, $sql) or die ("Error: $sql");
  disconnectDB($dataBD);
}