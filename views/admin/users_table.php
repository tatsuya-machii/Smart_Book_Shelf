<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/User.php");
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
    $user = new User($host, $dbname, $user, $pass);
    $user->connectDB();

    // アカウント削除
    if (!empty($_GET["delete"])) {
      if ($_SESSION["user"]["id"]!= $_GET["delete"]) {
        $user->adminDelete($_GET["delete"]);
      }else{
        // 管理者は自身のアカウントを削除できない
        $confirm="管理者は自身のアカウントを削除することはできません。";
        $alert = "<script type='text/javascript'>alert('".$confirm. "');</script>";
        echo $alert;
      }
    }

    //ユーザー情報取得
    $result = $user->adminFindAll();

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
    $('.delete').on('click', function(){
      alert("test");
      if (!confirm('本当に削除しますか？')) return false;
    })

    // インプット時の動き（ajaｘ）
    $('input.search').on('input', function(){
      // 入力するとソートボタンのクリック回数はリセットされる。
      $('.sort').removeClass('desc');

      $.ajax({
        url: "ajax_user_search.php",
        type:"GET",
        data:{search_name: $("input.name").val()}
      })
      .done(function(data){
        ajax_table(data);
      })
    })

    // ソートボタンクリック時の機能(DESCクラスがないときはASCでソートしてDESCクラス追加、ある時はDESCでソートしてDESCクラス消去)
    $('.sort').on("click", function(){
      if ($(this).hasClass("desc")) {
        $('.sort').removeClass('desc');

        $.ajax({
          type: "GET",
          url: "ajax_user_search.php",
          data: {
            search_name: $("input.name").val(),
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
          url: "ajax_user_search.php",
          data: {
            search_name: $("input.name").val(),
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
      $('#ajax_user_list').empty();

      $.each(data, function(arr, key){
        var confirm = "if (!confirm('本当に削除しますか？')) return false";
        var html ="<tr>";
        var html = html + "<td><p>" + key['id'] + "</p></td>";
        var html = html + "<td><p>" + key['name'] + "</p></td>";
        var html = html + "<td><p>" + key['mail'] + "</p></td>";
        var html = html + "<td><p class='image_block'>" + key['image'] + "</td>;"
        var html = html + "<td><p>" + key['role'] + "</p></td>";
        var html = html + "<td><p>" + key['status'] + "</p></td>";
        var html = html + "<td><p><a href='../users/main.php?user_id=" + key['id'] + "'>メインページ</a></p></td>";
        var html = html + "<td><p><a class='btn btn-default' href='user_edit.php?user_id=" + key['id'] + "'>編集</a></p></td>";
        var html = html + "<td><p><a class='btn btn-warning delete' href='?delete=" + key['id'] + "' onClick='" + confirm + "'>削除</a></p></td></tr>";
        $('#ajax_user_list').append(html);
      })
    }

  })

  </script>
</head>
<body>
  <?php require('admins_temp/_header.php'); ?>

  <main>
    <!-- HOME SECTION -->
    <section id="home">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index">管理者専用ページ</p>
            <p class="index_right"><strong>users_table</strong></p>
            <p><a class="mb20" href="main.php">戻る</a></p>
          </div>
          <!-- 入力フォーム -->
          <form class="clearfix">
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
              <p><strong>ユーザーネーム</strong></p>
              <input class="search name mb20" type="text" name="search_name" placeholder="ユーザーネーム">
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
              <a href="user_edit.php">ユーザーを追加する</a>
            </div>

          </form><!-- 入力フォーム -->

          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <!--  ソートボタンクリック時の機能(DESCクラスがないときはASCでソートしてDESCクラス追加、ある時はDESCでソートしてDESCクラス消去) -->
                  <td><a class="sort" id="id" href="">id</a></td>
                  <td><a class="sort" id="name" href="">ユーザーネーム</a></td>
                  <td><a class="sort" id="mail" href="">メールアドレス</a></td>
                  <td>イメージ画像</td>
                  <td><a class="sort asc" id="role" href="">権限</a></td>
                  <td><a class="sort asc" id="status" href="">状態</br>1:アクティブ</br>2:退会済み</a></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              </thead>
              <tbody id="ajax_user_list">
                  <?php foreach ($result as $user): ?>
                    <tr>
                      <td><p><?php echo $user["id"] ?></p></td>
                      <td><p><?php echo $user["name"] ?></p></td>
                      <td><p><?php echo $user["mail"] ?></p></td>
                      <td>
                        <?php if (!empty($user["image"])){ ?>
                          <p class="image_block"><img class="small_icon" src="../../img/users/<?php echo $user["image"] ?>" alt="ユーザーアイコン"></p>
                        <?php }else{ ?>
                          <p class="image_block"><i class="fas fa-user main_icon"></i></p><!-- $user_image -->
                        <?php } ?>
                      </td>
                      <td><p><?php echo $user["role"] ?></p></td>
                      <td><p><?php echo $user["status"] ?></p></td>
                      <td><p><a href="../users/main.php?user_id=<?php echo $user["id"] ?>">メインページ</a></p></td>
                      <td><p><a class="btn btn-default" href="user_edit.php?user_id=<?php echo $user["id"] ?>">編集</a></p></td>
                      <?php $confirm = "if (!confirm('本当に削除しますか？')) return false"; ?>
                      <td><p><?php if ($_SESSION["user"]["id"]!= $user["id"]) echo '<a class="btn btn-warning" href="?delete='.$user["id"].'" onClick="'.$confirm.'" >削除</a>' ?></p></tr>
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
