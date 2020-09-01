<?php
require_once("DB.php");
class User extends DB {

  // ログイン
  public function login($arr) {
    $sql = "SELECT * FROM users WHERE mail = :mail";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":mail" => $arr["mail"],
    );
    $stmt->execute($params);
    $result = $stmt->fetch();
    if (password_verify($arr["password"], $result["password"])) {
      return $result;
    }
  }

  // twitter_ID参照
  public function findByTwitterUser($id) {
    $sql = "SELECT * FROM users WHERE twitter_id = :twitter_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":twitter_id" => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }

  // 新規登録
  public function create($arr){
    $sql = "INSERT INTO users (name, mail, password) VALUES (:name, :mail, :password)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":mail" => $arr["mail"],
      ":password" => password_hash($arr["password"], PASSWORD_DEFAULT)
    );
    $result = $stmt->execute($params);
    return $result;
  }

  // 新規登録(twitter_account)
  public function createByTwitter($arr){
    $sql = "INSERT INTO users (name, twitter_id) VALUES (:name, :twitter_id)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":twitter_id" => $arr["twitter_id"],
    );
    $result = $stmt->execute($params);
    return $result;
  }

  // 参照（パスワードを忘れた場合の処理）forget.php
  public function findByMail($mail){
    $sql = "SELECT * FROM users WHERE mail=:mail";
    $stmt = $this->connect->prepare($sql);
    $params = array(":mail" => $mail);
    $stmt->execute($params);
    $result = $stmt->fetch();
    // メール送信の条件分岐
    if (!empty($result)) {
      if ($result["status"] == 1) {
        $message["error"] = "アカウントは退会済みです。";
        $result = $message;
      }else{
        // 仮パスワード発行
        $temporary_password = substr(base_convert(md5(uniqid()), 16, 36), 0, 10);
        $sql = "UPDATE users SET temporary_password=:temporary_password WHERE id=:id";
        $stmt = $this->connect->prepare($sql);
        $params = array(
          ":temporary_password" => $temporary_password,
          ":id" => $result["id"]
        );
        $stmt->execute($params);
        // 以下でメール送信
        // 日本語対応
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        $to = $_POST["mail"];//宛先
        $title = "仮パスワードのお知らせ[Smart_Book_Shelf]";
        $message = "仮パスワードを発行しました。以下のurlにアクセスし、仮パスワードを入力して、新しいパスワードを登録してください。
                    \n仮パスワード：".$temporary_password.
                    "\n\nurl: http://localhost/curriculum/Smart_Book_Shelf/views/identification.php";
        $headers = "From: from@example.com";//?

        if(mb_send_mail($to, $title, $message, $headers)){
          $result = $result["id"];
        }else{
          $message["error"] = "アカウントの確認ができません。";
          $result = $message;
        };
      }
    }else{
      $message["error"] = "アカウントの確認ができません。";
      $result = $message;
    }
    return $result;
  }

  // 参照（仮パスワード）identification.php
  public function temporaryPassComfirm($arr){
    $sql = "SELECT * FROM users WHERE id=:id AND temporary_password=:temporary_password";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":id" => $arr["id"],
      ":temporary_password" => $arr["temporary_password"]
    );
    $result = $stmt->execute($params);
    return $result;
  }

  // 更新（パスワードを忘れた場合　パスワード変更）passwordedit.php
  public function passwordEdit($arr){
    $sql = "UPDATE users SET password = :password, updated_at = :updated_at, temporary_password=:temporary_password WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":password" => password_hash($arr["password"], PASSWORD_DEFAULT),
      ":temporary_password" => null,
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    $stmt->execute($params);
    $result= $stmt->fetch();
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

  // 参照（１件　edit）admin/user_edit.php, review_book_search.php
  public function findById($id){
    $sql = "select * FROM users WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt-> execute($params);
    $result = $stmt->fetch();
    return $result;
  }



  // 参照 (メインページ)
  public function findBy($arr){
    $sql = "SELECT u.id AS user_id, u.name AS user_name, u.image AS user_image, b.id AS book_id, b.name AS book_name, b.image AS book_image, r.id AS review_id, r.recommends, r.created_at ";
    $sql .= "FROM users u ";
    $sql .= "LEFT JOIN reviews r ON u.id = r.user_id ";
    $sql .= "LEFT JOIN books b ON r.book_id = b.id ";
    $sql .= "WHERE u.id = :id ";
    $sql .= "ORDER BY r.id DESC ";
    $sql .= "LIMIT :start, 8";
    $stmt = $this->connect->prepare($sql);
    $stmt->bindValue(":start", (int)$arr["start"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $arr["id"]);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  // 更新
  public function edit($arr){
    if (isset($srr["image"])) {
      // 画像の保存
      //拡張子判別
      $mimetype  = mime_content_type($_FILES['image']['tmp_name']);
      $extension = array_search($mimetype, [
          'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
      ]);
      // 拡張子が判別できたら画像をアップロード
      if (false !== $extension) {
        $uploaddir ="/Applications/MAMP/htdocs/curriculum/Smart_Book_Shelf/img/users/";
        $upfile = $arr["id"].".".$extension;  //固定アップロードファイル名（拡張子自動補完）
        $upload = $uploaddir.$upfile;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload);
      }
    }

    if (isset($upfire)) {
      $sql = "UPDATE users SET name = :name, mail= :mail, image=:image, password = :password, updated_at = :updated_at WHERE id = :id";
    }else{
      $sql = "UPDATE users SET name = :name, mail= :mail, password = :password, updated_at = :updated_at WHERE id = :id";
    }
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":mail" => $arr["mail"],
      ":password" => password_hash($arr["password"], PASSWORD_DEFAULT),
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    if (isset($upfile)) {
      $params = array_merge($params, array(":image" => $upfile));
    };

    $stmt->execute($params);
    $result= $stmt->fetch();
    return $result;
  }

  // 退会
  public function delete($id){

    // アカウントを論理削除
    $sql = "UPDATE users SET status = 1 WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
  }

  // admin_command------------------------------------------
  // 参照（全件）
  public function adminFindAll(){
    $sql = "select * FROM users";
    $stmt = $this->connect->prepare($sql);
    $stmt-> execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // 更新
  public function adminEdit($arr){
    $sql = "UPDATE users SET name=:name, mail=:mail, password=:password, role=:role, status=:status, updated_at=:updated_at WHERE id=:id";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":mail" => $arr["mail"],
      ":password" => $arr["password"],
      ":role" => $arr["role"],
      ":status" => $arr["status"],
      ":updated_at" => date("Y-m-d H:i:s"),
      ":id" => $arr["id"]
    );
    $stmt->execute($params);
    $result= $stmt->fetch();
    return $result;
  }
  // 削除
  public function adminDelete($id){
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(":id" => $id);
    $stmt->execute($params);
  }
  // 新規登録(role,status選択あり)
  public function adminCreate($arr){
    $sql = "INSERT INTO users (name, mail, password, image, role, status) VALUES (:name, :mail, :password, :image, :role, :status)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ":name" => $arr["name"],
      ":mail" => $arr["mail"],
      ":password" => $arr["password"],
      ":image" => $arr["image"],
      ":role" => $arr["role"],
      ":status" => $arr["status"]
    );
    $result = $stmt->execute($params);
    return $result;
  }


  // validation------------------------------------------
  // name:２０文字以内
  // mail:メールアドレス
  // pass：8文字以上
  // image:画像ファイル・ｊｐｇのみ
  //
  public function validate($arr){
    $message = array();

    if (empty($arr["name"])) {
      $message["name"] = "ユーザー名を入力してください。";
    }elseif (strlen($arr["name"]) > 20) {
      $message["name"] = "ユーザー名は20字以内で入力してください。";
    }
    // twitterからログインしていないユーザーの場合はID、パスは必須
    if (empty($_SESSION["user"]["twitter_id"])) {
      if (empty($arr["mail"])) {
        $message["mail"] = "メールアドレスを入力してください。";
      }
      if (empty($arr["password"])) {
        $message["password"] = "パスワードを入力してください。";
      }
    }
    // メールアドレスの型チェック
    if (!empty($arr["mail"])) {
      if (!filter_var($arr["mail"], FILTER_VALIDATE_EMAIL)) {
        $message["mail"] = "メールアドレスが不正です。";
      }
    }
    // パスワードの型チェック
    if (!empty($arr["password"])) {
      if (strlen($arr["password"])  < 8) {
        $message["password"] = "パスワードは8文字以上で入力してください。";
      }
    }
    // 画像のファイル形式チェック
    // inputタグで「accept=".jpg"」とすればOK
    return $message;
  }


  // ログイン時のバリデーション
  public function loginvalidate($arr){
    // 未入力エラー
    $message = array();
    if (empty($arr["mail"])) {
      $message["mail"] = "メールアドレスを入力してください。";
      // メール型エラー
    }elseif (!filter_var($arr["mail"], FILTER_VALIDATE_EMAIL)) {
      $message["mail"] = "メールアドレスが不正です。";
    }
    // パスワードの型チェック
    if (empty($arr["password"])) {
        $message["password"] = "パスワードを入力してください。";
    }
    return $message;
  }

  // パスワード変更時のバリデーション
  public function passwordValidate($arr){
    $message = array();
    // パスワードの型チェック
    if (empty($arr["password"])) {
      $message["password"] = "パスワードを入力してください。";
    }
    if (strlen($arr["password"])  < 8) {
      $message["password"] = "パスワードは8文字以上で入力してください。";
    }

    return $message;
  }

}
