<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Good.php");

if (!empty($_POST["review_id"])) {

  try{
    // DB接続
    $good = new Good($host, $dbname, $user, $pass);
    $good->connectDb();

      // いいね済みか確認
      $confirm = $good->goodConfirm(array("user_id"=>$_SESSION["user"]["id"], "review_id"=>$_POST["review_id"]));
      if ($confirm) {
        $result = 'already';
      }else{
        $result = 'yet';
      }
      header("Content-Type: application/json; charset=utf-8");
      // htmlへ渡す配列$resultをjsonに変換する
      echo json_encode($result);

    }catch(PDOException $e){
      echo "erroe";

      print "エラー！". $e->getMessage()."</br>";
    };
  }



 ?>
