<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Friend.php");

try{
  // DB接続
  $friend = new Friend($host, $dbname, $user, $pass);
  $friend->connectDb();

  if (!empty($_POST["user_id"])) {
    // $_POST["user_id"]=user_idのフレンドを全件取得
    $friends = $friend->friendCount($_POST["user_id"]);
  }else{
    // $_SESSION["user"]["id"]=user_idのフレンドを全件取得
    $friends = $friend->friendCount($_SESSION["user"]["id"]);
  };


  $result = count($friends);

  header("Content-Type: application/json; charset=utf-8");
  // htmlへ渡す配列$resultをjsonに変換する
  echo json_encode($result);

}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
};


 ?>
