<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Good.php");
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
    $good = new Good($host, $dbname, $user, $pass);
    $good->connectDB();

    // いいねの削除
    if (!empty($_GET["delete"])) {
      $good->adminDelete($_GET["delete"]);
    }


    //いいねの情報取得
    $result = $good->adminFindAll();

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
  <script>
    $(function(){

      // インプット時の動き（ajaｘ）
      $('input.search').on("input",function(){
        // 入力するとソートボタンのクリック回数はリセットされる。
        $('.sort').removeClass('desc');

        $.ajax({
          type: "GET",
          url: "ajax_good_search.php",
          data:{
            search_user: $('input.user').val(),
            search_review: $('input.review').val()
          }
        })
        .done(function(data){
          ajax_table(data)
        })
      })

      // ソートボタンクリック時の機能(DESCクラスがないときはASCでソートしてDESCクラス追加、ある時はDESCでソートしてDESCクラス消去)
      $('.sort').on("click", function(){
        if ($(this).hasClass("desc")) {
          $('.sort').removeClass('desc');

          $.ajax({
            type: "GET",
            url: "ajax_good_search.php",
            data: {
              search_user: $('input.user').val(),
              search_review: $('input.review').val(),
              column_desc: $(this).attr("id")
            }
          })
          .done(function(data){
           ajax_table(data);
          })

        }else{
          // 2回目クリック時は降順にソート
          $('.sort').removeClass('desc');
          $(this).addClass("desc");
          $.ajax({
            type: "GET",
            url: "ajax_good_search.php",
            data: {
              search_user: $('input.user').val(),
              search_review: $('input.review').val(),
              column_asc: $(this).attr("id")
            }
          })
          .done(function(data){
            ajax_table(data);
          })
        }
        return false;
      })


      // ajax実行後のHTML
      function ajax_table(data){
        $('#ajax_good_list').empty();

        $.each(data, function(arr, key){
          var html ="<tr>";
          var html = html + "<td><p>" + key['id'] + "</p></td>";
          var html = html + "<td><p>" + key['user_id'] + " (" + key['user_name'] + ")</p></td>";
          var html = html + "<td><p>" + key['review_id'] + "</p></td>";
          var html = html + "<td><p><a class='btn btn-default' href='good_edit.php?good_id=" + key['id'] + "'>編集</a></p></td>";
          var html = html + "<td><p><a class='btn btn-warning' href='?delete=" + key['id'] + "'>削除</a></p></td></tr>";
          $('#ajax_good_list').append(html);
        })
      }

    })
  </script>
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
            <p class="index_right"><strong>goods_table</strong></p>
            <p class="mb20"><a href="main.php">戻る</a></p>
          </div>
          <!-- 入力フォーム -->
          <form class="clearfix">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <p><strong>ユーザーID</strong></p>
              <input class="search user mb20" type="text" name="search_user" placeholder="ユーザーID">
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <p><strong>投稿ID</strong></p>
              <input class="search review mb20" type="text" name="search_review" placeholder="投稿ID">
            </div>

          </form><!-- 入力フォーム -->

          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <td><a class="sort" id="id" href="">id</a></td>
                  <td><a class="sort" id="user_id" href="">ユーザーID</a></td>
                  <td><a class="sort" id="review_id" href="">投稿ID</a></td>
                  <td></td>
                  <td></td>
                </tr>
              </thead>
              <tbody id=ajax_good_list>
                  <?php foreach ($result as $good): ?>
                    <tr>
                      <td><p><?php echo $good["id"] ?></p></td>
                      <td><p><?php echo $good["user_id"] ?> (<?php echo $good["user_name"] ?>)</p></td>
                      <td><p><?php echo $good["review_id"] ?></p></td>
                      <td><p><a class="btn btn-default" href="good_edit.php?good_id=<?php echo $good["id"] ?>">編集</a></p></td>
                      <?php $confirm = "if (!confirm('本当に削除しますか？')) return false"; ?>
                      <td><p><?php echo '<a class="btn btn-warning" href="?delete='.$good["id"].'" onClick="'.$confirm.'" >削除</a>' ?></p></td>
                    </tr>
                  <?php endforeach; ?>

              </tbody>
            </table>

          </div>


        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
  </main>

  <?php require('admins_temp/_footer.php'); ?>
</body>
</html>
