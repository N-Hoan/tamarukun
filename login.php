<?php

//セッションを使う
session_start();

require_once __DIR__ . "/def.php";

$result = [
  "status"  => true,
  "result"  => null,
];

$mail=filter_input(INPUT_POST,"mail");
$password=filter_input(INPUT_POST,"password");

//ログインボタンが押されたかどうか
if (isset($_POST["submit"])) {
  //アドレスが空かどうかのチェック
  if(!$mail){
    $result["status"]=false;
  }
  //パスワードが空かどうかのチェック
  if(!$password){
      $result["status"]=false;
  }

  //DB接続
  if($result["status"]){
    try{

      //課題８ではoldProductから全件表示
      //1.PDOクラスのインスタンス化
      $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
      $db = new PDO($dsn, DB_USER, DB_PASS);
      
      //2.属性変更
      //例外を発生させる
      $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      //静的プレスホルダの設定
      $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

      $sqlSelect = "SELECT count(*) FROM USER WHERE EMAIL = :mail LIMIT 1";
      //SQLの準備とバインド
      $stmt = $db->prepare($sqlSelect);
      $stmt->bindParam('mail', $mail, PDO::PARAM_INT);
      //SQLの実行
      $stmt->execute();
      //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
      //処理を振り分ける
      $count = $stmt->fetchColumn();
      //入力されたメールアドレスがデータベースにあるか
      if($count===1){
        $passwordResult = "SELECT PASSWORD FROM USER WHERE EMAIL = :mail LIMIT 1";
        $stmt = $db->prepare($passwordResult);
        $stmt->bindParam('mail', $mail, PDO::PARAM_INT);
        //SQLの実行
        $stmt->execute();
        //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
        //処理を振り分ける
        $passwordResult = $stmt->fetchColumn();

        //入力されたパスワードが一致しているか
        if($password==$passwordResult){
          $_SESSION ['email'] = $mail;
          header("Location: infochange.php");	//accountRegist.phpへ画面遷移
          exit;	// 処理を終了させる
        }
      }
      //DB切断
      $stmt=null;
      $db=null;
    }catch(PDOException $poe){
      exit("DBエラー".$poe->getMessage());
    }
  }
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/common.css">
  <title>login</title>
</head>

<body>
  <header>
    <h1>貯まる君</h1>
  </header>
  <main>
    <div class="main-contents">
      <div class="content">
        <h1 class="center">アカウントログイン</h1>
        <hr>
        <form method="POST">
          <div class="wrap-box flex-even">
            <div class="center input-item">
              <label for="memberId">
                  <h2>MALL</h2>
              </label>
              <div class="input-group">
                  <img src="/img/mail.png" alt="メールロゴ">
                  <input type="text" id="" name="mail" placeholder="メールアドレスを入力してください" required>
              </div>
          </div>
            <div class="center input-item">
              <label for="password">
                <h2>パスワード</h2>
              </label>
              <div class="input-group">
                <img src="/img/key.png" alt="鍵ロゴ">
                <input type="password" id="password" name="password" placeholder="パスワードを入力してください" required>
              </div>

            </div>
            <div class="submit">
              <input type="submit" id="submit" name="submit" value="ログイン">
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>
  <!-- <footer>
    <div>2024 &copy tamarukun-team</div>
    <hr><a href="./userInfo.html">
    <nav>
      <img src="/img/facebook.png" alt="">
      <img src="/img/mail.png" alt="">
      <img src="/img/LINE.png" alt="">
      <img src="/img/Phone.png" alt="">
    </nav>
  </footer> -->
</body>

</html>