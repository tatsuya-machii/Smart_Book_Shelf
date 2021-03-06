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

  // reviewの更新後、メインページにリダイレクト。
  if ($_POST) {
    // 入力のバリデーション
    $message = $review->validate($_POST);
    if (!isset($message["recommends"]) && !isset($message["description"]) && !isset($message["impression"])) {

      $review->edit($_POST);
      header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
      exit;
    }
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
  <script>
    $(function(){
      $('.range-group > a').on('click', function(){
        var index = $(this).index();
        for (var i = 0; i <= index; i++) {
          $('.range-group > a > img').eq(i).attr('src', '../../img/base/star-on.png');
        }
        if (index < 4) {
          for (var i = index + 1; i < 5; i++) {
            $('.range-group > a > img').eq(i).attr('src', '../../img/base/star-off.png');
          };
        }
        $(this).parent().find('.input-range').attr('value', index + 1);
        return false;
      })
    })
  </script>
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
            <p class="index_right"><strong>本の編集</strong></p>
            <div class="col-sm-3 col-xs-6 ">
              <?php if (file_exists("../../img/books/".$result["book_id"].".jpg")){ ?>
                <img  class="book_icon" src="../../img/books/<?php echo $result["book_id"]; ?>.jpg" alt="ブックアイコン"><!-- $user_image -->
              <?php }else{ ?>
                <i class="fas fa-book-open"></i>
              <?php } ?>
            </div>
            <div class="col-sm-9 col-xs-6 ">
              <p><strong>本のタイトル</strong></p>
              <p><?php echo $result["book_name"] ?></p>
              <p><strong>著者</strong></p>
              <p><?php echo $result["author"] ?></p>

              <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $result["id"]; ?>">
                <p><strong>おすすめ度</strong></p>
                <p class="error"><?php  if (isset($message["recommends"]))echo $message["recommends"]; ?></p>
                <div class="range-group">
                  <?php for ($i=0; $i < $result["recommends"]; $i++):?>
                    <a href=""><img src="../../img/base/star-on.png"></a>
                  <?php endfor ?>
                  <?php if ($result["recommends"] < 5) {for ($i=$result["recommends"]; $i < 5; $i++):?>
                    <a href=""><img src="../../img/base/star-off.png"></a>
                  <?php endfor;} ?>
                  <input type="range" name="recommends" min="1" max="5" value="<?php echo $result["recommends"] ?>" class="hidden input-range">
                </div>
              </div>

              <div class=" col-sm-9 col-xs-12 col-sm-offset-3">
                <p><strong>本の概要</strong></p>
                <p class="error"><?php  if (isset($message["description"]))echo $message["description"]; ?></p>
                <textarea name="description" rows="8" cols="80">
                  <?php
                    if (isset($_POST["description"])) {
                      echo $_POST["description"];
                    }elseif (isset($result["description"])) {
                      echo $result["description"];
                    };
                  ?>
                </textarea>
                <p><strong>感想</strong></p>
                <p class="error"><?php  if (isset($message["impression"]))echo $message["impression"]; ?></p>
                <textarea name="impression" rows="8" cols="80">
                  <?php
                    if (isset($_POST["impression"])) {
                      echo $_POST["impression"];
                    }elseif (isset($result["impression"])) {
                      echo $result["impression"];
                    };
                  ?>
                </textarea></br>
                <input class="btn btn-warning" type="submit" value="登　録">
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
