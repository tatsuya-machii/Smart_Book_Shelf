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

    // 本の削除
    if (!empty($_GET["delete"])) {
      $book->adminDelete($_GET["delete"]);
    }


    //本の情報取得
    $result = $book->FindAll();

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
          type: "get",
          url: "ajax_book_search.php",
          data:{
            search_name: $('input.name').val(),
            search_author: $('input.author').val(),
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
            url: "ajax_book_search.php",
            data: {
              search_name: $('input.name').val(),
              search_author: $('input.author').val(),
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
            url: "ajax_book_search.php",
            data: {
              search_name: $('input.name').val(),
              search_author: $('input.author').val(),
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
        $('#ajax_book_list').empty();

        $.each(data, function(arr, key){
          var html ="<tr>";
          var html = html + "<td><p>" + key['id'] + "</p></td>";
          var html = html + "<td><p>" + key['name'] + "</p></td>";
          var html = html + "<td><p>" + key['author'] + "</p></td>";
          var html = html + "<td><p>" + key['publisher'] + "</p></td>";
          var html = html + "<td><p class='image_block'>" + key['image'] + "</p></td>";
          var html = html + "<td><p>" + key['status'] + "</p></td>";
          var html = html + "<td><p>" + key['created_user_id'] + "</p></td>";
          var html = html + "<td><p><a class='btn btn-default' href='book_edit.php?book_id=" + key['id'] + "'>編集</a></p></td>";
          var html = html + "<td><p><a class='btn btn-warning' href='?delete=" + key['id'] + "'>削除</a></p></td></tr>";
          $('#ajax_book_list').append(html);
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
            <p class="index_right"><strong>books_table</strong></p>
            <p><a class="mb20" href="main.php">戻る</a></p>
          </div>
          <!-- 入力フォーム -->
          <form class="clearfix">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
              <p><strong>本のタイトル</strong></p>
              <input class="search name mb20" type="text" name="search_name" placeholder="本のタイトル">
            </div>
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
              <p><strong>著者</strong></p>
              <input class="search author mb20" type="text" name="search_author" placeholder="著者">
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
              <a href="book_edit.php">本を追加する</a>
            </div>

          </form><!-- 入力フォーム -->

          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <td><a class="sort" id="id" href="">id</a></td>
                  <td><a class="sort" id="name" href="">本のタイトル</a></td>
                  <td><a class="sort" id="author" href="">著者</a></td>
                  <td><a class="sort" id="publisher" href="">出版社</a></td>
                  <td>イメージ画像</td>
                  <td><a class="sort" id="status" href="">状態</a></br>1:アクティブ</br>2:削除済み</td>
                  <td><a class="sort" id="created_user_id" href="">登録ユーザーID</a></td>
                  <td></td>
                  <td></td>
                </tr>
              </thead>
              <tbody id=ajax_book_list>
                  <?php foreach ($result as $book): ?>
                    <tr>
                      <td><p><?php echo $book["id"] ?></p></td>
                      <td><p><?php echo $book["name"] ?></p></td>
                      <td><p><?php echo $book["author"] ?></p></td>
                      <td><p><?php echo $book["publisher"] ?></p></td>
                      <td>
                        <p class="image_block">
                          <?php if (!empty($book["image"])){ ?>
                            <img class="small_icon" src="../../img/books/<?php echo $book["image"] ?>" alt="ユーザーアイコン">
                          <?php }else{ ?>
                            <i class="fas fa-book-open small_icon"></i>'
                          <?php } ?>
                        </p>
                      </td>
                      <td><p><?php echo $book["status"] ?></p></td>
                      <td><p><?php echo $book["created_user_id"] ?></p></td>
                      <td><p><a class="btn btn-default" href="book_edit.php?book_id=<?php echo $book["id"] ?>">編集</a></p></td>
                      <?php $confirm = "if (!confirm('本当に削除しますか？')) return false"; ?>
                      <td><p><?php echo '<a class="btn btn-warning" href="?delete='.$book["id"].'" onClick="'.$confirm.'" >削除</a>' ?></p></td>
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
