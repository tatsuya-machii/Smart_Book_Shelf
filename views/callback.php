<?php
session_start();
require_once("../config/config.php");
require_once("../model/User.php");

// twitteroauth の読み込み
require "../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

//Twitterのコンシュマーキー(APIキー)等読み込み
define('TWITTER_API_KEY', '1JpUVP7dNOL8V81WpshXEsdFP'); //Consumer Key (API Key)
define('TWITTER_API_SECRET', 'ySoffbkluE5AVYtiFx9Id4CnVdNErwHvw9wiJQR1AewNDiKPuV');//Consumer Secret (API Secret)



//リクエストトークンを使い、アクセストークンを取得する
$twitter_connect = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$access_token = $twitter_connect->oauth('oauth/access_token', array('oauth_verifier' => $_GET['oauth_verifier'], 'oauth_token'=> $_GET['oauth_token']));
print_r($access_token);


//アクセストークンからユーザの情報を取得する
$user_connect = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$user_info = $user_connect->get('account/verify_credentials');//アカウントの有効性を確認するためのエンドポイント

if (empty($user_info)) {
  // code...
  echo "アクセストークンがからです。";
  print_r($access_token);
}else{

  try {
    // DB接続
    $user = new User($host, $dbname, $user, $pass);
    $user->connectDb();
    // $user = ユーザー情報（twitter_ID == $access_token["user_id"]）取得
    $tw_user = $user->findByTwitterUser($user_info->id);

    // 条件分岐（$tw_userがいない場合）
    if (empty($tw_user)) {
      // ユーザーをクリエイト（name= $access_token["screen_name"]）
      $name = $user_info->name;
      $twitter_id = $user_info->id;
      $arr = ["name" => $name, "twitter_id" => $twitter_id];
      $tw_user = $user->findByTwitterUser($twitter_id);
    }

    $_SESSION["user"] = $tw_user;

    if (!empty($_SESSION["user"])) {
      header("location: /curriculum/Smart_Book_Shelf/views/users/main.php");
      exit;
    }

  } catch (\PDOException $e) {
    echo "erroe";

    print "エラー！". $e->getMessage()."</br>";

  }


}


?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	  <title>サンプルコード コールバックページ-【2019年】phpでTwitterログイン機能を実装[twitteroauth]</title>
	<meta name="robots" content="noindex, nofollow">
	</head>
	<body>
        	<section class="box1 entry">
            	<h1 class="boxh1_2">コールバック</h1>
<?php
//ユーザ情報が取得できればcomplete.html、それ以外はerror.htmlに移動する
if(isset($user_info->id_str)){
?>
			<p>ログイン成功　<?php echo $user_info->name; ?>さん</p>
			<p>ログインが本当に成功していることを表現するために名前を表示していますが、データの保存は一切行ってないので安心してください。</p>
<?php }else{ ?>
			<p>ログイン失敗</p>
<?php } ?>
  </body>
</html>
