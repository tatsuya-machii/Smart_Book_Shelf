<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/User.php");

try{
  // DB接続
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDb();

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

  // ユーザー情報取得
  $result = $user->findById($_SESSION["user"]["id"]);

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
  $(function(){
    searchWord();

    // 絞り込み検索
    function searchWord(){
      $("input.search").on("input", function(){
        $.ajax("/curriculum/Smart_Book_Shelf/views/users/ajax_book_search.php",
          {
          type: 'get',
          data: {search_name: $("input.name").val(), search_author: $("input.author").val()},
          dataType: 'text'
          }
        ).done(function(data){
          $("#ajax_book_list").empty();

          var result = JSON.parse(data);
          $.each(result, function(arr, key){
            var html ="<tr><td><p>";
            var html = html +  key["image"];
            var html = html + "</p><p><a href='review_create.php?id=" + key['id'] + "'>" + key['name'] + "</a></p></td>";
            var html = html + "<td>" + key['author'] + "</td>";
            var html = html + "<td>" + key['publisher'] + "</td>";
            $('#ajax_book_list').append(html);


         })
        })
      })
    }
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
            <p class="index"><?php echo $result["name"]; ?>さんの本棚</p>
            <p class="index_right"><strong>本の追加</strong></p>
          </div>
          <!-- 入力フォーム -->
          <form class="clearfix">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <p><strong>本のタイトル</strong></p>
              <input class="search name" type="text" name="search_name" placeholder="本のタイトル">
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <p><strong>著者</strong></p>
              <input class="search author" type="text" name="search_author" placeholder="著者">
            </div>
          </form><!-- 入力フォーム -->
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>本</th>
                  <th>著者名</th>
                  <th>出版社</th>
                </tr>
              </thead>
              <tbody id="ajax_book_list">
                <tr>
                </tr>
              </tbody>
            </table>

          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 text-center">
            <a href="book_create.php">該当する本がない場合はこちら</a>
          </div>


        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
  </main>

  <?php require('users_temp/_footer.php'); ?>
</body>
</html>
