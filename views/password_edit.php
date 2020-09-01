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

  if (!empty($_POST)) {
    // バリデーション
    $message = $user->passwordValidate($_POST);
    if (!isset($message["password"])) {
      // パスワード変更
      $user->passwordEdit($_POST);
      // セッション情報（仮ID、仮パスワード）を削除してログイン画面へ
      $_SESSION = array();
      session_destroy();
      header("location: /curriculum/Smart_Book_Shelf/views/login.php");
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

  <p>新しいパスワードを入力してください。</p>
  <p class="error"><?php  if (isset($message["password"]))echo $message["password"]; ?></p>
  <input type="hidden" name="id" value="<?php echo $_SESSION["temporary_id"]; ?>">
  <input type="text" name="password" placeholder="パスワード">
  <input type="submit" class="btn btn-warning" value="送信">
</form>

    </div>
  </main>

  <?php require('login_temp/_footer.php'); ?>
</body>
</html>
