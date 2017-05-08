<?php
$q = intval($_GET['q']);

$con = mysqli_connect('localhost', '134721_7_1', 'SLiQlm7@A=ZE', '134721_7_1');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql="SELECT * FROM Switzerland WHERE plz = '".$q."'";
$result = mysqli_query($con,$sql);

while($row = mysqli_fetch_array($result)) {
    echo $row['address'];
}

mysqli_close($con);
?>
