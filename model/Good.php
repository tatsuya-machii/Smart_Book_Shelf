<?php
require_once("DB.php");

class Good extends DB{

  // 追加(ajax_good_add)
  public function create($arr){
    $sql = "INSERT INTO goods (user_id, review_id) VALUES (:user_id, :review_id)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":review_id" => $arr["review_id"]
    );
    $stmt->execute($params);
  }
  // 削除（ajax_good_add）
  public function delete($arr){
    $sql = "DELETE FROM goods WHERE review_id=:review_id AND user_id=:user_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":review_id" => $arr["review_id"],
      ":user_id" => $arr["user_id"]
    );
    $stmt->execute($params);
  }
  // 参照（１件　user_id, review_idによる）　ajaｘ_good_btn
  public function goodConfirm($arr){
    $sql = "SELECT id FROM goods WHERE user_id=:user_id AND review_id=:review_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":review_id" => $arr["review_id"]
    );
    $stmt->execute($params);
    return $stmt->fetch();
  }

  // 参照(ajaxカウント)※goodCount($id)と同じ！
  public function findBy($id){
    $sql = "SELECT id FROM goods WHERE review_id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
  }
  // 参照(いいね一覧)
  public function findByReview($id){
    $sql = "SELECT g.id, u.id AS user_id, u.name AS user_name, u.image AS user_image, me.name AS account_name FROM goods g JOIN users u ON g.user_id = u.id JOIN reviews r ON g.review_id = r.id JOIN users me ON r.user_id = me.id WHERE review_id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
  }
  // 参照（いいねカウント）ajax_goods_count
  public function goodCount($id){
    $sql = "SELECT id FROM goods WHERE review_id=:review_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":review_id" => $id);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }
  // 参照（good_index(レビューに「いいね」がついていない場合、レビューを書いたユーザーの情報を取得する。)）
  public function findUser($id){
    $sql = "SELECT u.name AS account_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.id = :id";
    $stmt = $this->connect->prepare($sql);
    $params= array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
  }

  // admin_command------------------------------------------

  // 参照
  public function adminFindAll(){
    $sql = "SELECT g.id, g.user_id, g.review_id, u.name AS user_name FROM goods g JOIN users u ON g.user_id=u.id";
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // 削除
  public function adminDelete($id){
    $sql = "DELETE FROM goods WHERE id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
  }

  // 更新
  public function adminEdit($arr){
    $sql = "UPDATE goods SET user_id = :user_id, review_id= :review_id, updated_at = :updated_at WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":review_id" => $arr["review_id"],
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    $stmt->execute($params);
  }
  // 参照(１件)
  public function adminFindBy($id){
    $sql = "SELECT * FROM goods WHERE id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }




}
