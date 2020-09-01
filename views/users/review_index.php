<?php
session_start();
require_once("../../config/config.php");
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

  if (isset($_GET["user_id"])) {
    // code...
  }

  // 本のデータ取得
  $result = $review->findAll();


}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
};

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
  <!-- ajax読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

  <link rel="stylesheet" href="../../css/base/base.css">
  <link rel="stylesheet" href="../../css/users/main.css">
  <title>Smart Book Shelf</title>
  <script>
  $(function search_reviews(){
    $('input.search').on("input", function(){
      $.ajax({
        type: "get",
        url: "ajax_review_all.php",
        datatype:"json",
        data:{"search_user_name": $("input.user_name").val(),"search_book_name": $("input.book_name").val()}
      })
      .done(function(data){
        console.log(data);
        $("#ajax_review_list").empty();

        $.each(data, function(arr, key){

          var html ="<tr><td>";
          var html = html + "<p>" + key["user_image"];
          var html = html + "</p><p><a href='main.php?user_id=" + key["user_id"] + "'>" + key["user_name"]+ "</a></p></td>";
          var html = html + "<td>";
          var html = html +  "<p>" + key["book_image"];
          var html = html + "</p><p><a href='review_show.php?user_id=" + key["user_id"]+ "&review_id=" + key["id"]+ "'>" + key["book_name"] + "</p></td>";
          var html = html + "<td><span class='range-group'" + key["recommends"] + "</span></td>";
          var html = html + "<td>" + key["created_at"] + "</td></tr>"
          $('#ajax_review_list').append(html);
        });
      })
      .fail(function(data){
        console.log('通信失敗');
        console.log(data);
      });
    })
  })


  </script>
</head>
<body>
  <?php require('users_temp/_header.php'); ?>

  <main>

    <!-- HOME SECTION -->
    <section id="review_book_search">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index">最近の投稿</p>
          </div>
          <!-- 入力フォーム -->
          <form class="clearfix">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <p><strong>ユーザー名</strong></p>
              <input class="search user_name" type="text" name="search_user_name" placeholder="ユーザー名">
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <p><strong>本のタイトル</strong></p>
              <input class="search book_name" type="text" name="search_book_name" placeholder="本のタイトル">
            </div>
          </form><!-- 入力フォーム -->
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>投稿者</th>
                  <th>読まれた本</th>
                  <th>おすすめ度</th>
                    <th class="hidden-xs">投稿日時</th>
                </tr>
              </thead>
              <tbody id="ajax_review_list">
                  <?php foreach ($result as $review): ?>
                    <tr>
                      <td>
                        <p>
                          <?php if (file_exists("../../img/users/".$review["user_id"].".jpg")){ ?>
                            <img  class="small_icon" src="../../img/users/<?php echo $review["user_id"]; ?>.jpg" alt="ユーザーアイコン"><!-- $user_image -->
                          <?php }else{ ?>
                            <i class="fas fa-user small_icon user"></i>
                          <?php } ?>
                        </p>
                        <p>
                          <a href='main.php?user_id=<?php echo $review["user_id"]; ?>'><?php echo $review["user_name"]; ?></a>
                        </p>
                      </td>
                      <td>
                        <p>
                          <?php if (file_exists("../../img/books/".$review["book_id"].".jpg")){ ?>
                            <img  class="small_icon" src="../../img/books/<?php echo $review["book_id"]; ?>.jpg" alt="ブックアイコン"><!-- $user_image -->
                          <?php }else{ ?>
                            <i class="fas fa-book-open small_icon"></i>
                          <?php } ?>
                        </p>
                        <p>
                          <a href='review_show.php?user_id=<?php echo $review["user_id"]; ?>&review_id=<?php echo $review["id"]; ?>'><?php echo $review["book_name"]; ?></a>
                        </p>
                      </td>
                      <td>
                        <p class="recommends_box">
                          <span class="range-group">

                            <?php for ($j=1; $j <= $review["recommends"]; $j++):?>
                              <img src="../../img/base/star-on.png">
                            <?php endfor ?>
                            <?php if ($review["recommends"] < 5) {for ($j=$review["recommends"]; $j < 5; $j++):?>
                              <img src="../../img/base/star-off.png">
                            <?php endfor;} ?>

                          </span>
                        </p>
                      </td>
                      <td class="hidden-xs"><p><?php echo $review["created_at"]; ?></p></td>
                    </tr>

                  <?php endforeach; ?>

              </tbody>
            </table>

          </div>


        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
  </main>

  <?php require('users_temp/_footer.php'); ?>
</body>
</html>
