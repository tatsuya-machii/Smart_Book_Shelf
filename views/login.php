<?php
session_start();
require_once("../config/config.php");
require_once("../model/User.php");
require "../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;


// twitter_login
if (isset($_GET["twitter_login"])) {
  // twitter API
  define('TWITTER_API_KEY', '1JpUVP7dNOL8V81WpshXEsdFP');//Consumer Key (API Key)
  define('TWITTER_API_SECRET', 'ySoffbkluE5AVYtiFx9Id4CnVdNErwHvw9wiJQR1AewNDiKPuV');//Consumer Secret (API Secret)
  define('CALLBACK_URL', 'http://127.0.0.1/curriculum/Smart_Book_Shelf/views/callback.php');//Twitterから認証した時に飛ぶページ場所

  //「abraham/twitteroauth」ライブラリのインスタンスを生成し、Twitterからリクエストトークンを取得する
  $connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);
  $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => CALLBACK_URL));

  //リクエストトークンはcallback.phpでも利用するのでセッションに保存する
  $_SESSION['oauth_token'] = $request_token['oauth_token'];
  $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

  //Twitterの認証画面のURL
  $oauthUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
  header('Location: '.$oauthUrl);
  exit;
}

try{
  // DB接続
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDb();

  // ログインしている場合はusers/main.phpにリダイレクト。
  if (!empty($_SESSION["user"])) {
    header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
    exit;
  };

  // login
  if ($_POST){
    // 入力のバリデーション
    $message = $user->loginvalidate($_POST);
    if (!isset($message["mail"]) && !isset($message["password"])) {
      // 入力内容からユーザー情報の取得
      if ($user->login($_POST)) {
        $_SESSION["user"] = $user->login($_POST);

        // 取得したアカウントが退会済みの場合はメッセージを出力
        if ($_SESSION["user"]["status"] == 1) {
          $message["withdrawal"] = "このアカウントは退会済みです。";
          $_SESSION = array();
          session_destroy();
        }else{
          // 以下、ログイン（画面繊維処理）
          // 管理者権限がある場合はadmin/main.phpにリダイレクト。
          if ($_SESSION["user"]["role"] == 1) {
            header('location: /curriculum/Smart_Book_Shelf/views/admin/main.php');
            exit;
          }else{
            // 管理者権限がない場合はmain.phpにリダイレクト。
            header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
            exit;
          }
        }

      }else{
        // アカウントが見つからない場合
        $message["login_error"] = "アカウントが見つかりません。";
      };
    }
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
   <?php require('login_temp/_header.php'); ?>

  <main>
    <div id="login_container">
      <p id="login_index">Smart Book Shelf</p>
      <div id="icon">
        <i class="fas fa-book-open"></i>
      </div>

      <form action="" method="post">

        <p class="error"><?php if(!empty($message["login_error"])){echo $message["login_error"];}?></p>
        <p class="error"><?php if(!empty($message["withdrawal"])) {echo $message["withdrawal"];}?></p>
        <p class="error"><?php  if (isset($message["mail"]))echo $message["mail"]; ?></p>
        <input type="text" name="mail" placeholder="email">
        <p class="error"><?php  if (isset($message["password"]))echo $message["password"]; ?></p>
        <input type="password" name="password" placeholder="password">
        <input type="submit" class="btn btn-warning" value="ログイン">
      </form>
      <a href="?twitter_login=1"><img src="../img/base/sign-in-with-twitter-btn.png"></a>
      <a class="grayLink" href="signup.php">新規アカウント作成</a></br>
      <a class="grayLink" href="forget.php">パスワードをお忘れですか？</a>


    </div>
  </main>

  <?php require('login_temp/_footer.php'); ?>
</body>
</html>
