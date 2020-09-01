<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/book.php");

try{
  // DB接続
  $book = new Book($host, $dbname, $user, $pass);
  $book->connectDb();

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

  // 本の登録
  if (!empty($_POST)) {
    // 入力のバリデーション
    $message = $book->validate($_POST);
    if (!isset($message["name"]) && !isset($message["author"]) && !isset($message["publisher"])) {
      // 本を作成
      if ($result = $book->create($_POST)) {
        $url = "location: /curriculum/Smart_Book_Shelf/views/users/review_create.php?id=".$result;
        header("$url");
        exit;
      }else{
        // 登録できなかった場合、本の登録の有無を確認する。
        $book_confirm = $book->deleteConfirm($_POST);
        // 本が存在する場合
        if (isset($book_confirm)) {
          // 本が削除済みの場合
          if ($book_confirm["status"] == 1) {
            $message["error"] = "この本は管理者によって削除されたため、現在登録できません。";
          }else{
            $message["error"] = "この本は既に存在します。";
          }
        }else{
          // 本が存在しないが登録できなかった場合
          $message["error"] = "エラーが発生しました。";
        }
      }
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

    <!-- FORM SECTION -->
    <section id="book_create">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <p class="index"><?php echo $_SESSION["user"]["name"] ?>さんの本棚</p>
            <p class="index_right"><strong>本の追加</strong></p>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 ">
              <i class="fas fa-book-open"></i>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
              <form action="" method="post">
                <p class="error"><?php if (!empty($message["error"])) echo $message["error"]; ?></p>
                <input type="hidden" name="created_user_id" value="<?php echo $_SESSION["user"]["id"] ?>">
                <p><strong>本のタイトル</strong></p>
                <p class="error"><?php  if (isset($message["name"]))echo $message["name"]; ?></p>
                <input type="text" name="name" placeholder="本のタイトル">
                <p><strong>著者</strong></p>
                <p class="error"><?php  if (isset($message["author"]))echo $message["author"]; ?></p>
                <input type="text" name="author" placeholder="著者">
                <p><strong>出版社</strong></p>
                <p class="error"><?php  if (isset($message["publisher"]))echo $message["publisher"]; ?></p>
                <input type="text" name="publisher" placeholder="出版社"></br>
                <input class="btn btn-warning" type="submit" value="本を登録して続ける">
              </form>

            </div>

          </div>
        </div>
      </div>




    </section>
  </main>

  <?php require('users_temp/_footer.php'); ?>
</body>
</html>
