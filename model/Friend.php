<?php
require_once("DB.php");

class Friend extends DB{

  // 追加
  public function create($id){
    $sql = "INSERT INTO friends (user_id, friends_id) VALUES (:user_id, :friends_id)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $_SESSION["user"]["id"],
      ":friends_id" => $id
    );
    $stmt->execute($params);
  }

  // 参照
  public function findBy($id){
    $sql = "SELECT f.id, u.id AS user_id, f.friends_id AS friend_id, u.name AS user_name, u.image AS user_image FROM friends f JOIN users u ON f.friends_id = u.id WHERE user_id = :user_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":user_id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
  }
  // 参照（ajax_friend_count）_表示されているユーザーの友だち人数確認
  public function friendCount($id){
    $sql = "SELECT id FROM friends WHERE user_id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
  }


  //確認 (友だちかどうかで表示を変える)ajax_friend_btn
  public function friendConfirm($arr){
    $sql = "SELECT * FROM friends WHERE user_id = :user_id AND friends_id = :friends_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":friends_id" => $arr["friends_id"]
    );
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }
  // 削除
  public function delete($id){
    $sql = "DELETE FROM friends WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
  }

  // 削除（ajax_friend_add.php）非同期によるデリート
  public function ajaxDelete($id){
    $sql = "DELETE FROM friends WHERE user_id = :user_id AND friends_id = :friends_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $_SESSION["user"]["id"],
      ":friends_id" => $id
    );
    $stmt->execute($params);
  }
  // admin_command------------------------------------------
  // 参照（全件）
  public function adminFindAll(){
    $sql = "SELECT f.id, f.user_id, f.friends_id, me.name AS user_name, op.name AS friend_name FROM friends f JOIN users me ON f.user_id=me.id JOIN users op ON f.friends_id=op.id";
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // 参照（１件）
  public function adminFindBy($id){
    $sql = "SELECT * FROM friends WHERE id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }
  // 更新
  public function adminEdit($arr){
    $sql = "UPDATE friends SET user_id = :user_id, friends_id= :friends_id, updated_at = :updated_at WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":user_id" => $arr["user_id"],
      ":friends_id" => $arr["friends_id"],
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    $stmt->execute($params);
  }







}
