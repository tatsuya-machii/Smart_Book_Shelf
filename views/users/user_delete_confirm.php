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

  // 退会
  if (isset($_GET["delete"])) {
    $user->delete($_SESSION["user"]["id"]);
    $_SESSION = array();
    session_destroy();
  }

  // ログインしていない場合はlogin.phpにリダイレクト。
  if (empty($_SESSION["user"])) {
    header("location: /curriculum/Smart_Book_Shelf/views/login.php");
    exit;
  };




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
              <img class="user_icon" src="../../img/icon_sample.jpg" alt="ユーザーアイコン">
              <p class="danger">退会するとこのアカウントに関す情報は全て失われます。</p>
              <p>名前</p>
              <p><?php echo $_SESSION["user"]["name"] ?></p>
              <p>メールアドレス</p>
              <p><?php echo $_SESSION["user"]["mail"] ?></p>
              <a class="btn btn-warning" href="user_edit.php">戻　る</a>
              <a class="btn btn-warning" href="?delete=1" onClick= "if(!confirm('本当に削除しますか？')) return false">退　会</a>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>

  <?php require('users_temp/_footer.php'); ?>
</body>
</html>
