<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/User.php");

try{
  // DB接続
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDb();


    // 本のデータ取得
    $result = $user->adminFindAll();

  // 必要な変数の定義
  $searchTextName = $_GET["search_name"];
  $targetText = "";
  $searchResult = [];

  foreach($result as $user){
    $add_flag =0;
    if(!empty($searchTextName)){
      if (strpos($user["name"], $searchTextName) !== false) {
        $add_flag ++;
      }
    }else{
      $add_flag ++;
    };
    if ($add_flag == 1) {
      if (!empty($user["image"])){
        $user["image"] = '<img src="../../img/users/'.$user["image"].'" alt="ユーザーアイコン">';
      }else{
        $user["image"] = '<i class="fas fa-user main_icon"></i>';
      };

      $searchResult[] = array_merge($user);
    };
  }


  // ソートボタンクリック時の動作
  if (isset($_GET["column_asc"])) {
    $sort_colmn = $_GET['column_asc'];
    foreach ($searchResult as $key => $value) {
      $sortkey[] = $value[$sort_colmn];
    }
    array_multisort($sortkey, SORT_ASC, $searchResult);
  }
  if (isset($_GET["column_desc"])) {
    $sort_colmn = $_GET['column_desc'];
    foreach ($searchResult as $key => $value) {
      $sortkey[] = $value[$sort_colmn];
    }
    array_multisort($sortkey, SORT_DESC, $searchResult);
  }

  header("Content-Type: application/json; charset=utf-8");
  // htmlへ渡す配列$resultをjsonに変換する
  echo json_encode($searchResult);


}catch(PDOException $e){
  echo "erroe";
  print "エラー！". $e->getMessage()."</br>";
};
