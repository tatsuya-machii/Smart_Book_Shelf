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

    // いいねの更新・登録
    if (!empty($_POST)) {
      if (empty($_POST["id"])) {
        $good->create($_POST);
        header("location: /curriculum/Smart_Book_Shelf/views/admin/goods_table.php");
        exit;
      }else{
        $good->adminEdit($_POST);
        header("location: /curriculum/Smart_Book_Shelf/views/admin/goods_table.php");
        exit;
      }
    }

    // 本の削除
    if (!empty($_GET["delete"])) {
      $good->adminDelete($_GET["delete"]);
    }


    //本の情報取得
    if (!empty($_GET["good_id"])) {
      $result = $good->adminFindBy($_GET["good_id"]);
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
            <p class="index_right"><strong>good_edit</strong></p>
            <p class="mb20"><a href="goods_table.php">戻る</a></p>

          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <td>id</td>
                  <td>ユーザーID</td>
                  <td>フレンドID</td>
                  <td></td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <form action="" method="post">
                    <td><p><?php if(!empty($result)) echo $result["id"]; ?></p></td>
                    <input type="hidden" name="id" value="<?php if(!empty($result)) echo $result["id"]; ?>">
                    <td><input type="text" name="user_id" value="<?php if(!empty($result)) echo $result["user_id"]; ?>"></td>
                    <td><input type="text" name="review_id" value="<?php if(!empty($result)) echo $result["review_id"]; ?>"></td>
                    <td><input class="btn btn-default" type="submit" value="登録"></td>
                  </form>
                </tr>
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
