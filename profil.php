<?php
session_start();
if(!isset($_SESSION['id'])){
  header("Location:index.php");
}else{
  $user_id = $_SESSION['id'];
}

require_once('system/data.php');
require_once('system/security.php');

if(isset($_POST['update-submit']))
{
  $email = filter_data($_POST['email']);
  $password = filter_data($_POST['password']);
  $confirm_password = filter_data($_POST['confirm-password']);
  $gender = filter_data($_POST['gender']);
  $firstname = filter_data($_POST['firstname']);
  $lastname = filter_data($_POST['lastname']);
  $country = filter_data($_POST['country']);
  $plz = filter_data($_POST['plz']);
  $ort = filter_data($_POST['ort']);

  $image_name = "";

  $result = get_user($user_id);
  $user = mysqli_fetch_assoc($result);
  $image_name = $user['img_src'];

  $upload_path = "user_img/";
  $max_file_size = 500000;
  $upload_ok = true;

  if ($_FILES['profil_img']['name'] != "") {
    $filetype = $_FILES['profil_img']['type'];
    switch($filetype){
      case "image/jpg":
      $file_extension = "jpg";
      break;
      case "image/jpeg":
      $file_extension = "jpg";
      break;
      case "image/gif":
      $file_extension = "gif";
      break;
      case "image/png":
      $file_extension = "png";
      break;
      default:
      $upload_ok = false;
    }

    $upload_filesize = $_FILES['profil_img']['size'];
    if($upload_filesize >= $max_file_size){
      $upload_ok = false;
      echo "Leider ist die Datei mit $upload_filesize KB zu gross. <br> Sie darf nicht grösser als $max_file_size sein. ";
    }

    if($upload_ok){
      $image_name = time() . "_" . $user['user_id'] . "." . $file_extension;
      move_uploaded_file($_FILES['profil_img']['tmp_name'], $upload_path . $image_name);
    }else{
      echo "Leider konnte die Datei nicht hochgeladen werden. ";
    }
  }
  $result = update_user($user_id, $email, $password, $confirm_password, $gender, $firstname, $lastname, $image_name, $country, $plz, $ort);
}


$result = get_user($user_id);
$user = mysqli_fetch_assoc($result);

