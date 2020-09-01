<?php
session_start();
require_once("../config/config.php");
require_once("../model/User.php");

try{
  // DB接続
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDb();

  // ログインしている場合はusers/main.phpにリダイレクト。
  if (!empty($_SESSION["user"])) {
    header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
    exit;
  };

  if (isset($_POST["mail"])) {
    $result = $user->findByMail($_POST["mail"]);
    if (empty($result["error"])) {
      $_SESSION["temporary_id"] = $result;
      header("location: /curriculum/Smart_Book_Shelf/views/identification.php");
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
   <?php require('login_temp/_header.php'); ?>

  <main>
    <div id="login_container">
      <p id="login_index">Smart Book Shelf</p>
      <div id="icon">
        <i class="fas fa-book-open"></i>
      </div>

      <form action="" method="post">

        <p class="error"><?php if(!empty($result["error"])) {echo $result["error"];}?></p>
        <p style="width:300px;">
          登録されたメールアドレスを入力し、送信ボタンを押してください。
          登録が確認できた場合はメールで仮パスワードを送信します。
          メールの内容に沿ってお手続きください。
        </p>
        <input type="text" name="mail" placeholder="email">
        <input type="submit" class="btn btn-warning" value="送信">
      </form>
      <a class="grayLink" href="login.php">ログイン画面に戻る</a>

    </div>
  </main>

  <?php require('login_temp/_footer.php'); ?>
</body>
</html>
