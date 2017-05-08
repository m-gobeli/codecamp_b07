<?php
$q = intval($_GET['q']);

$con = mysqli_connect('localhost', '134721_7_1', 'SLiQlm7@A=ZE', '134721_7_1');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql="SELECT * FROM Switzerland WHERE plz = '".$q."'";
$result = mysqli_query($con,$sql);

// Ein Resultat

$num_rows = mysqli_num_rows($result);
//print_r($num_rows);
if($num_rows == 0) {
  //
  print_r("kein Eintrag");
}

elseif($num_rows == 1){

  $row = mysqli_fetch_array($result);

      echo $row['address'];
      echo "//";
      echo $row['state'];

 } else {
//echo "<datalist>";
  while($row = mysqli_fetch_array($result)) {
      echo '<option value="' . $row['address'] . ', ' . $row['state'] . '" />';
    }
//echo "</datalist>";
}


mysqli_close($con);
?>