$update_time = date_parse($user['update_time']);
$last_update = $update_time['day'] . "." . $update_time['month'] . "." . $update_time['year'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Die drei vorausgehenden meta-Tags *müssen* vor allen anderen Inhalten des head stehen -->
  <title>p42 - Profil</title>
  <!-- Bootstrap Styles -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- eigene Styles -->
  <link rel="stylesheet" href="css/p42_style.css">

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>




</head>
<body>
  <!-- Navigation -->
  <!-- http://getbootstrap.com/components/#navbar -->
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#p42-navbar" aria-expanded="false">
          <!-- Übersetzung für Screenreader-Text -->
          <span class="sr-only">Menü anzeigen</span>
          <!-- Wir ersetzen drei waagerechte Striche (Burgermenü) durch Glyphicon -->
          <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
        </button>
        <a class="navbar-brand" href="#">p42</a>
      </div>
      <!-- Sichtbarer Inhalt des Menüs -->
      <div class="collapse navbar-collapse" id="p42-navbar">
        <ul class="nav navbar-nav">
          <li><a href="home.php">Home</a></li>
          <!-- Der Menüpunkt der aktuellen Seite ist mit class="active" markiert und ist nicht verlinkt -->
          <li class="active"><a href="#">Profil</a></li>
          <li><a href="friends.php">Freunde finden</a></li>
        </ul>
        <!-- rechtsbündiger Inhalt -->
        <ul class="nav navbar-nav navbar-right">
          <li><a href="index.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav><!-- /Navigation -->

  <div class="container">
    <div class="panel panel-default container-fluid"> <!-- fluid -->
      <div class="panel-heading row">
        <div class="col-sm-6">
          <h4>Persönliche Einstellungen</h4>
        </div>
        <!-- Button für die Einblendung des modalen Fensters zur Userdatenaktualisierung -->
        <div class="col-xs-6 text-right">
          <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#myModal">Profil anpassen</button>
        </div>
        <!-- /Button Userdatenaktualisierung -->
      </div>
      <div class="panel-body">
        <div class="col-sm-3">
          <!-- /Profilbild -->
          <img src="user_img/<?php echo $user['img_src']; ?>" alt="Profilbild" class="img-responsive">
          <!-- Profilbild -->
        </div>
        <div class="col-sm-9">
          <!-- Profildaten des Users -->
          <dl class="dl-horizontal lead">
            <dt>Name</dt>
            <dd><?php echo $user['firstname'] . " " . $user['lastname']; ?></dd>

            <!--<dt>Nutzername</dt>
            <dd>wobo</dd>-->

            <dt>E-Mail</dt>
            <dd><?php echo $user['email']; ?></dd>

            <dt>letzte Änderung</dt>
            <dd>Ihr Profil wurde zuletzt am <?php echo $last_update; ?> aktualisiert.</dd>
          </dl>
          <!-- / Profildaten des Users -->
        </div>
      </div>
    </div>

    <!-- Modales Fenster zur Userdatenaktualisierung-->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="p42-profil-modalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="modal-header">
              <h4 class="modal-title" id="p42-profil-modalLabel">persönliche Einstellungen</h4>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                <label for="Gender" class="col-sm-2 form-control-label">Anrede</label>
                <div class="col-sm-5">
                  <select class="form-control form-control-sm" id="Gender" name="gender">
                    <option <?php if($user['gender'] == "") echo "selected"; ?> value="">--</option>
                    <option <?php if($user['gender'] == "Frau") echo "selected"; ?> value="Frau">Frau</option>
                    <option <?php if($user['gender'] == "Herr") echo "selected"; ?> value="Herr">Herr</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="Vorname" class="col-sm-2 col-xs-12 form-control-label">Name</label>
                <div class="col-sm-5 col-xs-6">
                  <input  type="text" class="form-control form-control-sm"
                  id="Vorname" placeholder="Vorname"
                  name="firstname" value="<?php echo $user['firstname']; ?>">
                </div>
                <div class="col-sm-5 col-xs-6">
                  <input  type="text" class="form-control form-control-sm"
                  id="Nachname" placeholder="Nachname"
                  name="lastname" value="<?php echo $user['lastname']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="Email" class="col-sm-2 form-control-label">E-Mail</label>
                <div class="col-sm-10">
                  <input  type="email" class="form-control form-control-sm"
                  id="Email" placeholder="E-Mail" required
                  name="email" value="<?php echo $user['email']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="Passwort" class="col-sm-2 form-control-label">Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control form-control-sm" id="Passwort" placeholder="Passwort" name="password">
                </div>
              </div>
              <div class="form-group row">
                <label for="Passwort_Conf" class="col-sm-2 form-control-label">Passwort bestätigen</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control form-control-sm" id="Passwort_Conf" placeholder="Passwort" name="confirm-password">
                </div>
              </div>

              <div class="form-group row">
                <!-- http://plugins.krajee.com/file-input -->
                <label for="Tel" class="col-sm-2 form-control-label">Profilbild</label>
                <div class="col-sm-10">
                  <input type="file" name="profil_img">
                </div>
              </div>


			  <hr>


              <div class="form-group row">
                <label for="PLZ" class="col-sm-2 form-control-label">Land</label>
                <div class="col-sm-10">
					<input id="country_name" name="country" type="text" list="country" placeholder="<?php echo $user['country']; ?>"/>
					<datalist id="country">
            <option <?php if($user['country'] == "") echo "selected"; ?> value="Bitte wählen" />
						<option value="Afghanistan" <?php if($user['country'] == "Afghanistan") echo "selected"; ?> />
						<option value="Albania" <?php if($user['country'] == "Albania") echo "selected"; ?> />
						<option value="Algeria" <?php if($user['country'] == "Algeria") echo "selected"; ?> />
						<option value="American Samoa" <?php if($user['country'] == "American Samoa") echo "selected"; ?> />
						<option value="Andorra" <?php if($user['country'] == "Andorra") echo "selected"; ?> />
						<option value="Angola" <?php if($user['country'] == "Angola") echo "selected"; ?> />
						<option value="Anguilla" <?php if($user['country'] == "Anguilla") echo "selected"; ?> />
						<option value="Antarctica" <?php if($user['country'] == "Antarctica") echo "selected"; ?> />
						<option value="Antigua and Barbuda" <?php if($user['country'] == "Antigua and Barbuda") echo "selected"; ?> />
						<option value="Argentina" <?php if($user['country'] == "Argentina") echo "selected"; ?> />
						<option value="Armenia" <?php if($user['country'] == "Armenia") echo "selected"; ?> />
						<option value="Aruba" <?php if($user['country'] == "Aruba") echo "selected"; ?> />
						<option value="Australia" <?php if($user['country'] == "Australia") echo "selected"; ?> />
						<option value="Austria" <?php if($user['country'] == "Austria") echo "selected"; ?> />
						<option value="Azerbaijan" <?php if($user['country'] == "Azerbaijan") echo "selected"; ?> />
						<option value="Bahamas" <?php if($user['country'] == "Bahamas") echo "selected"; ?> />
						<option value="Bahrain" <?php if($user['country'] == "Bahrain") echo "selected"; ?> />
						<option value="Bangladesh" <?php if($user['country'] == "Bangladesh") echo "selected"; ?> />
						<option value="Barbados" <?php if($user['country'] == "Barbados") echo "selected"; ?> />
						<option value="Belarus" <?php if($user['country'] == "Belarus") echo "selected"; ?> />
						<option value="Belgium" <?php if($user['country'] == "Belgium") echo "selected"; ?> />
						<option value="Belize" <?php if($user['country'] == "Belize") echo "selected"; ?> />
						<option value="Benin" <?php if($user['country'] == "Benin") echo "selected"; ?> />
						<option value="Bermuda" <?php if($user['country'] == "Bermuda") echo "selected"; ?> />
						<option value="Bhutan" <?php if($user['country'] == "Bhutan") echo "selected"; ?> />
						<option value="Bolivia" <?php if($user['country'] == "Bolivia") echo "selected"; ?> />
						<option value="Bosnia and Herzegovina" <?php if($user['country'] == "Bosnia and Herzegovina") echo "selected"; ?> />
						<option value="Botswana" <?php if($user['country'] == "Botswana") echo "selected"; ?> />
						<option value="Bouvet Island" <?php if($user['country'] == "Bouvet Island") echo "selected"; ?> />
						<option value="Brazil" <?php if($user['country'] == "Brazil") echo "selected"; ?> />
						<option value="British Indian Ocean Territory" <?php if($user['country'] == "British Indian Ocean Territory") echo "selected"; ?> />
						<option value="Brunei Darussalam" <?php if($user['country'] == "Brunei Darussalam") echo "selected"; ?> />
						<option value="Bulgaria" <?php if($user['country'] == "Bulgaria") echo "selected"; ?> />
						<option value="Burkina Faso" <?php if($user['country'] == "Burkina Faso") echo "selected"; ?> />
						<option value="Burundi" <?php if($user['country'] == "Burundi") echo "selected"; ?> />
						<option value="Cambodia" <?php if($user['country'] == "Cambodia") echo "selected"; ?> />
						<option value="Cameroon" <?php if($user['country'] == "Cameroon") echo "selected"; ?> />
						<option value="Canada" <?php if($user['country'] == "Canada") echo "selected"; ?> />
						<option value="Cape Verde" <?php if($user['country'] == "Cape Verde") echo "selected"; ?> />
						<option value="Cayman Islands" <?php if($user['country'] == "Cayman Islands") echo "selected"; ?> />
						<option value="Central African Republic" <?php if($user['country'] == "Central African Republic") echo "selected"; ?> />
						<option value="Chad" <?php if($user['country'] == "Chad") echo "selected"; ?> />
						<option value="Chile" <?php if($user['country'] == "Chile") echo "selected"; ?> />
						<option value="China" <?php if($user['country'] == "China") echo "selected"; ?> />
						<option value="Christmas Island" <?php if($user['country'] == "Christmas Island") echo "selected"; ?> />
						<option value="Cocos (Keeling) Islands" <?php if($user['country'] == "Cocos (Keeling) Islands") echo "selected"; ?> />
						<option value="Colombia" <?php if($user['country'] == "Colombia") echo "selected"; ?> />
						<option value="Comoros" <?php if($user['country'] == "Comoros") echo "selected"; ?> />
						<option value="Congo" <?php if($user['country'] == "Congo") echo "selected"; ?> />
						<option value="Congo, The Democratic Republic of The" <?php if($user['country'] == "Congo, The Democratic Republic of The") echo "selected"; ?> />
						<option value="Cook Islands" <?php if($user['country'] == "Cook Islands") echo "selected"; ?> />
						<option value="Costa Rica" <?php if($user['country'] == "Costa Rica") echo "selected"; ?> />
						<option value="Cote D'ivoire" <?php if($user['country'] == "Cote D'ivoire") echo "selected"; ?> />
						<option value="Croatia" <?php if($user['country'] == "Croatia") echo "selected"; ?> />
						<option value="Cuba" <?php if($user['country'] == "Cuba") echo "selected"; ?> />
						<option value="Cyprus" <?php if($user['country'] == "Cyprus") echo "selected"; ?> />
						<option value="Czech Republic" <?php if($user['country'] == "Czech Republic") echo "selected"; ?> />
						<option value="Denmark" <?php if($user['country'] == "Denmark") echo "selected"; ?> />
						<option value="Djibouti" <?php if($user['country'] == "Djibouti") echo "selected"; ?> />
						<option value="Dominica" <?php if($user['country'] == "Dominica") echo "selected"; ?> />
						<option value="Dominican Republic" <?php if($user['country'] == "Dominican Republic") echo "selected"; ?> />
						<option value="Ecuador" <?php if($user['country'] == "Ecuador") echo "selected"; ?> />
						<option value="Egypt" <?php if($user['country'] == "Egypt") echo "selected"; ?> />
						<option value="El Salvador" <?php if($user['country'] == "El Salvador") echo "selected"; ?> />
						<option value="Equatorial Guinea" <?php if($user['country'] == "Equatorial Guinea") echo "selected"; ?> />
						<option value="Eritrea" <?php if($user['country'] == "Eritrea") echo "selected"; ?> />
						<option value="Estonia" <?php if($user['country'] == "Estonia") echo "selected"; ?> />
						<option value="Ethiopia" <?php if($user['country'] == "Ethiopia") echo "selected"; ?> />
						<option value="Falkland Islands (Malvinas)" <?php if($user['country'] == "Falkland Islands (Malvinas)") echo "selected"; ?> />
						<option value="Faroe Islands" <?php if($user['country'] == "Faroe Islands") echo "selected"; ?> />
						<option value="Fiji" <?php if($user['country'] == "Fiji") echo "selected"; ?> />
						<option value="Finland" <?php if($user['country'] == "Finland") echo "selected"; ?> />
						<option value="France" <?php if($user['country'] == "France") echo "selected"; ?> />
						<option value="French Guiana" <?php if($user['country'] == "French Guiana") echo "selected"; ?> />
						<option value="French Polynesia" <?php if($user['country'] == "French Polynesia") echo "selected"; ?> />
						<option value="French Southern Territories" <?php if($user['country'] == "French Southern Territories") echo "selected"; ?> />
						<option value="Gabon" <?php if($user['country'] == "Gabon") echo "selected"; ?> />
						<option value="Gambia" <?php if($user['country'] == "Gambia") echo "selected"; ?> />
						<option value="Georgia" <?php if($user['country'] == "Georgia") echo "selected"; ?> />
						<option value="Germany" <?php if($user['country'] == "Germany") echo "selected"; ?> />
						<option value="Ghana" <?php if($user['country'] == "Ghana") echo "selected"; ?> />
						<option value="Gibraltar" <?php if($user['country'] == "Gibraltar") echo "selected"; ?> />
						<option value="Greece" <?php if($user['country'] == "Greece") echo "selected"; ?> />
						<option value="Greenland" <?php if($user['country'] == "Greenland") echo "selected"; ?> />
						<option value="Grenada" <?php if($user['country'] == "Grenada") echo "selected"; ?> />
						<option value="Guadeloupe" <?php if($user['country'] == "Guadeloupe") echo "selected"; ?> />
						<option value="Guam" <?php if($user['country'] == "Guam") echo "selected"; ?> />
						<option value="Guatemala" <?php if($user['country'] == "Guatemala") echo "selected"; ?> />
						<option value="Guinea" <?php if($user['country'] == "Guinea") echo "selected"; ?> />
						<option value="Guinea-bissau" <?php if($user['country'] == "Guinea-bissau") echo "selected"; ?> />
						<option value="Guyana" <?php if($user['country'] == "Guyana") echo "selected"; ?> />
						<option value="Haiti" <?php if($user['country'] == "Haiti") echo "selected"; ?> />
						<option value="Heard Island and Mcdonald Islands" <?php if($user['country'] == "Heard Island and Mcdonald Islands") echo "selected"; ?> />
						<option value="Holy See (Vatican City State)" <?php if($user['country'] == "Holy See (Vatican City State)") echo "selected"; ?> />
						<option value="Honduras" <?php if($user['country'] == "Honduras") echo "selected"; ?> />
						<option value="Hong Kong" <?php if($user['country'] == "Hong Kong") echo "selected"; ?> />
						<option value="Hungary" <?php if($user['country'] == "Hungary") echo "selected"; ?> />
						<option value="Iceland" <?php if($user['country'] == "Iceland") echo "selected"; ?> />
						<option value="India" <?php if($user['country'] == "India") echo "selected"; ?> />
						<option value="Indonesia" <?php if($user['country'] == "Indonesia") echo "selected"; ?> />
						<option value="Iran, Islamic Republic of" <?php if($user['country'] == "Iran, Islamic Republic of") echo "selected"; ?> />
						<option value="Iraq" <?php if($user['country'] == "Iraq") echo "selected"; ?> />
						<option value="Ireland" <?php if($user['country'] == "Ireland") echo "selected"; ?> />
						<option value="Israel" <?php if($user['country'] == "Israel") echo "selected"; ?> />
						<option value="Italy" <?php if($user['country'] == "Italy") echo "selected"; ?> />
						<option value="Jamaica" <?php if($user['country'] == "Jamaica") echo "selected"; ?> />
						<option value="Japan" <?php if($user['country'] == "Japan") echo "selected"; ?> />
						<option value="Jordan" <?php if($user['country'] == "Jordan") echo "selected"; ?> />
						<option value="Kazakhstan" <?php if($user['country'] == "Kazakhstan") echo "selected"; ?> />
						<option value="Kenya" <?php if($user['country'] == "Kenya") echo "selected"; ?> />
						<option value="Kiribati" <?php if($user['country'] == "Kiribati") echo "selected"; ?> />
						<option value="Korea, Democratic People's Republic of" <?php if($user['country'] == "Korea, Democratic People's Republic of") echo "selected"; ?> />
						<option value="Korea, Republic of" <?php if($user['country'] == "Korea, Republic of") echo "selected"; ?> />
						<option value="Kuwait" <?php if($user['country'] == "Kuwait") echo "selected"; ?> />
						<option value="Kyrgyzstan" <?php if($user['country'] == "Kyrgyzstan") echo "selected"; ?> />
						<option value="Lao People's Democratic Republic" <?php if($user['country'] == "Lao People's Democratic Republic") echo "selected"; ?> />
						<option value="Latvia" <?php if($user['country'] == "Latvia") echo "selected"; ?> />
						<option value="Lebanon" <?php if($user['country'] == "Lebanon") echo "selected"; ?> />
						<option value="Lesotho" <?php if($user['country'] == "Lesotho") echo "selected"; ?> />
						<option value="Liberia" <?php if($user['country'] == "Liberia") echo "selected"; ?> />
						<option value="Libyan Arab Jamahiriya" <?php if($user['country'] == "Libyan Arab Jamahiriya") echo "selected"; ?> />
						<option value="Liechtenstein" <?php if($user['country'] == "Liechtenstein") echo "selected"; ?> />
						<option value="Lithuania" <?php if($user['country'] == "Lithuania") echo "selected"; ?> />
						<option value="Luxembourg" <?php if($user['country'] == "Luxembourg") echo "selected"; ?> />
						<option value="Macao" <?php if($user['country'] == "Macao") echo "selected"; ?> />
						<option value="Macedonia, The Former Yugoslav Republic of" <?php if($user['country'] == "Macedonia, The Former Yugoslav Republic of") echo "selected"; ?> />
						<option value="Madagascar" <?php if($user['country'] == "Madagascar") echo "selected"; ?> />
						<option value="Malawi" <?php if($user['country'] == "Malawi") echo "selected"; ?> />
						<option value="Malaysia" <?php if($user['country'] == "Malaysia") echo "selected"; ?> />
						<option value="Maldives" <?php if($user['country'] == "Maldives") echo "selected"; ?> />
						<option value="Mali" <?php if($user['country'] == "Mali") echo "selected"; ?> />
						<option value="Malta" <?php if($user['country'] == "Malta") echo "selected"; ?> />
						<option value="Marshall Islands" <?php if($user['country'] == "Marshall Islands") echo "selected"; ?> />
						<option value="Martinique" <?php if($user['country'] == "Martinique") echo "selected"; ?> />
						<option value="Mauritania" <?php if($user['country'] == "Mauritania") echo "selected"; ?> />
						<option value="Mauritius" <?php if($user['country'] == "Mauritius") echo "selected"; ?> />
						<option value="Mayotte" <?php if($user['country'] == "Mayotte") echo "selected"; ?> />
						<option value="Mexico" <?php if($user['country'] == "Mexico") echo "selected"; ?> />
						<option value="Micronesia, Federated States of" <?php if($user['country'] == "Micronesia, Federated States of") echo "selected"; ?> />
						<option value="Moldova, Republic of" <?php if($user['country'] == "Moldova, Republic of") echo "selected"; ?> />
						<option value="Monaco" <?php if($user['country'] == "Monaco") echo "selected"; ?> />
						<option value="Mongolia" <?php if($user['country'] == "Mongolia") echo "selected"; ?> />
						<option value="Montserrat" <?php if($user['country'] == "Montserrat") echo "selected"; ?> />
						<option value="Morocco" <?php if($user['country'] == "Morocco") echo "selected"; ?> />
						<option value="Mozambique" <?php if($user['country'] == "Mozambique") echo "selected"; ?> />
						<option value="Myanmar" <?php if($user['country'] == "Myanmar") echo "selected"; ?> />
						<option value="Namibia" <?php if($user['country'] == "Namibia") echo "selected"; ?> />
						<option value="Nauru" <?php if($user['country'] == "Nauru") echo "selected"; ?> />
						<option value="Nepal" <?php if($user['country'] == "Nepal") echo "selected"; ?> />
						<option value="Netherlands" <?php if($user['country'] == "Netherlands") echo "selected"; ?> />
						<option value="Netherlands Antilles" <?php if($user['country'] == "Netherlands Antilles") echo "selected"; ?> />
						<option value="New Caledonia" <?php if($user['country'] == "New Caledonia") echo "selected"; ?> />
						<option value="New Zealand" <?php if($user['country'] == "New Zealand") echo "selected"; ?> />
						<option value="Nicaragua" <?php if($user['country'] == "Nicaragua") echo "selected"; ?> />
						<option value="Niger" <?php if($user['country'] == "Niger") echo "selected"; ?> />
						<option value="Nigeria" <?php if($user['country'] == "Nigeria") echo "selected"; ?> />
						<option value="Niue" <?php if($user['country'] == "Niue") echo "selected"; ?> />
						<option value="Norfolk Island" <?php if($user['country'] == "Norfolk Island") echo "selected"; ?> />
						<option value="Northern Mariana Islands" <?php if($user['country'] == "Northern Mariana Islands") echo "selected"; ?> />
						<option value="Norway" <?php if($user['country'] == "Norway") echo "selected"; ?> />
						<option value="Oman" <?php if($user['country'] == "Oman") echo "selected"; ?> />
						<option value="Pakistan" <?php if($user['country'] == "Pakistan") echo "selected"; ?> />
						<option value="Palau" <?php if($user['country'] == "Palau") echo "selected"; ?> />
						<option value="Palestinian Territory, Occupied" <?php if($user['country'] == "Palestinian Territory, Occupied") echo "selected"; ?> />
						<option value="Panama" <?php if($user['country'] == "Panama") echo "selected"; ?> />
						<option value="Papua New Guinea" <?php if($user['country'] == "Papua New Guinea") echo "selected"; ?> />
						<option value="Paraguay" <?php if($user['country'] == "Paraguay") echo "selected"; ?> />
						<option value="Peru" <?php if($user['country'] == "Peru") echo "selected"; ?> />
						<option value="Philippines" <?php if($user['country'] == "Philippines") echo "selected"; ?> />
						<option value="Pitcairn" <?php if($user['country'] == "Pitcairn") echo "selected"; ?> />
						<option value="Poland" <?php if($user['country'] == "Poland") echo "selected"; ?> />
						<option value="Portugal" <?php if($user['country'] == "Portugal") echo "selected"; ?> />
						<option value="Puerto Rico" <?php if($user['country'] == "Puerto Rico") echo "selected"; ?> />
						<option value="Qatar" <?php if($user['country'] == "Qatar") echo "selected"; ?> />
						<option value="Reunion" <?php if($user['country'] == "Reunion") echo "selected"; ?> />
						<option value="Romania" <?php if($user['country'] == "Romania") echo "selected"; ?> />
						<option value="Russian Federation" <?php if($user['country'] == "Russian Federation") echo "selected"; ?> />
						<option value="Rwanda" <?php if($user['country'] == "Rwanda") echo "selected"; ?> />
						<option value="Saint Helena" <?php if($user['country'] == "Saint Helena") echo "selected"; ?> />
						<option value="Saint Kitts and Nevis" <?php if($user['country'] == "Saint Kitts and Nevis") echo "selected"; ?> />
						<option value="Saint Lucia" <?php if($user['country'] == "Saint Lucia") echo "selected"; ?> />
						<option value="Saint Pierre and Miquelon" <?php if($user['country'] == "Saint Pierre and Miquelon") echo "selected"; ?> />
						<option value="Saint Vincent and The Grenadines" <?php if($user['country'] == "Saint Vincent and The Grenadines") echo "selected"; ?> />
						<option value="Samoa" <?php if($user['country'] == "Samoa") echo "selected"; ?> />
						<option value="San Marino" <?php if($user['country'] == "San Marino") echo "selected"; ?> />
						<option value="Sao Tome and Principe" <?php if($user['country'] == "Sao Tome and Principe") echo "selected"; ?> />
						<option value="Saudi Arabia" <?php if($user['country'] == "Saudi Arabia") echo "selected"; ?> />
						<option value="Senegal" <?php if($user['country'] == "Senegal") echo "selected"; ?> />
						<option value="Serbia and Montenegro" <?php if($user['country'] == "Serbia and Montenegro") echo "selected"; ?> />
						<option value="Seychelles" <?php if($user['country'] == "Seychelles") echo "selected"; ?> />
						<option value="Sierra Leone" <?php if($user['country'] == "Sierra Leone") echo "selected"; ?> />
						<option value="Singapore" <?php if($user['country'] == "Singapore") echo "selected"; ?> />
						<option value="Slovakia" <?php if($user['country'] == "Slovakia") echo "selected"; ?> />
						<option value="Slovenia" <?php if($user['country'] == "Slovenia") echo "selected"; ?> />
						<option value="Solomon Islands" <?php if($user['country'] == "Solomon Islands") echo "selected"; ?> />
						<option value="Somalia" <?php if($user['country'] == "Somalia") echo "selected"; ?> />
						<option value="South Africa" <?php if($user['country'] == "South Africa") echo "selected"; ?> />
						<option value="South Georgia and The South Sandwich Islands" <?php if($user['country'] == "South Georgia and The South Sandwich Islands") echo "selected"; ?> />
						<option value="Spain" <?php if($user['country'] == "Spain") echo "selected"; ?> />
						<option value="Sri Lanka" <?php if($user['country'] == "Sri Lanka") echo "selected"; ?> />
						<option value="Sudan" <?php if($user['country'] == "Sudan") echo "selected"; ?> />
						<option value="Suriname" <?php if($user['country'] == "Suriname") echo "selected"; ?> />
						<option value="Svalbard and Jan Mayen" <?php if($user['country'] == "Svalbard and Jan Mayen") echo "selected"; ?> />
						<option value="Swaziland" <?php if($user['country'] == "Swaziland") echo "selected"; ?> />
						<option value="Sweden" <?php if($user['country'] == "Sweden") echo "selected"; ?> />
						<option value="Switzerland" <?php if($user['country'] == "Switzerland") echo "selected"; ?> />
						<option value="Syrian Arab Republic" <?php if($user['country'] == "Syrian Arab Republic") echo "selected"; ?> />
						<option value="Taiwan, Province of China" <?php if($user['country'] == "Taiwan, Province of China") echo "selected"; ?> />
						<option value="Tajikistan" <?php if($user['country'] == "Tajikistan") echo "selected"; ?> />
						<option value="Tanzania, United Republic of" <?php if($user['country'] == "Tanzania, United Republic of") echo "selected"; ?> />
						<option value="Thailand" <?php if($user['country'] == "Thailand") echo "selected"; ?> />
						<option value="Timor-leste" <?php if($user['country'] == "Timor-leste") echo "selected"; ?> />
						<option value="Togo" <?php if($user['country'] == "Togo") echo "selected"; ?> />
						<option value="Tokelau" <?php if($user['country'] == "Tokelau") echo "selected"; ?> />
						<option value="Tonga" <?php if($user['country'] == "Tonga") echo "selected"; ?> />
						<option value="Trinidad and Tobago" <?php if($user['country'] == "Trinidad and Tobago") echo "selected"; ?> />
						<option value="Tunisia" <?php if($user['country'] == "Tunisia") echo "selected"; ?> />
						<option value="Turkey" <?php if($user['country'] == "Turkey") echo "selected"; ?> />
						<option value="Turkmenistan" <?php if($user['country'] == "Turkmenistan") echo "selected"; ?> />
						<option value="Turks and Caicos Islands" <?php if($user['country'] == "Turks and Caicos Islands") echo "selected"; ?> />
						<option value="Tuvalu" <?php if($user['country'] == "Tuvalu") echo "selected"; ?> />
						<option value="Uganda" <?php if($user['country'] == "Uganda") echo "selected"; ?> />
						<option value="Ukraine" <?php if($user['country'] == "Ukraine") echo "selected"; ?> />
						<option value="United Arab Emirates" <?php if($user['country'] == "United Arab Emirates") echo "selected"; ?> />
						<option value="United Kingdom" <?php if($user['country'] == "United Kingdom") echo "selected"; ?> />
						<option value="United States" <?php if($user['country'] == "United States") echo "selected"; ?> />
						<option value="United States Minor Outlying Islands" <?php if($user['country'] == "United States Minor Outlying Islands") echo "selected"; ?> />
						<option value="Uruguay" <?php if($user['country'] == "Uruguay") echo "selected"; ?> />
						<option value="Uzbekistan" <?php if($user['country'] == "Uzbekistan") echo "selected"; ?> />
						<option value="Vanuatu" <?php if($user['country'] == "Vanuatu") echo "selected"; ?> />
						<option value="Venezuela" <?php if($user['country'] == "Venezuela") echo "selected"; ?> />
						<option value="Viet Nam" <?php if($user['country'] == "Viet Nam") echo "selected"; ?> />
						<option value="Virgin Islands, British" <?php if($user['country'] == "Virgin Islands, British") echo "selected"; ?> />
						<option value="Virgin Islands, U.S" <?php if($user['country'] == "Virgin Islands, U.S") echo "selected"; ?> />
						<option value="Wallis and Futuna" <?php if($user['country'] == "Wallis and Futuna") echo "selected"; ?> />
						<option value="Western Sahara" <?php if($user['country'] == "Western Sahara") echo "selected"; ?> />
						<option value="Yemen" <?php if($user['country'] == "Yemen") echo "selected"; ?> />
						<option value="Zambia" <?php if($user['country'] == "Zambia") echo "selected"; ?> />
						<option value="Zimbabwe" <?php if($user['country'] == "Zimbabwe") echo "selected"; ?> />
					</datalist>
                </div>
              </div>






              <div class="form-group row">
                <label for="PLZ" class="col-sm-2 form-control-label">PLZ</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control form-control-sm"
                  id="PLZ" placeholder="1234" required
                  name="plz" value="<?php echo $user['plz']; ?>">
                </div>
              </div>

              <div class="form-group row">
                <label for="Ort" class="col-sm-2 form-control-label">Ort</label>
                <div class="col-sm-10">
                  <input  type="text" class="form-control form-control-sm"
                  id="ort" placeholder="Muster" required
                  name="ort" value="<?php echo $user['address']; ?>">
                </div>
              </div>


              <div class="form-group row">
                <label for="Kanton" class="col-sm-2 form-control-label">Kanton</label>
                <div class="col-sm-10">
                  <p><?php echo $user['address']; ?></p>
                </div>
              </div>


            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Abbrechen</button>
              <button type="submit" class="btn btn-success btn-sm" name="update-submit">Änderungen speichern</button>
            </div>
          </form>

        </div>
      </div>
    </div>
    <!-- /Modales Fenster zur Userdatenaktualisierung-->

  </div>

  <!-- jQuery (nötig für alle JavaScript-basierten Plugins von BS) -->
  <script src="js/jquery-3.1.1.min.js"></script>
  <!-- Beinhaltet alle JavaScript-basierten Plugins von BS -->
  <script src="js/bootstrap.min.js"></script>

  <script type="text/javascript">


// ************************************************************************ //
// PLZ INPUT LENGTH LISTENER                                                //
// ************************************************************************ //
  document.getElementById('PLZ').onkeyup = function(){
       if(this.value.length == 4){
                  // genau 4
                    var str = this.value;

                    if (window.XMLHttpRequest) {
                      // code for IE7+, Firefox, Chrome, Opera, Safari
                      xmlhttp=new XMLHttpRequest();
                    } else { // code for IE6, IE5
                      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                    }

                    xmlhttp.onreadystatechange = function() {
                      if (this.readyState==4 && this.status==200) {
                        document.getElementById("ort").value=this.responseText;
                      }
                    }
                    xmlhttp.open("GET","get.php?q="+str,true);
                    xmlhttp.send();


              }
      }



  </script>

</body>
</html>
