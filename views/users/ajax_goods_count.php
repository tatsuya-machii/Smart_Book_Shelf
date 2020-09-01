<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/good.php");

try{
  // DB接続
  $good = new Good($host, $dbname, $user, $pass);
  $good->connectDb();

    // $_POST["review_id"]=review_idのいいねを全件取得
    $goods = $good->goodCount($_POST["review_id"]);


  $result = count($goods);

  header("Content-Type: application/json; charset=utf-8");
  // htmlへ渡す配列$resultをjsonに変換する
  echo json_encode($result);

}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
};


 ?>
