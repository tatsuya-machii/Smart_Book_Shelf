<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Book.php");
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

  try{
    // DB接続
    $book = new Book($host, $dbname, $user, $pass);
    $book->connectDB();

    // 本の更新・登録
    if ($_POST) {
      $message = $book->validate($_POST);
      if (!isset($message["name"]) && !isset($message["author"]) && !isset($message["publisher"])) {
        if (empty($_POST["id"])) {
          $book->adminCreate($_POST);
          header("location: /curriculum/Smart_Book_Shelf/views/admin/books_table.php");
          exit;
        }else{
          $book->edit($_POST);
          header("location: /curriculum/Smart_Book_Shelf/views/admin/books_table.php");
          exit;
        }
      }
    }

    // 本の削除
    if (!empty($_GET["delete"])) {
      $book->adminDelete($_GET["delete"]);
    }


    //本の情報取得
    if (!empty($_GET["book_id"])) {
      $result = $book->FindBy($_GET["book_id"]);
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
  <link rel="stylesheet" href="../../css/admin/main.css">
  <title>Smart Book Shelf</title>
</head>
<body>
  <?php require('admins_temp/_header.php'); ?>

  <main>
    <!-- HOME SECTION -->
    <section id="book_table">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index">管理者専用ページ</p>
            <p class="index_right"><strong>books_table</strong></p>
            <p class="mb20"><a href="books_table.php">戻る</a></p>

          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <td>id</td>
                  <td>本のタイトル</td>
                  <td>著者</td>
                  <td>出版社</td>
                  <td>イメージ画像</td>
                  <td>状態</br>1:アクティブ</br>2:削除済み</td>
                  <td>登録ユーザーID</td>
                  <td></td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <form action="" method="POST" enctype="multipart/form-data">
                    <td><p><?php if(!empty($result)) echo $result["id"]; ?></p></td>
                    <input type="hidden" name="id" value="<?php if(!empty($result)) echo $result["id"]; ?>">
                    <td><input type="text" name="name" value="<?php if(!empty($result)) echo $result["name"]; ?>"></td>
                    <td><input type="text" name="author" value="<?php if(!empty($result)) echo $result["author"]; ?>"></td>
                    <td><input type="text" name="publisher" value="<?php if(!empty($result)) echo $result["publisher"]; ?>"></td>
                    <td><input type="file" name="image" accept=".jpg"></td>
                    <td>
                      <p class="radio"><input type="radio" name="status" value="0" <?php if(!empty($result)){ if($result["status"]==0) echo 'checked="checked"';};?>>0（アクティブ）</br><input type="radio" name="status" value="1" <?php if(!empty($result)){ if($result["status"]==1) echo 'checked="checked"';};?>>1(削除済)</p>
                    </td>
                    <td><input type="text" name="created_user_id" value="<?php if(!empty($result)) echo $result["created_user_id"]; ?>"></td>
                    <td><input class="btn btn-default" type="submit" value="登録"></td>
                  </form>
                </tr>
              </tbody>
            </table>
            <p>※新規登録の場合、画像は登録されません。</p>
            <?php  if (isset($message["name"]))echo $message["name"]."</br>"; ?>
            <?php  if (isset($message["author"]))echo $message["author"]."</br>"; ?>
            <?php  if (isset($message["publisher"]))echo $message["publisher"]; ?>
          </div>


        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
  </main>

  <?php require('admins_temp/_footer.php'); ?>
</body>
</html>
