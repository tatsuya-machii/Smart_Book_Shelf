<?php
session_start();
require_once("../config/config.php");
require_once("../model/User.php");

try{
  // DB接続
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDb();

  // 新規登録
  if ($_POST){
    // 入力のバリデーション
    $message = $user->validate($_POST);
    if (!isset($message["name"]) && !isset($message["mail"]) && !isset($message["password"])) {
      if ($user->create($_POST)) {
        $_SESSION["user"] = $user->login($_POST);
        header('location: /curriculum/Smart_Book_Shelf/views/users/main.php');
        exit;
      }else{
        $message["login_error"] = "このアカウントは現在ご利用いただけません。";
      };
    };
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
  <link rel="stylesheet" href="../css/base/bootstrap.css">
  <script type="text/javascript" src="../js/jquery.js"></script>
  <script type="text/javascript" src="../js/bootstrap.js"></script>
  <link rel="stylesheet" href="../css/base/base.css">
  <link rel="stylesheet" href="../css/login/login.css">
  <title>Smart Book Shelf</title>
  <script>
  </script>
</head>
<body>
  <!-- 外部ファイル「headeｒ」読み込み -->
  <?php require('login_temp/_header.php'); ?>

  <main>
    <div id="login_container">
      <p id="login_index">Smart Book Shelf</p>
      <div id="icon">
        <i class="fas fa-book-open"></i>
      </div>
      <form action="" method="post">
        <p class="error"><?php  if (isset($message["login_error"]))echo $message["login_error"]; ?></p>
        <p class="error"><?php  if (isset($message["name"]))echo $message["name"]; ?></p>
        <input type="text" name="name" placeholder="name">
        <p class="error"><?php  if (isset($message["mail"]))echo $message["mail"]; ?></p>
        <input type="text" name="mail" placeholder="email">
        <p class="error"><?php  if (isset($message["password"]))echo $message["password"]; ?></p>
        <input type="password" name="password" placeholder="password">
        <input type="submit" class="btn btn-warning" value="登　　録">
      </form>
      <a href="login.php">アカウントをお持ちの方</a>


    </div>
  </main>
  <!-- 外部ファイル「footer」読み込み -->
  <?php require('login_temp/_footer.php'); ?>
</body>
</html>
