<?php
require_once('../system/data.php');
require_once('../system/security.php');

if(isset($_POST['posttext'])){
  $posttext = filter_data($_POST['posttext']);
  $user_id = $_POST['user_id'];

  $id = write_ajax_post($posttext, $user_id);
  $post = mysqli_fetch_assoc(get_post($id));
  $user = mysqli_fetch_assoc(get_user($user_id));
?>

  <article class="row">
    <div class="col-xs-2">
      <div class="thumbnail p42thumbnail">
        <!-- http://getbootstrap.com/css/#images -->
        <img src="user_img/<?php echo $user['img_src']; ?>" alt="Profilbild" class="img-responsive">
      </div><!-- /thumbnail p42thumbnail -->
    </div><!-- /col-sm-2 -->

    <form method="post" action="home.php" class="form-inline">
      <input type="hidden" name="post_id" value="<?php echo $user_id; ?>">
      <div class="col-xs-10">
        <div class="panel panel-info p42panel">
          <div class="panel-heading">
            <?php
            echo $user['firstname'] . " " . $user['lastname']; ?>
            <?php
            if($post['owner'] == $user_id) {  ?>
              <!-- Button zum löschen des Posts.
              Darf nur bei Posts des aktiven Nutzers angezeigt werden. -->
              <button type="submit" class="close" name="post_delete" value="<?php echo $post['post_id']; ?>">
                <span aria-hidden="true">&times;</span>
              </button>
              <?php
            } ?>
          </div>
          <div class="panel-body">
            <p><?php echo $post['text'] ?></p>
            <?php if($post['post_img'] != NULL){  ?>
              <img src="post_img/<?php echo $post['post_img']; ?>" alt="postimage" class="img-responsive img-thumbnail">
              <?php
            } ?>
          </div>
          <div class="panel-footer text-right">
            <button type="submit" name="like_submit" class="btn btn-default btn-xs">
              <span class="glyphicon glyphicon-thumbs-up text-success" aria-hidden="true"></span>
              <span class="badge badge-success">2</span>
            </button>
            <button type="submit" name="hate_submit" class="btn btn-default btn-xs">
              <span class="glyphicon glyphicon-thumbs-down text-danger" aria-hidden="true"></span>
              <span class="badge badge-danger">0</span>
            </button>
          </div>
        </div>
      </div><!-- /col-xs-10 -->
    </form>
  </article> <!-- /Beitrag -->
  <?php
}
?>
