<?php
require_once("DB.php");

class Review extends DB {



  // 参照 (review of session user(メインページ))
  public function findByUser($arr){
    $sql = "SELECT r.id AS review_id, r.recommends, r.created_at, b.id AS book_id, b.name AS book_name, b.image AS book_image FROM reviews r JOIN books b ON r.book_id = b.id WHERE r.user_id = :id ORDER BY r.id DESC LIMIT :start, 8";
    $stmt = $this->connect->prepare($sql);
    $stmt->bindValue(":start", (int)$arr["start"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $arr["id"]);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // メインページ：ページネーション　
  public function pageCount($id){
    $sql = "SELECT COUNT(*) FROM reviews WHERE user_id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt-> execute($params);
    $page_num = $stmt->fetchColumn();
    // ページ数の取得
    $pagination = ceil($page_num / 8);
    return $pagination;

  }
  // 参照 (review of the Book(詳細・編集・削除確認ページ))
  public function findByBook($id){
    $sql = "SELECT r.id, r.recommends, r.description, r.impression, r.created_at, b.id AS book_id, b.name AS book_name, b.author, b.publisher, b.image AS book_image, u.name AS user_name, u.id AS user_id FROM reviews r JOIN books b ON r.book_id = b.id JOIN users u ON r.user_id = u.id WHERE r.id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }
  // 参照 (全件(投稿一覧ページ))
  public function findAll(){
    $sql = "SELECT r.id, r.recommends, r.created_at, b.id AS book_id ,b.name AS book_name, b.image AS book_image, u.id AS user_id, u.name AS user_name, u.image AS user_image FROM reviews r JOIN books b ON r.book_id = b.id JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC";
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // 新規登録
  public function create($arr){
    $sql = "INSERT INTO reviews (user_id, book_id, recommends, description, impression) VALUES (:user_id, :book_id, :recommends, :description, :impression)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":book_id" => $arr["book_id"],
      ":recommends" => $arr["recommends"],
      ":description" => $arr["description"],
      ":impression" => $arr["impression"],
    );
    $result = $stmt->execute($params);
    return $result;
  }
  // 更新
  public function edit($arr){
    $sql = "UPDATE reviews SET recommends = :recommends, description = :description, impression = :impression, updated_at = :updated_at WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":recommends" => $arr["recommends"],
      ":description" => $arr["description"],
      ":impression" => $arr["impression"],
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    $stmt->execute($params);
    $result= $stmt->fetch();
    return $result;
  }
  // 削除
  public function delete($id){
    $sql = "DELETE FROM reviews WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
  }

  // admin_command------------------------------------------

  // 参照
  public function adminFindAll(){
    $sql = "SELECT * FROM reviews";
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // 参照
  public function adminFindBy($id){
    $sql = "SELECT * FROM reviews WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }
  // 更新
  public function adminEdit($arr){
    $sql = "UPDATE reviews SET user_id=:user_id, book_id=:book_id, recommends=:recommends, description=:description, impression=:impression, updated_at=:updated_at WHERE id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":book_id" => $arr["book_id"],
      ":recommends" => $arr["recommends"],
      ":description" => $arr["description"],
      ":impression" => $arr["impression"],
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    $stmt->execute($params);
    return $result;
  }

  // validation------------------------------------------
  public function validate($arr){
    if (empty($arr["recommends"])) {
      $message["recommends"] = "おすすめ度を1から5までで入力してください。";
    }
    if (strlen($arr["description"]) > 500) {
      $message["description"] = "概要は500文字以内で入力してください。";
    }
    if (strlen($arr["impression"]) > 500) {
      $message["impression"] = "感想は500文字以内で入力してください。";
    }
    return $message;
  }







}
