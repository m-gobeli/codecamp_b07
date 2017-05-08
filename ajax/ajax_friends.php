<?php

require_once('../system/data.php');
require_once('../system/security.php');

// Neuen Freund in die DB einfügen.
if(isset($_GET['new_friend'])){
  add_friend($_GET['user_id'], $_GET['new_friend']);

  // Daten des neuen Freundes aus der DB laden.
  $user = mysqli_fetch_assoc(get_user($_GET['new_friend']));
  $user_id = $user['user_id'];
  $user_name = $user['firstname'] . " " . $user['lastname'];

// Daten in den zu übertragenden HTML-Code einfügen
// Unterbruch des PHP-Blocks zwecks Syntaxhighlightning für HTML
?>
  <div class="row my_friend">
    <div class="btn-group col-xs-12" data-toggle="buttons">
      <label class="btn btn-default btn-block p42-friend-btn">
        <input type="checkbox" name="del_friends[]" autocomplete="off" value="<?php echo $user_id ; ?>" />
        <span class="glyphicon glyphicon-minus"></span> <?php echo $user_name ; ?>
      </label>
    </div>
  </div>

<?php
}
?>


<?php
// Freundschaft beenden.
if(isset($_GET['del_friend'])){
  remove_friend($_GET['user_id'], $_GET['del_friend']);

  $user = mysqli_fetch_assoc(get_user($_GET['del_friend']));
  $user_id = $user['user_id'];
  $user_name = $user['firstname'] . " " . $user['lastname'];

// Unterbruch des PHP-Blocks zwecks Syntaxhilightning für HTML
 ?>
  <div class="btn-group col-xs-12 not_my_friend" data-toggle="buttons" >
    <label class="btn btn-default btn-block p42-friend-btn">
      <input type="checkbox" name="new_friends[]" autocomplete="off" value="<?php echo $user_id ; ?>" >
      <span class="glyphicon glyphicon-plus"></span> <?php echo $user_name ; ?>
    </label>
  </div>
<?php
} ?>
