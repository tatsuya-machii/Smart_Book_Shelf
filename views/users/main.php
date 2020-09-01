<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/review.php");
require_once("../../model/User.php");
require_once("../../model/Friend.php");

try{

  // DB接続
  if (empty($_GET["user_id"])) {
    $review = new Review($host, $dbname, $user, $pass);
    $review->connectDb();

  }else{
    $user = new User($host, $dbname, $user, $pass);
    $user->connectDb();
  };


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

  // ページネーション情報
  // GETで現在のページ数を取得する（未入力の場合は1を挿入）
  if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
  } else {
    $page = 1;
  }
  // スタートのポジションを計算する
  if ($page > 1) {
    // 例：２ページ目の場合は、『(2 × 10) - 10 = 10』
    $start = ($page * 8) - 8;
  } else {
    $start = 0;
  }


  if (empty($_GET["user_id"])) {
    // postsテーブルのデータ件数を取得する
  $pagination = $review->pageCount($_SESSION["user"]["id"]);
    // 自分のレビューを取得
    $arr = array("id"=>$_SESSION["user"]["id"], "start"=>$start);
    $result = $review->findByUser($arr);
  }else{
    // postsテーブルのデータ件数を取得する
  $pagination = $user->pageCount($_GET["user_id"]);

    // ユーザー情報とレビュー情報を取得
    $arr = array("id"=>$_GET["user_id"], "start"=>$start);
    $result = $user->findBy($arr);

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


    $(document).on('click', ".ajax_friend_add", function(){

      // 友だち解除ボタンクリックの場合
      if ($(".ajax_friend_add").html() == "友だち登録を解除する") {
        console.log("delete");

        // ajaxの実行
        $.ajax({
          type: "POST",
          url: "ajax_friend_add.php",
          data: {
            "delete_friend_id": queryObject.user_id
          }
        }).done(function(data){
          $('#friend_add').empty();
          $('#friend_add').append(data);
          //クリックによる画面リロードを防ぐ。
        })
        // 友だち追加ボタンクリックの場合
      }else if ($(".ajax_friend_add").html() == "友だちに追加する") {

        // ajaxの実行
        $.ajax({
          type: "POST",
          url: "ajax_friend_add.php",
          data: {
            "create_friend_id": queryObject.user_id
          }
        }).done(function(data){
          $("#friend_add").empty();
          $("#friend_add").append(data);
        })
      }
      //クリックによる画面リロードを防ぐ。
      return false;
    })



    // 読み込み時の友だち数取得
    $(window).on('load', function(){
      $.ajax({
        type: "post",
        url: "ajax_friends_count.php",
        data: {
          "user_id": queryObject.user_id
        }
      })
      .done(function(data){
        $('#ajax_friends').empty();
        $('#ajax_friends').append(data);
      })
    });

    // 読み込み時に表示されているユーザーが友だちかどうか確認
    $(window).on('load', function(){
      $.ajax({
        type: "post",
        url:"ajax_friend_btn.php",
        data: {
          "user_id": queryObject.user_id
        }
      }).done(function(data){
        $('#friend_add').empty();
        $('#friend_add').append(data);
      })
    });


  })

  </script>
