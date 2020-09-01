<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Good.php");

try{
  // DB接続
  $good = new Good($host, $dbname, $user, $pass);
  $good->connectDb();

  // 本のデータ取得
  $result = $good->adminFindAll();


      // 必要な変数の定義
      $searchTextUser = $_GET["search_user"];
      $searchTextReview = $_GET["search_review"];
      $targetText = "";
      $searchResult = [];

      foreach ($result as $good) {
        $add_flag=0;
        if (!empty($searchTextUser)) {
          if (strpos($good["user_id"], $searchTextUser) !== false) {
            $add_flag ++;
          };
        }else{
          $add_flag ++;
        }
        if (!empty($searchTextReview)) {
          if (strpos($good["review_id"], $searchTextReview) !== false) {
            $add_flag ++;
          };
        }else{
          $add_flag ++;
        }
        if ($add_flag == 2) {
          $searchResult[] = array_merge($good);
        }
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


 ?>
