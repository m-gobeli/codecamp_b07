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

if($num_rows == 0) {
  //
  echo "Error";
}

$array_objects = array();

while($row = mysqli_fetch_assoc($result)) {

  //echo "<option value='".$row['plz']. " " .$row['address'] ."' />";

  // neues JSON Objekt befüllen und zurückliefern
  // $myObj = new \stdClass();
  // $myObj->plz = $row['plz'];
  // $myObj->address = $row['address'];
  // $myObj->state = $row['state'];
  
  // $myJSON = json_encode($myObj);
  //echo $myJSON;

  //

  $array_objects[] = $row;

  }

echo json_encode($array_objects);



mysqli_close($con);
?>
