<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Friend.php");

if (!empty($_POST["user_id"])) {

  try{
    // DB接続
    $friend = new Friend($host, $dbname, $user, $pass);
    $friend->connectDb();

      // 友だちか確認
      $confirm = $friend->friendConfirm(array("user_id"=>$_SESSION["user"]["id"], "friends_id"=>$_POST["user_id"]));
      if ($confirm) {
        $result = '<a class="ajax_friend_add" href="">友だち登録を解除する</a>';
      }else{
        $result = '<a class="ajax_friend_add" href="">友だちに追加する</a>';
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
