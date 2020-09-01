<?php
require_once("DB.php");

class Book extends DB {


  // 参照　(条件なし全件)
  public function findAll(){
    $sql = "SELECT * FROM books";
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  // 参照 (条件あり)
  public function findBy($id){
    $sql = "SELECT * FROM books WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }

  // 新規登録
  public function create($arr){
    $sql = "INSERT INTO books (name, author, publisher, created_user_id) VALUES (:name, :author, :publisher, :created_user_id)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":author" => $arr["author"],
      ":publisher" => $arr["publisher"],
      ":created_user_id" => $arr["created_user_id"]
    );
    $stmt->execute($params);
    return $this->connect->lastInsertId();
  }

  // 参照（削除確認）book_create.php
  public function deleteConfirm($arr){
    $sql = "SELECT * FROM books WHERE name=:name AND author=:author AND publisher=:publisher";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":author" => $arr["author"],
      ":publisher" => $arr["publisher"]
    );
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }




  // admin_command------------------------------------------

  // 削除
  public function adminDelete($id){
    $sql = "DELETE FROM books WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
  }
  // 更新
  public function edit($arr){
    if (!empty($_FILES['image']['name'])) {
      // 画像の保存
      //拡張子判別
      $mimetype  = mime_content_type($_FILES['image']['tmp_name']);
      $extension = array_search($mimetype, [
          'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
      ]);
      // 拡張子が判別できたら画像をアップロード
      if (false !== $extension) {
        $uploaddir ="/Applications/MAMP/htdocs/curriculum/Smart_Book_Shelf/img/books/";
        $upfile = $arr["id"].".".$extension;  //固定アップロードファイル名（拡張子自動補完）
        $upload = $uploaddir.$upfile;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload);
      }
    }

     if (isset($upfile)) {
    $sql = "UPDATE books SET  name=:name, author=:author, publisher=:publisher, image=:image, status=:status, created_user_id=:created_user_id, updated_at=:updated_at WHERE id=:id";
    }else{
      $sql = "UPDATE books SET  name=:name, author=:author, publisher=:publisher, status=:status, created_user_id=:created_user_id, updated_at=:updated_at WHERE id=:id";
    }
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":author" => $arr["author"],
      ":publisher" => $arr["publisher"],
      ":status" => $arr["status"],
      ":created_user_id" => $arr["created_user_id"],
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    if (isset($upfile)) {
      $params = array_merge($params, array(":image" => $upfile));
    };
    $stmt->execute($params);
  }
  // 登録
  public function adminCreate($arr){
    $sql = "INSERT INTO books (name, author, publisher, status, created_user_id) VALUES (:name, :author, :publisher, :status, :created_user_id)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":author" => $arr["author"],
      ":publisher" => $arr["publisher"],
      ":status" => $arr["status"],
      ":created_user_id" => $arr["created_user_id"]
    );
    $stmt->execute($params);
    $result = $this->connect->lastInsertId();
    // 画像の保存
    //拡張子判別
    $mimetype  = mime_content_type($_FILES['image']['tmp_name']);
    $extension = array_search($mimetype, [
        'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
    ]);
    // 拡張子が判別できたら画像をアップロード
    if (false !== $extension) {
      $uploaddir ="/Applications/MAMP/htdocs/curriculum/Smart_Book_Shelf/img/books/";
      $upfile = $result.".".$extension;  //固定アップロードファイル名（拡張子自動補完）
      $upload = $uploaddir.$upfile;
      move_uploaded_file($_FILES['image']['tmp_name'], $upload);
    };


    $sql = "UPDATE books SET image=:image WHERE id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":image" => $upfile,
      ":id" => $result
    );
    $stmt->execute($params);
    return $result;
  }

  // validation------------------------------------------
  // name:２０文字以内
  // author:２０文字以内
  // publisher:２０文字以内
  // image:画像ファイル・ｊｐｇのみ
  public function validate($arr){
    $message= array();

    if (empty($arr["name"])) {
      $message["name"] = "本の名前を入力してください。";
    }elseif (strlen($arr["name"]) > 20) {
      $message["name"] = "本の名前は20字以内で入力してください。";
    }
    if (empty($arr["author"])) {
      $message["author"] = "著者の名前を入力してください。";
    }elseif (strlen($arr["author"]) > 20) {
      $message["author"] = "著者の名前は20字以内で入力してください。";
    }
    if (empty($arr["publisher"])) {
      $message["publisher"] = "出版社の名前を入力してください。";
    }elseif (strlen($arr["publisher"]) > 20) {
      $message["publisher"] = "出版社の名前は20字以内で入力してください。";
    }
    // 画像のファイル形式チェック
    // inputタグで「accept=".jpg"」とすればOK
    return $message;
  }




}