</head>
<body>
  <?php require('users_temp/_header.php'); ?>

  <main>
    <?php if (empty($_GET["user_id"])){
      $user_name = $_SESSION["user"]["name"];
      $user_image = $_SESSION["user"]["name"];
    }else{
      $user_name = $result[0]["user_name"];
      $user_image = $result[0]["user_image"];
    }; ?>
    <!-- HOME SECTION -->
    <section id="home">
      <div id="user_inform" class="container">
        <div class="row">
          <div class="col-lg-12 col-sm-12">
            <p class="index text-center">アカウント情報</p>
          </div>
            <div class="col-lg-2 col-sm-2 col-xs-12">
              <?php if (empty($_GET["user_id"]) || $_GET["user_id"] == $_SESSION["user"]["id"]){ ?>
                <?php if (isset($_SESSION["user"]["image"]) && file_exists("../../img/users/".$_SESSION["user"]["image"])){ ?>
                  <img class="user_icon" src="../../img/users/<?php echo $_SESSION["user"]["id"]; ?>.jpg" alt="ユーザーアイコン"><!-- $user_image -->
                <?php }else{ ?>
                    <i class="fas fa-user main_icon"></i><!-- $user_image -->
                <?php } ?>
              <?php }else{ ?>
                <?php if (isset($_GET["user_image"]) && file_exists("../../img/users/".$_GET["user_image"])){ ?>
                  <img class="user_icon" src="../../img/users/<?php echo $_GET["user_id"]; ?>.jpg" alt="ユーザーアイコン"><!-- $user_image -->
                <?php }else{ ?>
                    <i class="fas fa-user main_icon"></i><!-- $user_image -->
                <?php } ?>

              <?php } ?>

            </div>
            <div id="user_text" class="col-lg-9 col-sm-9  col-xs-12 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
              <p>名前：<?php echo $user_name ?></p>
              <?php if (empty($_GET["user_id"]) || $_GET["user_id"] == $_SESSION["user"]["id"]): ?>
                <p>メールアドレス：<?php echo $_SESSION["user"]["mail"] ?></p>
              <?php endif; ?>
                <p>友だち：
                  <a href="friend_index.php<?php if(!empty($_GET["user_id"]) && $_GET["user_id"] != $_SESSION["user"]["id"]){echo "?user_id=".$_GET["user_id"];} ?>">
                    <span id="ajax_friends"></span>人
                  </a>
                </p>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-3 col-lg-offset-10 col-md-offset-10 col-sm-offset-9 text-right">
              <?php if (empty($_GET["user_id"]) || $_GET["user_id"] == $_SESSION["user"]["id"]){ ?>
                <a class="edit_link" href="user_edit.php">編集する</a>
              <?php }else{ ?>
                <div id="friend_add"></div>
              <?php }; ?>
            </div>
        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
    <section id="book_shelf">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-sm-12">
            <p class="index"><?php echo $user_name; ?>さんの本棚</p>
            <?php if(empty($_GET["user_id"]) || $_GET["user_id"] == $_SESSION["user"]["id"]): ?>
              <a href="review_book_search.php">本を追加する</a>
            <?php endif; ?>


            <div id="book_shelf_container">
              <?php if (!empty($result[0]["book_id"])): ?>
                <?php for ($i=0; $i < count($result); $i++): ?>
                  <?php $url = "review_show.php?review_id=".$result[$i]["review_id"]; ?>

                  <div class="book_container">
                    <div class="book_image">
                      <a href="<?php echo $url; ?>">
                        <?php if (file_exists("../../img/books/".$result[$i]["book_id"].".jpg")){ ?>
                          <img  class="book_icon" src="../../img/books/<?php echo $result[$i]["book_id"]; ?>.jpg" alt="ブックアイコン"><!-- $user_image -->
                        <?php }else{ ?>
                          <i class="fas fa-book-open"></i>
                        <?php } ?>
                      </a>

                    </div>
                    <p>名前：<?php echo $result[$i]["book_name"] ?></p>
                    <p>おすすめ度：
                      <span class="range-group">

                        <?php for ($j=1; $j <= $result[$i]["recommends"]; $j++):?>
                          <img src="../../img/base/star-on.png">
                        <?php endfor ?>
                        <?php if ($result[$i]["recommends"] < 5) {for ($j=$result[$i]["recommends"]; $j < 5; $j++):?>
                          <img src="../../img/base/star-off.png">
                        <?php endfor;} ?>

                      </span>
                    </p>
                    <p>追加日時：<?php echo $result[$i]["created_at"] ?></p>
                    <p><a href="<?php echo $url; ?>">詳しく見る</a></p>
                  </div>
                <?php endfor ?>

              <?php endif; ?>
            </div>

          </div>
          <div class="col-lg-12 col-sm-12 text-center">
            <?php for ($x=1; $x <= $pagination ; $x++) {
              if (!empty($_GET["user_id"])) { ?>
                <a href="?user_id=<?php echo $_GET["user_id"] ?>&page=<?php echo $x ?>"><?php echo $x; ?></a>
              <?php }else{ ?>
                <a href="?page=<?php echo $x ?>"><?php echo $x; ?></a>
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php require('users_temp/_footer.php'); ?>
</body>
</html>
