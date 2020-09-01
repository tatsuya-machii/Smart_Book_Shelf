<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/User.php");

try{
  // DB接続
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDb();

  // ログアウト処理
  if (isset($_GET["logout"])) {
    $_SESSION = array();
    session_destroy();
  }

  // ログインしていない場合はlogin.phpにリダイレクト。
  if (empty($_SESSION["user"])) {
    header("location: /curriculum/Smart_Book_Shelf/views/login.php");
    exit;
  };

  // ユーザー情報更新
  if ($_POST) {
    // パスワードの確認
    if (password_verify($_POST["current_password"], $_SESSION["user"]["password"])) {
      $message = $user->validate($_POST);
      if (!isset($message["name"]) && !isset($message["mail"]) && !isset($message["password"])) {
        $user->edit($_POST);
        $_SESSION["user"] = $user->login($_POST);
        print_r($_POST);
        header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
        exit;
    }
  }else{
    $message["current_password"] = "パスワードが一致しません。";
  }
  }



}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
}

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <!-- fontawesome読み込み -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
  <!-- bootstrap読み込み -->
  <link rel="stylesheet" href="../../css/base/bootstrap.css">
  <script type="text/javascript" src="../../js/jquery.js"></script>
  <script type="text/javascript" src="../../js/bootstrap.js"></script>
  <link rel="stylesheet" href="../../css/base/base.css">
  <link rel="stylesheet" href="../../css/users/main.css">
  <title>Smart Book Shelf</title>
</head>
<body>
  <?php require('users_temp/_header.php'); ?>

  <main>
    <!-- HOME SECTION -->
    <section id="user_edit">

      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index">アカウント編集</p>

            <div class="user_edit_form">
              <?php if (file_exists("../../img/users/".$_SESSION["user"]["id"].".jpg")){ ?>
                <img class="user_icon" src="../../img/users/<?php echo $_SESSION["user"]["id"]; ?>.jpg" alt="ユーザーアイコン"><!-- $user_image -->
              <?php }else{ ?>
                  <i class="fas fa-user main_icon"></i><!-- $user_image -->
              <?php } ?>
              <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="image" accept=".jpg">
                <input type="hidden" name="id" value="<?php echo $_SESSION["user"]["id"] ?>">
                <label for="name">名前</label>
                <p class="error"><?php  if (isset($message["name"]))echo $message["name"]; ?></p>
                <input type="text" name="name" placeholder="name" value="<?php echo $_SESSION["user"]["name"] ?>">
                <label for="name">メールアドレス</label>
                <p class="error"><?php  if (isset($message["mail"]))echo $message["mail"]; ?></p>
                <input type="text" name="mail" placeholder="email" value="<?php echo $_SESSION["user"]["mail"] ?>">
                <label for="name">現在のパスワード</label>
                <p class="error"><?php  if (isset($message["current_password"]))echo $message["current_password"]; ?></p>
                <input type="password" name="current_password" placeholder="password">
                <label for="name">新しいパスワード</label>
                <p class="error"><?php  if (isset($message["password"]))echo $message["password"]; ?></p>
                <input type="password" name="password" placeholder="password">
                <input type="submit" class="btn btn-warning" value="登　録">
              </form>
              <a class="grayLink" href="user_delete_confirm.php">このアカウントを削除する</a>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>

  <?php require('users_temp/_footer.php'); ?>
</body>
</html>
