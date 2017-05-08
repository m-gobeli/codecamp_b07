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
						<option <?php if($user['address'] == "") echo "selected"; ?> value="Bitte wählen" />
						<option value="Afghanistan" <?php if($user['address'] == "Afghanistan") echo "selected"; ?> />
						<option value="Albania" <?php if($user['address'] == "Albania") echo "selected"; ?> />
						<option value="Algeria" <?php if($user['address'] == "Algeria") echo "selected"; ?> />
						<option value="American Samoa" <?php if($user['address'] == "American Samoa") echo "selected"; ?> />
						<option value="Andorra" <?php if($user['address'] == "Andorra") echo "selected"; ?> />
						<option value="Angola" <?php if($user['address'] == "Angola") echo "selected"; ?> />
						<option value="Anguilla" <?php if($user['address'] == "Anguilla") echo "selected"; ?> />
						<option value="Antarctica" <?php if($user['address'] == "Antarctica") echo "selected"; ?> />
						<option value="Antigua and Barbuda" <?php if($user['address'] == "Antigua and Barbuda") echo "selected"; ?> />
						<option value="Argentina" <?php if($user['address'] == "Argentina") echo "selected"; ?> />
						<option value="Armenia" <?php if($user['address'] == "Armenia") echo "selected"; ?> />
						<option value="Aruba" <?php if($user['address'] == "Aruba") echo "selected"; ?> />
						<option value="Australia" <?php if($user['address'] == "Australia") echo "selected"; ?> />
						<option value="Austria" <?php if($user['address'] == "Austria") echo "selected"; ?> />
						<option value="Azerbaijan" <?php if($user['address'] == "Azerbaijan") echo "selected"; ?> />
						<option value="Bahamas" <?php if($user['address'] == "Bahamas") echo "selected"; ?> />
						<option value="Bahrain" <?php if($user['address'] == "Bahrain") echo "selected"; ?> />
						<option value="Bangladesh" <?php if($user['address'] == "Bangladesh") echo "selected"; ?> />
						<option value="Barbados" <?php if($user['address'] == "Barbados") echo "selected"; ?> />
						<option value="Belarus" <?php if($user['address'] == "Belarus") echo "selected"; ?> />
						<option value="Belgium" <?php if($user['address'] == "Belgium") echo "selected"; ?> />
						<option value="Belize" <?php if($user['address'] == "Belize") echo "selected"; ?> />
						<option value="Benin" <?php if($user['address'] == "Benin") echo "selected"; ?> />
						<option value="Bermuda" <?php if($user['address'] == "Bermuda") echo "selected"; ?> />
						<option value="Bhutan" <?php if($user['address'] == "Bhutan") echo "selected"; ?> />
						<option value="Bolivia" <?php if($user['address'] == "Bolivia") echo "selected"; ?> />
						<option value="Bosnia and Herzegovina" <?php if($user['address'] == "Bosnia and Herzegovina") echo "selected"; ?> />
						<option value="Botswana" <?php if($user['address'] == "Botswana") echo "selected"; ?> />
						<option value="Bouvet Island" <?php if($user['address'] == "Bouvet Island") echo "selected"; ?> />
						<option value="Brazil" <?php if($user['address'] == "Brazil") echo "selected"; ?> />
						<option value="British Indian Ocean Territory" <?php if($user['address'] == "British Indian Ocean Territory") echo "selected"; ?> />
						<option value="Brunei Darussalam" <?php if($user['address'] == "Brunei Darussalam") echo "selected"; ?> />
						<option value="Bulgaria" <?php if($user['address'] == "Bulgaria") echo "selected"; ?> />
						<option value="Burkina Faso" <?php if($user['address'] == "Burkina Faso") echo "selected"; ?> />
						<option value="Burundi" <?php if($user['address'] == "Burundi") echo "selected"; ?> />
						<option value="Cambodia" <?php if($user['address'] == "Cambodia") echo "selected"; ?> />
						<option value="Cameroon" <?php if($user['address'] == "Cameroon") echo "selected"; ?> />
						<option value="Canada" <?php if($user['address'] == "Canada") echo "selected"; ?> />
						<option value="Cape Verde" <?php if($user['address'] == "Cape Verde") echo "selected"; ?> />
						<option value="Cayman Islands" <?php if($user['address'] == "Cayman Islands") echo "selected"; ?> />
						<option value="Central African Republic" <?php if($user['address'] == "Central African Republic") echo "selected"; ?> />
						<option value="Chad" <?php if($user['address'] == "Chad") echo "selected"; ?> />
						<option value="Chile" <?php if($user['address'] == "Chile") echo "selected"; ?> />
						<option value="China" <?php if($user['address'] == "China") echo "selected"; ?> />
						<option value="Christmas Island" <?php if($user['address'] == "Christmas Island") echo "selected"; ?> />
						<option value="Cocos (Keeling) Islands" <?php if($user['address'] == "Cocos (Keeling) Islands") echo "selected"; ?> />
						<option value="Colombia" <?php if($user['address'] == "Colombia") echo "selected"; ?> />
						<option value="Comoros" <?php if($user['address'] == "Comoros") echo "selected"; ?> />
						<option value="Congo" <?php if($user['address'] == "Congo") echo "selected"; ?> />
						<option value="Congo, The Democratic Republic of The" <?php if($user['address'] == "Congo, The Democratic Republic of The") echo "selected"; ?> />
						<option value="Cook Islands" <?php if($user['address'] == "Cook Islands") echo "selected"; ?> />
						<option value="Costa Rica" <?php if($user['address'] == "Costa Rica") echo "selected"; ?> />
						<option value="Cote D'ivoire" <?php if($user['address'] == "Cote D'ivoire") echo "selected"; ?> />
						<option value="Croatia" <?php if($user['address'] == "Croatia") echo "selected"; ?> />
						<option value="Cuba" <?php if($user['address'] == "Cuba") echo "selected"; ?> />
						<option value="Cyprus" <?php if($user['address'] == "Cyprus") echo "selected"; ?> />
						<option value="Czech Republic" <?php if($user['address'] == "Czech Republic") echo "selected"; ?> />
						<option value="Denmark" <?php if($user['address'] == "Denmark") echo "selected"; ?> />
						<option value="Djibouti" <?php if($user['address'] == "Djibouti") echo "selected"; ?> />
						<option value="Dominica" <?php if($user['address'] == "Dominica") echo "selected"; ?> />
						<option value="Dominican Republic" <?php if($user['address'] == "Dominican Republic") echo "selected"; ?> />
						<option value="Ecuador" <?php if($user['address'] == "Ecuador") echo "selected"; ?> />
						<option value="Egypt" <?php if($user['address'] == "Egypt") echo "selected"; ?> />
						<option value="El Salvador" <?php if($user['address'] == "El Salvador") echo "selected"; ?> />
						<option value="Equatorial Guinea" <?php if($user['address'] == "Equatorial Guinea") echo "selected"; ?> />
						<option value="Eritrea" <?php if($user['address'] == "Eritrea") echo "selected"; ?> />
						<option value="Estonia" <?php if($user['address'] == "Estonia") echo "selected"; ?> />
						<option value="Ethiopia" <?php if($user['address'] == "Ethiopia") echo "selected"; ?> />
						<option value="Falkland Islands (Malvinas)" <?php if($user['address'] == "Falkland Islands (Malvinas)") echo "selected"; ?> />
						<option value="Faroe Islands" <?php if($user['address'] == "Faroe Islands") echo "selected"; ?> />
						<option value="Fiji" <?php if($user['address'] == "Fiji") echo "selected"; ?> />
						<option value="Finland" <?php if($user['address'] == "Finland") echo "selected"; ?> />
						<option value="France" <?php if($user['address'] == "France") echo "selected"; ?> />
						<option value="French Guiana" <?php if($user['address'] == "French Guiana") echo "selected"; ?> />
						<option value="French Polynesia" <?php if($user['address'] == "French Polynesia") echo "selected"; ?> />
						<option value="French Southern Territories" <?php if($user['address'] == "French Southern Territories") echo "selected"; ?> />
						<option value="Gabon" <?php if($user['address'] == "Gabon") echo "selected"; ?> />
						<option value="Gambia" <?php if($user['address'] == "Gambia") echo "selected"; ?> />
						<option value="Georgia" <?php if($user['address'] == "Georgia") echo "selected"; ?> />
						<option value="Germany" <?php if($user['address'] == "Germany") echo "selected"; ?> />
						<option value="Ghana" <?php if($user['address'] == "Ghana") echo "selected"; ?> />
						<option value="Gibraltar" <?php if($user['address'] == "Gibraltar") echo "selected"; ?> />
						<option value="Greece" <?php if($user['address'] == "Greece") echo "selected"; ?> />
						<option value="Greenland" <?php if($user['address'] == "Greenland") echo "selected"; ?> />
						<option value="Grenada" <?php if($user['address'] == "Grenada") echo "selected"; ?> />
						<option value="Guadeloupe" <?php if($user['address'] == "Guadeloupe") echo "selected"; ?> />
						<option value="Guam" <?php if($user['address'] == "Guam") echo "selected"; ?> />
						<option value="Guatemala" <?php if($user['address'] == "Guatemala") echo "selected"; ?> />
						<option value="Guinea" <?php if($user['address'] == "Guinea") echo "selected"; ?> />
						<option value="Guinea-bissau" <?php if($user['address'] == "Guinea-bissau") echo "selected"; ?> />
						<option value="Guyana" <?php if($user['address'] == "Guyana") echo "selected"; ?> />
						<option value="Haiti" <?php if($user['address'] == "Haiti") echo "selected"; ?> />
						<option value="Heard Island and Mcdonald Islands" <?php if($user['address'] == "Heard Island and Mcdonald Islands") echo "selected"; ?> />
						<option value="Holy See (Vatican City State)" <?php if($user['address'] == "Holy See (Vatican City State)") echo "selected"; ?> />
						<option value="Honduras" <?php if($user['address'] == "Honduras") echo "selected"; ?> />
						<option value="Hong Kong" <?php if($user['address'] == "Hong Kong") echo "selected"; ?> />
						<option value="Hungary" <?php if($user['address'] == "Hungary") echo "selected"; ?> />
						<option value="Iceland" <?php if($user['address'] == "Iceland") echo "selected"; ?> />
						<option value="India" <?php if($user['address'] == "India") echo "selected"; ?> />
						<option value="Indonesia" <?php if($user['address'] == "Indonesia") echo "selected"; ?> />
						<option value="Iran, Islamic Republic of" <?php if($user['address'] == "Iran, Islamic Republic of") echo "selected"; ?> />
						<option value="Iraq" <?php if($user['address'] == "Iraq") echo "selected"; ?> />
						<option value="Ireland" <?php if($user['address'] == "Ireland") echo "selected"; ?> />
						<option value="Israel" <?php if($user['address'] == "Israel") echo "selected"; ?> />
						<option value="Italy" <?php if($user['address'] == "Italy") echo "selected"; ?> />
						<option value="Jamaica" <?php if($user['address'] == "Jamaica") echo "selected"; ?> />
						<option value="Japan" <?php if($user['address'] == "Japan") echo "selected"; ?> />
						<option value="Jordan" <?php if($user['address'] == "Jordan") echo "selected"; ?> />
						<option value="Kazakhstan" <?php if($user['address'] == "Kazakhstan") echo "selected"; ?> />
						<option value="Kenya" <?php if($user['address'] == "Kenya") echo "selected"; ?> />
						<option value="Kiribati" <?php if($user['address'] == "Kiribati") echo "selected"; ?> />
						<option value="Korea, Democratic People's Republic of" <?php if($user['address'] == "Korea, Democratic People's Republic of") echo "selected"; ?> />
						<option value="Korea, Republic of" <?php if($user['address'] == "Korea, Republic of") echo "selected"; ?> />
						<option value="Kuwait" <?php if($user['address'] == "Kuwait") echo "selected"; ?> />
						<option value="Kyrgyzstan" <?php if($user['address'] == "Kyrgyzstan") echo "selected"; ?> />
						<option value="Lao People's Democratic Republic" <?php if($user['address'] == "Lao People's Democratic Republic") echo "selected"; ?> />
						<option value="Latvia" <?php if($user['address'] == "Latvia") echo "selected"; ?> />
						<option value="Lebanon" <?php if($user['address'] == "Lebanon") echo "selected"; ?> />
						<option value="Lesotho" <?php if($user['address'] == "Lesotho") echo "selected"; ?> />
						<option value="Liberia" <?php if($user['address'] == "Liberia") echo "selected"; ?> />
						<option value="Libyan Arab Jamahiriya" <?php if($user['address'] == "Libyan Arab Jamahiriya") echo "selected"; ?> />
						<option value="Liechtenstein" <?php if($user['address'] == "Liechtenstein") echo "selected"; ?> />
						<option value="Lithuania" <?php if($user['address'] == "Lithuania") echo "selected"; ?> />
						<option value="Luxembourg" <?php if($user['address'] == "Luxembourg") echo "selected"; ?> />
						<option value="Macao" <?php if($user['address'] == "Macao") echo "selected"; ?> />
						<option value="Macedonia, The Former Yugoslav Republic of" <?php if($user['address'] == "Macedonia, The Former Yugoslav Republic of") echo "selected"; ?> />
						<option value="Madagascar" <?php if($user['address'] == "Madagascar") echo "selected"; ?> />
						<option value="Malawi" <?php if($user['address'] == "Malawi") echo "selected"; ?> />
						<option value="Malaysia" <?php if($user['address'] == "Malaysia") echo "selected"; ?> />
						<option value="Maldives" <?php if($user['address'] == "Maldives") echo "selected"; ?> />
						<option value="Mali" <?php if($user['address'] == "Mali") echo "selected"; ?> />
						<option value="Malta" <?php if($user['address'] == "Malta") echo "selected"; ?> />
						<option value="Marshall Islands" <?php if($user['address'] == "Marshall Islands") echo "selected"; ?> />
						<option value="Martinique" <?php if($user['address'] == "Martinique") echo "selected"; ?> />
						<option value="Mauritania" <?php if($user['address'] == "Mauritania") echo "selected"; ?> />
						<option value="Mauritius" <?php if($user['address'] == "Mauritius") echo "selected"; ?> />
						<option value="Mayotte" <?php if($user['address'] == "Mayotte") echo "selected"; ?> />
						<option value="Mexico" <?php if($user['address'] == "Mexico") echo "selected"; ?> />
						<option value="Micronesia, Federated States of" <?php if($user['address'] == "Micronesia, Federated States of") echo "selected"; ?> />
						<option value="Moldova, Republic of" <?php if($user['address'] == "Moldova, Republic of") echo "selected"; ?> />
						<option value="Monaco" <?php if($user['address'] == "Monaco") echo "selected"; ?> />
						<option value="Mongolia" <?php if($user['address'] == "Mongolia") echo "selected"; ?> />
						<option value="Montserrat" <?php if($user['address'] == "Montserrat") echo "selected"; ?> />
						<option value="Morocco" <?php if($user['address'] == "Morocco") echo "selected"; ?> />
						<option value="Mozambique" <?php if($user['address'] == "Mozambique") echo "selected"; ?> />
						<option value="Myanmar" <?php if($user['address'] == "Myanmar") echo "selected"; ?> />
						<option value="Namibia" <?php if($user['address'] == "Namibia") echo "selected"; ?> />
						<option value="Nauru" <?php if($user['address'] == "Nauru") echo "selected"; ?> />
						<option value="Nepal" <?php if($user['address'] == "Nepal") echo "selected"; ?> />
						<option value="Netherlands" <?php if($user['address'] == "Netherlands") echo "selected"; ?> />
						<option value="Netherlands Antilles" <?php if($user['address'] == "Netherlands Antilles") echo "selected"; ?> />
						<option value="New Caledonia" <?php if($user['address'] == "New Caledonia") echo "selected"; ?> />
						<option value="New Zealand" <?php if($user['address'] == "New Zealand") echo "selected"; ?> />
						<option value="Nicaragua" <?php if($user['address'] == "Nicaragua") echo "selected"; ?> />
						<option value="Niger" <?php if($user['address'] == "Niger") echo "selected"; ?> />
						<option value="Nigeria" <?php if($user['address'] == "Nigeria") echo "selected"; ?> />
						<option value="Niue" <?php if($user['address'] == "Niue") echo "selected"; ?> />
						<option value="Norfolk Island" <?php if($user['address'] == "Norfolk Island") echo "selected"; ?> />
						<option value="Northern Mariana Islands" <?php if($user['address'] == "Northern Mariana Islands") echo "selected"; ?> />
						<option value="Norway" <?php if($user['address'] == "Norway") echo "selected"; ?> />
						<option value="Oman" <?php if($user['address'] == "Oman") echo "selected"; ?> />
						<option value="Pakistan" <?php if($user['address'] == "Pakistan") echo "selected"; ?> />
						<option value="Palau" <?php if($user['address'] == "Palau") echo "selected"; ?> />
						<option value="Palestinian Territory, Occupied" <?php if($user['address'] == "Palestinian Territory, Occupied") echo "selected"; ?> />
						<option value="Panama" <?php if($user['address'] == "Panama") echo "selected"; ?> />
						<option value="Papua New Guinea" <?php if($user['address'] == "Papua New Guinea") echo "selected"; ?> />
						<option value="Paraguay" <?php if($user['address'] == "Paraguay") echo "selected"; ?> />
						<option value="Peru" <?php if($user['address'] == "Peru") echo "selected"; ?> />
						<option value="Philippines" <?php if($user['address'] == "Philippines") echo "selected"; ?> />
						<option value="Pitcairn" <?php if($user['address'] == "Pitcairn") echo "selected"; ?> />
						<option value="Poland" <?php if($user['address'] == "Poland") echo "selected"; ?> />
						<option value="Portugal" <?php if($user['address'] == "Portugal") echo "selected"; ?> />
						<option value="Puerto Rico" <?php if($user['address'] == "Puerto Rico") echo "selected"; ?> />
						<option value="Qatar" <?php if($user['address'] == "Qatar") echo "selected"; ?> />
						<option value="Reunion" <?php if($user['address'] == "Reunion") echo "selected"; ?> />
						<option value="Romania" <?php if($user['address'] == "Romania") echo "selected"; ?> />
						<option value="Russian Federation" <?php if($user['address'] == "Russian Federation") echo "selected"; ?> />
						<option value="Rwanda" <?php if($user['address'] == "Rwanda") echo "selected"; ?> />
						<option value="Saint Helena" <?php if($user['address'] == "Saint Helena") echo "selected"; ?> />
						<option value="Saint Kitts and Nevis" <?php if($user['address'] == "Saint Kitts and Nevis") echo "selected"; ?> />
						<option value="Saint Lucia" <?php if($user['address'] == "Saint Lucia") echo "selected"; ?> />
						<option value="Saint Pierre and Miquelon" <?php if($user['address'] == "Saint Pierre and Miquelon") echo "selected"; ?> />
						<option value="Saint Vincent and The Grenadines" <?php if($user['address'] == "Saint Vincent and The Grenadines") echo "selected"; ?> />
						<option value="Samoa" <?php if($user['address'] == "Samoa") echo "selected"; ?> />
						<option value="San Marino" <?php if($user['address'] == "San Marino") echo "selected"; ?> />
						<option value="Sao Tome and Principe" <?php if($user['address'] == "Sao Tome and Principe") echo "selected"; ?> />
						<option value="Saudi Arabia" <?php if($user['address'] == "Saudi Arabia") echo "selected"; ?> />
						<option value="Senegal" <?php if($user['address'] == "Senegal") echo "selected"; ?> />
						<option value="Serbia and Montenegro" <?php if($user['address'] == "Serbia and Montenegro") echo "selected"; ?> />
						<option value="Seychelles" <?php if($user['address'] == "Seychelles") echo "selected"; ?> />
						<option value="Sierra Leone" <?php if($user['address'] == "Sierra Leone") echo "selected"; ?> />
						<option value="Singapore" <?php if($user['address'] == "Singapore") echo "selected"; ?> />
						<option value="Slovakia" <?php if($user['address'] == "Slovakia") echo "selected"; ?> />
						<option value="Slovenia" <?php if($user['address'] == "Slovenia") echo "selected"; ?> />
						<option value="Solomon Islands" <?php if($user['address'] == "Solomon Islands") echo "selected"; ?> />
						<option value="Somalia" <?php if($user['address'] == "Somalia") echo "selected"; ?> />
						<option value="South Africa" <?php if($user['address'] == "South Africa") echo "selected"; ?> />
						<option value="South Georgia and The South Sandwich Islands" <?php if($user['address'] == "South Georgia and The South Sandwich Islands") echo "selected"; ?> />
						<option value="Spain" <?php if($user['address'] == "Spain") echo "selected"; ?> />
						<option value="Sri Lanka" <?php if($user['address'] == "Sri Lanka") echo "selected"; ?> />
						<option value="Sudan" <?php if($user['address'] == "Sudan") echo "selected"; ?> />
						<option value="Suriname" <?php if($user['address'] == "Suriname") echo "selected"; ?> />
						<option value="Svalbard and Jan Mayen" <?php if($user['address'] == "Svalbard and Jan Mayen") echo "selected"; ?> />
						<option value="Swaziland" <?php if($user['address'] == "Swaziland") echo "selected"; ?> />
						<option value="Sweden" <?php if($user['address'] == "Sweden") echo "selected"; ?> />
						<option value="Switzerland" <?php if($user['address'] == "Switzerland") echo "selected"; ?> />
						<option value="Syrian Arab Republic" <?php if($user['address'] == "Syrian Arab Republic") echo "selected"; ?> />
						<option value="Taiwan, Province of China" <?php if($user['address'] == "Taiwan, Province of China") echo "selected"; ?> />
						<option value="Tajikistan" <?php if($user['address'] == "Tajikistan") echo "selected"; ?> />
						<option value="Tanzania, United Republic of" <?php if($user['address'] == "Tanzania, United Republic of") echo "selected"; ?> />
						<option value="Thailand" <?php if($user['address'] == "Thailand") echo "selected"; ?> />
						<option value="Timor-leste" <?php if($user['address'] == "Timor-leste") echo "selected"; ?> />
						<option value="Togo" <?php if($user['address'] == "Togo") echo "selected"; ?> />
						<option value="Tokelau" <?php if($user['address'] == "Tokelau") echo "selected"; ?> />
						<option value="Tonga" <?php if($user['address'] == "Tonga") echo "selected"; ?> />
						<option value="Trinidad and Tobago" <?php if($user['address'] == "Trinidad and Tobago") echo "selected"; ?> />
						<option value="Tunisia" <?php if($user['address'] == "Tunisia") echo "selected"; ?> />
						<option value="Turkey" <?php if($user['address'] == "Turkey") echo "selected"; ?> />
						<option value="Turkmenistan" <?php if($user['address'] == "Turkmenistan") echo "selected"; ?> />
						<option value="Turks and Caicos Islands" <?php if($user['address'] == "Turks and Caicos Islands") echo "selected"; ?> />
						<option value="Tuvalu" <?php if($user['address'] == "Tuvalu") echo "selected"; ?> />
						<option value="Uganda" <?php if($user['address'] == "Uganda") echo "selected"; ?> />
						<option value="Ukraine" <?php if($user['address'] == "Ukraine") echo "selected"; ?> />
						<option value="United Arab Emirates" <?php if($user['address'] == "United Arab Emirates") echo "selected"; ?> />
						<option value="United Kingdom" <?php if($user['address'] == "United Kingdom") echo "selected"; ?> />
						<option value="United States" <?php if($user['address'] == "United States") echo "selected"; ?> />
						<option value="United States Minor Outlying Islands" <?php if($user['address'] == "United States Minor Outlying Islands") echo "selected"; ?> />
						<option value="Uruguay" <?php if($user['address'] == "Uruguay") echo "selected"; ?> />
						<option value="Uzbekistan" <?php if($user['address'] == "Uzbekistan") echo "selected"; ?> />
						<option value="Vanuatu" <?php if($user['address'] == "Vanuatu") echo "selected"; ?> />
						<option value="Venezuela" <?php if($user['address'] == "Venezuela") echo "selected"; ?> />
						<option value="Viet Nam" <?php if($user['address'] == "Viet Nam") echo "selected"; ?> />
						<option value="Virgin Islands, British" <?php if($user['address'] == "Virgin Islands, British") echo "selected"; ?> />
						<option value="Virgin Islands, U.S" <?php if($user['address'] == "Virgin Islands, U.S") echo "selected"; ?> />
						<option value="Wallis and Futuna" <?php if($user['address'] == "Wallis and Futuna") echo "selected"; ?> />
						<option value="Western Sahara" <?php if($user['address'] == "Western Sahara") echo "selected"; ?> />
						<option value="Yemen" <?php if($user['address'] == "Yemen") echo "selected"; ?> />
						<option value="Zambia" <?php if($user['address'] == "Zambia") echo "selected"; ?> />
						<option value="Zimbabwe" <?php if($user['address'] == "Zimbabwe") echo "selected"; ?> />
					</datalist>	
                </div>
              </div>
			  
		  
			  
			  
			  
			  
              <div class="form-group row">
                <label for="PLZ" class="col-sm-2 form-control-label">PLZ</label>
                <div class="col-sm-10">
                  <input  type="text" class="form-control form-control-sm"
                  id="PLZ" placeholder="1234" required
                  name="plz" value="<?php echo $user['plz']; ?>">
                </div>
              </div>

              <div class="form-group row">
                <label for="Ort" class="col-sm-2 form-control-label">Ort</label>
                <div class="col-sm-10">
                  <input  type="text" class="form-control form-control-sm"
                  id="Text" placeholder="Muster" required
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

</body>
</html>
