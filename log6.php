<?php
  require("func.php");
  if(isset($_POST["token"]) && !empty($_POST["token"]))
  {
    if ($_POST["token"] == $token )
    {
      if ($_POST["action"] == "i" )
      {
        # Verificar se existe uma conexao aberta deste login?
        $user = $_POST["user"];
        selectall("stop IS NULL AND user='$user'");

        if ($selectall->lin >= '1') 
        {
          $dataBD = connectDB();
          foreach ($selectall->res as $n) :
            #echo $n['id'] . " -> ". $n['start'];
            $id_fail = $n['id'];

            $sql = "UPDATE logs SET stop = current_timestamp(), stopcause = 'failure' WHERE id='$id_fail'";
            $query = mysqli_query($dataBD, $sql) or die ("Error: $sql");  

          endforeach;
          disconnectDB($dataBD);
        }

        $dhcpv6pd = $_POST["dhcpv6pd"] ?? null;
        startlog($_POST["user"],$_POST["mac"],$_POST["nas"],$_POST["service"],$_POST["ipv4"],$_POST["remoteipv6"],$dhcpv6pd);
      }
      else 
      {
        stoplog($_POST["user"],$_POST["mac"],$_POST["nas"]);
      }
    }
  }
  else {
    die;
  }
?>
