<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Good.php");

try{
  // DB接続
  $good = new Good($host, $dbname, $user, $pass);
  $good->connectDb();

  if (isset($_POST["create_good_id"])) {
    $test= (array("review_id" => $_POST["create_good_id"], "user_id" => $_SESSION["user"]["id"]));
    $good->create($test);
    $goods = $good->findBy($_POST["create_good_id"]);

  }elseif (isset($_POST["delete_good_id"])) {
    $test= (array("review_id" => $_POST["delete_good_id"], "user_id" => $_SESSION["user"]["id"]));
    $good->delete($test);
    $goods = $good->findBy($_POST["delete_good_id"]);
  }

  $result = count($goods);


  header("Content-Type: application/json; charset=utf-8");
  // htmlへ渡す配列$resultをjsonに変換する
  echo json_encode($result);

}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
};


 ?>
