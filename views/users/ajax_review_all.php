<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/review.php");

try{
  // DB接続
  $review = new Review($host, $dbname, $user, $pass);
  $review->connectDb();

  // 本のデータ取得
  $result = $review->findAll();


      // 必要な変数の定義
      $searchTextUserName = $_GET["search_user_name"];
      $searchTextBookName = $_GET["search_book_name"];
      $targetText = "";
      $searchResult = [];

        foreach ($result as $review) {
          $add_flag=0;
          if (!empty($searchTextUserName)) {
            if (strpos($review["user_name"], $searchTextUserName) !== false) {
              $add_flag ++;
            };
          }else{
            $add_flag ++;
          }
          if (!empty($searchTextBookName)) {
            if (strpos($review["book_name"], $searchTextBookName) !== false) {
              $add_flag ++;
            };
          }else{
            $add_flag ++;
          }




          if ($add_flag == 2) {
            // 画像があれば画像、なければアイコン
            // user_icon
            if (file_exists("../../img/users/".$review["user_id"].".jpg")){
              $review["user_image"] = '<img class="small_icon" src="../../img/users/'.$review["user_id"].'.jpg" alt="ユーザーアイコン">';
            }else{
              $review["user_image"] = '<i class="fas fa-user small_icon"></i>';
            };
            // book_icon
            if (file_exists("../../img/books/".$review["book_id"].".jpg")){
              $review["book_image"] = '<img class="small_icon" src="../../img/books/'.$review["book_id"].'.jpg" alt="ユーザーアイコン">';
            }else{
              $review["book_image"] = '<i class="fas fa-book-open small_icon"></i>';
            };
            // おすすめ度の星表記
            $on = null;
            $off = null;
            for ($j=0; $j <= $review["recommends"]; $j++){
              $on .= '<img src="../../img/base/star-on.png">';
            };
            if ($review["recommends"] < 5) {
              for ($j=$review["recommends"]; $j < 5; $j++){
                $off .= '<img src="../../img/base/star-off.png">';
              };
            };
            $review["recommends"] = $on.$off;

            $searchResult[] = array_merge($review);
          }
        }





      header("Content-Type: application/json; charset=utf-8");
      // htmlへ渡す配列$resultをjsonに変換する
      echo json_encode($searchResult);

}catch(PDOException $e){
  echo "erroe";

  print "エラー！". $e->getMessage()."</br>";
};


 ?>
