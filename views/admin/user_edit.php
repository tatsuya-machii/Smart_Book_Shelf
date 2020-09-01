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

    if (!empty($_POST)) {
      $message = $user->validate($_POST);
      if (!isset($message["name"]) && !isset($message["mail"]) && !isset($message["password"])) {
        if (!empty($_POST["id"])) {
          $user->adminEdit($_POST);
          header("location: /curriculum/Smart_Book_Shelf/views/admin/users_table.php");
          exit;
        }else{
          $user->adminCreate($_POST);
          header("location: /curriculum/Smart_Book_Shelf/views/admin/users_table.php");
          exit;
        }
      }
    }

    //編集ユーザー情報取得
    if (isset($_GET["user_id"])) {
      $result = $user->findById($_GET["user_id"]);
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
    <section id="admin_user_edit">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index">管理者専用ページ</p>
            <p class="index_right"><strong>user_edit</strong></p>
            <p><a class="mb20" href="users_table.php">戻る</a></p>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <thead>
                <tr>
                  <td>id</td>
                  <td>ユーザーネーム</td>
                  <td>メールアドレス</td>
                  <td>イメージ画像</td>
                  <td>権限</td>
                  <td>状態</br>1:アクティブ</br>2:退会済み</td>
                  <td></td>
                </tr>
              </thead>
              <tbody>
                    <tr>
                      <form action="" method="post" enctype="multipart/form-data">
                        <td><p><?php if(isset($_GET["user_id"]))echo $result["id"] ?></p></td>
                        <input type="hidden" name="id" value="<?php if(isset($_GET["user_id"])) echo $result["id"] ?>">
                        <td><input type="text" name="name" value="<?php  if(isset($_GET["user_id"]))echo $result["name"] ?>"></td>
                        <td><input type="text" name="mail" value="<?php  if(isset($_GET["user_id"]))echo $result["mail"] ?>"></td>
                        <input type="hidden" name="password" value="<?php  if(isset($_GET["user_id"]))echo $result["password"] ?>">
                        <td>
                          <p class="radio"><input type="radio" name="role" value="0" <?php  if(isset($_GET["user_id"])){if ($result["role"]==0) echo 'checked="checked"';}?>>0（一般）</br><input type="radio" name="role" value="1" <?php  if(isset($_GET["user_id"])){if ($result["role"]==1) echo 'checked="checked"';}?>>1(管理者)</p>
                        </td>
                        <td>
                          <p class="radio"><input type="radio" name="status" value="0" <?php  if(isset($_GET["user_id"])){if ($result["status"]==0) echo 'checked="checked"';}?>>0（アクティブ）</br><input type="radio" name="status" value="1 <?php  if(isset($_GET["user_id"])){if ($result["status"]==1) echo 'checked="checked"';}?>">1(退会済み)</p>
                        </td>
                        <td><input type="submit" class="btn btn-warning" value="登録"></td>
                      </form>
                    </tr>

              </tbody>
            </table>
            <p>※新規登録の場合、画像は登録されません。</p>
            <?php  if (isset($message["name"]))echo $message["name"]."</br>"; ?>
            <?php  if (isset($message["mail"]))echo $message["mail"]."</br>"; ?>
            <?php  if (isset($message["password"]))echo $message["password"]; ?>

          </div>


        </div><!-- row -->
      </div><!-- user_inform -->
    </section>
  </main>

  <?php require('admins_temp/_footer.php'); ?>
</body>
</html>
