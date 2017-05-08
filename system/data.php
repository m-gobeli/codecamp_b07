<?php

  function get_db_connection()
  {
    $db = mysqli_connect('localhost', '134721_7_1', 'SLiQlm7@A=ZE', '134721_7_1')
      or die('Fehler beim Verbinden mit dem Datenbank-Server.');
    mysqli_set_charset($db, "utf8");
    return $db;
  }

  function get_result($sql)
  {
    $db = get_db_connection();
    // echo $sql;
    $result = mysqli_query($db, $sql);
    mysqli_close($db);
    return $result;
  }

  function get_id_result($sql)
  {
    $db = get_db_connection();
    // echo $sql;
    mysqli_query($db, $sql);
    $result = mysqli_insert_id($db);
    mysqli_close($db);
    return $result;
  }


	/* *********************************************************
	/* Login index.php
	/* ****************************************************** */

  function login($email, $password)
  {
    $sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password';";
    return get_result($sql);
  }

  function register($email , $password){
    $sql = "INSERT INTO user (email, password) VALUES ('$email', '$password');";
		return get_result($sql);
	}


	/* *********************************************************
	/* home.php
	/* ****************************************************** */

  function write_post($posttext, $owner)
  {
    $sql = "INSERT INTO posts (text, owner) VALUES ('$posttext', '$owner');";
    return get_result($sql);
  }

  function write_ajax_post($posttext, $owner)
  {
    $id = "INSERT INTO posts (text, owner) VALUES ('$posttext', '$owner');";
    return get_id_result($id);
  }

  function get_post($post_id)
  {
    $sql = "SELECT * FROM posts p, user u WHERE p.post_id = $post_id AND u.user_id = p.owner";
    return get_result($sql);
  }

  function get_posts($user_id)
  {
    $sql = "SELECT * FROM posts p, user u WHERE p.owner = $user_id AND u.user_id = $user_id;";
    return get_result($sql);
  }

  function get_friends_and_my_posts($user_id){
    $sql = "SELECT * FROM posts p, user u WHERE p.owner IN
              (SELECT friend FROM userrelations WHERE user = $user_id)
              AND u.`user_id` = p.owner
              OR p.owner = $user_id AND u.`user_id` = $user_id
              ORDER BY p.posttime DESC;";
		return get_result($sql);
	}

	function delete_post($post_id){
    $sql = "DELETE FROM posts WHERE post_id = $post_id ;";
		return get_result($sql);
	}


	/* *********************************************************
	/* profil.php
	/* ****************************************************** */

  function get_user($user_id)
  {
    $sql = "SELECT * FROM user WHERE user_id = $user_id;";
    return get_result($sql);
  }

  function update_user($user_id, $email, $password, $confirm_password, $gender, $firstname, $lastname, $image_name, $country, $plz, $ort)
  {
    $sql_ok = false;
    $sql = "UPDATE user SET ";
    if($email != ""){
      $sql .= "email = '$email', ";
      $sql_ok = true;
    }
    if($password != "" && $confirm_password == $password){
      $sql .= "password = '$password', ";
      $sql_ok = true;
    }
    if($gender != ""){
      $sql .= "gender = '$gender', ";
      $sql_ok = true;
    }
    if($firstname != ""){
      $sql .= "firstname = '$firstname', ";
      $sql_ok = true;
    }
    if($lastname != ""){
      $sql .= "lastname = '$lastname', ";
      $sql_ok = true;
    }
    if($image_name != ""){
      $sql .= "img_src = '$image_name', ";
      $sql_ok = true;
    }
	if($country != ""){
      $sql .= "country = '$country', ";
      $sql_ok = true;
    }
    if($plz != ""){
      $sql .= "plz = '$plz', ";
      $sql_ok = true;
    }
    if($ort != ""){
      $sql .= "address = '$ort', ";
      $sql_ok = true;
    }
    $sql = substr_replace($sql, ' ', -2, 1);

    $sql .= "WHERE user_id = $user_id;";

    if($sql_ok){
      return get_result($sql);
    }else {
      return false;
    }
  }


	/* *********************************************************
	/* friends.php
	/* ****************************************************** */

  function get_no_friend_list($user_id)
  {
    $sql = "SELECT * FROM user WHERE user_id NOT in
      (SELECT friend FROM userrelations WHERE user = $user_id)
      AND  NOT user_id = $user_id;";
    return get_result($sql);
  }

  function add_friend($user_id, $friend_id)
  {
    $sql = "INSERT INTO userrelations (`user`, `friend`) VALUES ($user_id, $friend_id);";
    get_result($sql);
  }

  function add_friends($user_id, $friend_list)
  {
    foreach ($friend_list as $friend_id) {
      $sql = "INSERT INTO userrelations (`user`, `friend`) VALUES ($user_id, $friend_id);";
      get_result($sql);
    }
  }

	function get_friend_list($user_id){
    $sql = "SELECT * FROM user WHERE user_id in
              (SELECT friend FROM userrelations WHERE user = $user_id)
              AND  NOT user_id = $user_id;";
		return get_result($sql);
	}

  function remove_friend($user_id, $friend_id){
		$sql = "DELETE FROM userrelations WHERE user = $user_id AND friend = $friend_id;";
		get_result($sql);
	}

  function remove_friends($user_id, $friend_list){
		foreach($friend_list as $friend_id){
			$sql = "DELETE FROM userrelations WHERE user = $user_id AND friend = $friend_id;";
			get_result($sql);
		}
	}

?>
