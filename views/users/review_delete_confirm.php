<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/book.php");
require_once("../../model/review.php");

try{
  // DB接続
  $review = new Review($host, $dbname, $user, $pass);
  $review->connectDb();

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

  // reviewの削除後、メインページにリダイレクト。
  if (isset($_GET["delete"])) {
    $review->delete($_GET["delete"]);
    header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
    exit;
  }


  // 本の詳細情報取得
  if (isset($_GET["id"])) {
    $result = $review->findByBook($_GET["id"]);
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
    <section id="review_create">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
            <p class="index"><?php echo $_SESSION["user"]["name"] ?>さんの本棚</p>
            <p class="index_right"><strong>本の削除</strong></p>
            <div class="col-sm-3 col-xs-6">
              <?php if (file_exists("../../img/books/".$result["book_id"].".jpg")){ ?>
                <img  class="book_icon" src="../../img/books/<?php echo $result["book_id"]; ?>.jpg" alt="ブックアイコン"><!-- $user_image -->
              <?php }else{ ?>
                <i class="fas fa-book-open"></i>
              <?php } ?>
            </div>
            <div class="col-sm-9 col-xs-6">
              <p class="danger">この投稿を削除してもよろしいですか？</p>
              <p><strong>本のタイトル</strong></p>
              <p><?php echo $result["book_name"] ?></p>
              <p><strong>著者</strong></p>
              <p><?php echo $result["author"] ?></p>

              <p><strong>おすすめ度</strong></p>
              <div class="range-group">
                <?php for ($i=1; $i <= $result["recommends"]; $i++):?>
                  <img src="../../img/base/star-on.png">
                <?php endfor ?>
                <?php if ($result["recommends"] < 5) {for ($i=$result["recommends"]; $i < 5; $i++):?>
                  <img src="../../img/base/star-off.png">
                <?php endfor;} ?>
                <input type="range" name="recommends" min="1" max="5" value="1" class="hidden input-range">
              </div>
            </div>
            <div class=" col-sm-9 col-xs-12 col-sm-offset-3">
                <p><strong>本の概要</strong></p>
                <p><?php echo $result["description"] ?></p>
                <p><strong>感想</strong></p>
                <p><?php echo $result["impression"] ?></p>
                <a class="btn btn-warning" href="review_show.php?review_id=<?php echo $result["id"] ?>" type="submit">戻　る</a>
                <a class="btn btn-warning" href="?delete=<?php echo $result["id"] ?>" onClick= "if(!confirm('本当に削除しますか？')) return false">削　除</a>
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
