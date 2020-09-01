<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Friend.php");

try{
  // DB接続
  $friend = new Friend($host, $dbname, $user, $pass);
  $friend->connectDb();

  // 友だち解除ボタンクリックの場合（$_POST["delete_friend_id"]が送られてきた場合）
  if (isset($_POST["delete_friend_id"])) {
    $friend->ajaxDelete($_POST["delete_friend_id"]);
    $result = '<a class="ajax_friend_add" href="" value="add">友だちに追加する</a>';

    // 友だち追加ボタンクリックの場合（$_POST["create_friend_id"]が送られてきた場合）
  }elseif (isset($_POST["create_friend_id"])) {
    $friend->create($_POST["create_friend_id"]);
    $result = '<a class="ajax_friend_add" href="" value="delete">友だち登録を解除する</a>';
  };

  header("Content-Type: application/json; charset=utf-8");
  // htmlへ渡す配列$resultをjsonに変換する
  echo json_encode($result);

}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
};


 ?>
