<?php
session_start();
  // ログアウト処理
  if (isset($_GET["logout"])) {
    $_SESSION = array();
    session_destroy();
  }

  // ログインしていない場合はlogin.phpにリダイレクト。
  if (empty($_SESSION["user"])) {
    header("location: /curriculum/Smart_Book_Shelf/views/login.php");
    exit;
  // 管理者権限がない場合はmain.phpにリダイレクト。
  }elseif ($_SESSION["user"]["role"] != 1) {
    header("location: /curriculum/Smart_Book_Shelf/views/main.php");
    exit;
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
  <link rel="stylesheet" href="../../css/admin/main.css">
  <title>Smart Book Shelf</title>
</head>
<body>
  <?php require('admins_temp/_header.php'); ?>

  <main>
    <!-- HOME SECTION -->
    <section id="home">
      <div id="user_inform" class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index text-center">アカウント情報</p>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
            <i class="fas fa-user main_icon"></i><!-- $user_image -->
          </div>
          <div id="user_text" class="col-lg-9 col-md-9 col-sm-9  col-xs-12 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
            <p>ID:<?php echo $_SESSION["user"]["id"] ?></p>
            <p>名前：<?php echo $_SESSION["user"]["name"] ?></p>
            <p>メールアドレス：<?php echo $_SESSION["user"]["mail"] ?></p>
            <p>権限：管理者</p>
          </div>
        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
    <section id="book_shelf">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index">管理者専用ページ</p>
            <p class="index_right"><strong>メインページ</strong></p>
            <p><a class="top" href="users_table.php">ユーザー一覧</a></p>
            <p><a class="top" href="books_table.php">本一覧</a></p>
            <p><a class="top" href="reviews_table.php">レビュー一覧</a></p>
            <p><a class="top" href="goods_table.php">いいね一覧</a></p>
            <p><a class="top" href="friends_table.php">友だち一覧</a></p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php require('admins_temp/_footer.php'); ?>
</body>
</html>
