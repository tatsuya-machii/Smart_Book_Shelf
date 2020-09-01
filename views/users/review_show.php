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


  // 本の詳細情報取得
  if (isset($_GET["review_id"])) {

    $result = $review->findByBook($_GET["review_id"]);
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
    // 以下でGET情報を取得する
    var queryString = window.location.search;
    var queryObject = new Object();
    if(queryString){
      queryString = queryString.substring(1);
      var parameters = queryString.split('&');

      for (var i = 0; i < parameters.length; i++) {
        var element = parameters[i].split('=');

        var paramName = decodeURIComponent(element[0]);
        var paramValue = decodeURIComponent(element[1]);

        queryObject[paramName] = paramValue;
      }
    }


    $(document).on('click', '#good_add', function(){
      if ($('#good_add').hasClass("already")) {
        // いいね済みの場合の処理
        $.ajax({
          type: "POST",
          url: "ajax_good_add.php",
          data: {
            "delete_good_id": queryObject.review_id
          }
        }).done(function(data){
          $('.good_count').empty();
          $('.good_count').append(data + "件");
          $('#good_add').removeClass("already");
        })
      }else{
        // いいねの追加
        $.ajax({
          type: "POST",
          url: "ajax_good_add.php",
          data: {
            "create_good_id": queryObject.review_id
          }
        }).done(function(data){
          $('.good_count').empty();
          $('.good_count').append(data + "件");
          $('#good_add').addClass("already");
        })

      }
      //クリックによる画面リロードを防ぐ。
      return false;
    })

    // 読み込み時に表示されている投稿にいいねしているか確認
    $(window).on('load', function(){
      $.ajax({
        type: "post",
        url: "ajax_good_btn.php",
        data: {
          review_id: queryObject.review_id
        }
      }).done(function(data){
        $('#good_add').addClass(data);
      })
    })

    // 読み込み時のいいね数取得
    $(window).on('load', function(){
      $.ajax({
        type: "post",
        url: "ajax_goods_count.php",
        data:{
          "review_id": queryObject.review_id
        }
      })
      .done(function(data){
        $('.good_count').empty();
        $('.good_count').append(data + "件");
      })
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
            <p class="index"><?php echo $result["user_name"] ?>さんの本棚</p>
            <p class="index_right"><strong>本の詳細</strong></p>
            <?php if ($_SESSION["user"]["id"] != $result["user_id"]) { ?>
               <p><a href="main.php?user_id=<?php echo $result["user_id"]; ?>"><?php echo $result["user_name"]; ?>さんのマイページに戻る</a></p>
            <?php }; ?>
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
                <?php if ($_SESSION["user"]["id"] == $result["user_id"]) { ?>
                  <p>
                    <img class="good" src="../../img/base/good.jpg" alt="いいね！">
                    <a class="good_count" href="good_index.php?review_id=<?php echo $_GET["review_id"]?>">件</a>
                  </p>
                <?php }else{ ?>
                  <p>
                    <a id="good_add" href=""><img class="good" src="../../img/base/good.jpg" alt="いいね！"></a>
                    <span> </span>
                    <a class="good_count" href="good_index.php?review_id=<?php echo $_GET["review_id"]?>">件</a>
                  </p>
                <?php } ?>
                <?php if ($_SESSION["user"]["id"] == $result["user_id"]) { ?>
                  <a class="btn btn-warning" href="review_edit.php?id=<?php echo $result["id"] ?>" type="submit">編　集</a>
                  <a class="btn btn-warning" href="review_delete_confirm.php?id=<?php echo $result["id"] ?>" type="submit">削　除</a>
                <?php } ?>
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
