<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/good.php");

try{
  // DB接続
  $good = new Good($host, $dbname, $user, $pass);
  $good->connectDb();

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

  // いいねしたユーザーのデータ取得
    $result = $good->findByReview($_GET["review_id"]);
    // レビューに「いいね」がついていない場合、レビューを書いたユーザーの情報を取得する。
    if (empty($result)) {
      $result = $good->findUser($_GET["review_id"]);
    }

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
</head>
<body>
  <?php require('users_temp/_header.php'); ?>

  <main>

    <!-- HOME SECTION -->
    <section id="review_book_search">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="index"><?php echo $result[0]["account_name"]; ?>さんの本棚</p>
            <p class="index_right"><strong>いいね！したユーザー一覧</strong></p>
            <?php if (!empty($_GET["user_id"])): ?>
              <p><a href="review_show.php?user_id=<?php echo $_GET["user_id"]; ?>&review_id=<?php echo $_GET["review_id"]; ?>">本の詳細に戻る</a></p>
            <?php endif; ?>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-hover">
              <tbody id="ajax_review_list">
                <?php if (isset($result[0]["user_id"])){ ?>
                  <?php foreach ($result as $friend): ?>
                    <tr>
                      <td>
                        <p>
                          <?php if (file_exists("../../img/users/".$friend["user_id"].".jpg")){ ?>
                            <img  class="small_icon" src="../../img/users/<?php echo $friend["user_id"]; ?>.jpg" alt="ユーザーアイコン"><!-- $user_image -->
                          <?php }else{ ?>
                            <i class="fas fa-user small_icon"></i>
                          <?php } ?>
                        </p>
                        <p>
                          <a href='main.php?user_id=<?php echo $friend["user_id"]; ?>'>
                            <?php echo $friend["user_name"]; ?>
                          </a>                          
                        </p>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php }else{ ?>
                  <p>いいねしたユーザーがいません。</p>
                <?php } ?>
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
