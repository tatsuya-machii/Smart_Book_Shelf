<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Review.php");

try{
  // DB接続
  $review = new Review($host, $dbname, $user, $pass);
  $review->connectDb();

  // 本のデータ取得
  $result = $review->adminFindAll();


      // 必要な変数の定義
      $searchTextUser = $_GET["search_user"];
      $searchTextBook = $_GET["search_book"];
      $targetText = "";
      $searchResult = [];

      foreach ($result as $review) {
        $add_flag=0;
        if (!empty($searchTextUser)) {
          if (strpos($review["user_id"], $searchTextUser) !== false) {
            $add_flag ++;
          };
        }else{
          $add_flag ++;
        }
        if (!empty($searchTextBook)) {
          if (strpos($review["book_id"], $searchTextBook) !== false) {
            $add_flag ++;
          };
        }else{
          $add_flag ++;
        }
        if ($add_flag == 2) {
          $searchResult[] = array_merge($review);
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
