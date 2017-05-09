<?php
$q = intval($_GET['q']);

$con = mysqli_connect('localhost', '134721_7_1', 'SLiQlm7@A=ZE', '134721_7_1');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql="SELECT * FROM Switzerland WHERE CONVERT(plz, CHAR(30)) LIKE '%".$q."%';";

$result = mysqli_query($con,$sql);

// Ein Resultat

$num_rows = mysqli_num_rows($result);
//print_r($num_rows);
if($num_rows == 0) {
  //
  echo "Error";
}

elseif($num_rows == 1){

  $row = mysqli_fetch_array($result);

      echo "<option value='".$row['plz']."' />";

 } else {
  while($row = mysqli_fetch_array($result)) {

      echo "<option value='".$row['plz']. " " .$row['address'] ."' />";

    }

}


mysqli_close($con);
?>
