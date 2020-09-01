<?php
session_start();
require_once("../../config/config.php");
require_once("../../model/Book.php");

try{
  // DB接続
  $book = new Book($host, $dbname, $user, $pass);
  $book->connectDb();

  // 本のデータ取得
  $result = $book->findAll();


      // 必要な変数の定義
      $searchTextName = $_GET["search_name"];
      $searchTextAuthor = $_GET["search_author"];
      $targetText = "";
      $searchResult = [];

      foreach ($result as $book) {
        $add_flag=0;
        if (!empty($searchTextName)) {
          if (strpos($book["name"], $searchTextName) !== false) {
            $add_flag ++;
          };
        }else{
          $add_flag ++;
        }
        if (!empty($searchTextAuthor)) {
          if (strpos($book["author"], $searchTextAuthor) !== false) {
            $add_flag ++;
          };
        }else{
          $add_flag ++;
        }
        if (empty($searchTextName) && empty($searchTextAuthor)) {
          $add_flag = 0;
        }
        if ($add_flag == 2) {
          if (file_exists("../../img/books/".$book["id"].".jpg")){
            $book["image"] = '<img class="small_icon" src="../../img/books/'.$book["id"].'.jpg" alt="ユーザーアイコン">';
          }else{
            $book["image"] = '<i class="fas fa-book-open small_icon"></i>';
          };

          $searchResult[] = array_merge($book);
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
