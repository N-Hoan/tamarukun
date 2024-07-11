<?php

session_start();
$userid=$_SESSION['id'] ;

require_once __DIR__ . "/def.php";

// TODO:第2段階で追加（各入力値チェック）　↓↓↓-------------------
$result = [
  "status"  => true,
  "result"  => null,
  "message" => null
];

$money=filter_input(INPUT_POST,"money");

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$db = new PDO($dsn, DB_USER, DB_PASS);
                
//2.属性変更
//例外を発生させる
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//静的プレスホルダの設定
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

$status = "SELECT status FROM WANT WHERE userID = :userid LIMIT 1";
$stmt = $db->prepare($status);
$stmt->bindParam('userid', $userid, PDO::PARAM_INT);
//SQLの実行
$stmt->execute();
//SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
//処理を振り分ける
$status = $stmt->fetchColumn();

$price = "SELECT price FROM WANT WHERE userID = :userid LIMIT 1";
$stmt = $db->prepare($price);
$stmt->bindParam('userid', $userid, PDO::PARAM_INT);
//SQLの実行
$stmt->execute();
//SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
//処理を振り分ける
$price = $stmt->fetchColumn();

if($price<$status){
  header("Location: main.php");	//accountRegist.phpへ画面遷移
  exit;	
}

if (isset($_POST["submit"])) {
    if (!preg_match("/[0-9]/", $money)) {
        $result["status"]=false;
        $result["message"]="半角数字で入力してください<br>";
    }else{
      try{
        $db->beginTransaction();
        $status = "SELECT status FROM WANT WHERE userID = :userid LIMIT 1";
        $stmt = $db->prepare($status);
        $stmt->bindParam('userid', $userid, PDO::PARAM_INT);
        //SQLの実行
        $stmt->execute();
        //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
        //処理を振り分ける
        $status = $stmt->fetchColumn();
        $newStatus=$status+$money;
                
        $sqlUpdate = "UPDATE WANT SET status = :newStatus  WHERE userID = :userid";
        //SQLの準備とバインド
        $stmt = $db->prepare($sqlUpdate);
        $stmt->bindParam('newStatus', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam('userid', $userid, PDO::PARAM_STR);

        //SQLの実行
        $stmt->execute();
        //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
        //処理を振り分ける
        $result["result"] = $stmt->rowCount();//直前のexecuteした件数

        //挿入件数によってコミットかロールバックする
        if ($result["result"] === 1) {
          $_SESSION ['email'] = $mail;
          $db->commit();
          header("Location: main.php");	//accountRegist.phpへ画面遷移
          exit;	// 処理を終了させる
          // トランザクション確定                        
        }
        //件数が違うのでロールバック
        else {
          $db->rollBack();
        }
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
  <link rel="stylesheet" href="./styles/addmoney.css?ver=2.9.80">
  <title>addmoney</title>
</head>

<body>
  <header>
    <h1>貯まる君</h1>
    <div class="headerMenu">
      <div class="menuIcon">
        <div class="hamburgerMenu">
          <div class="hamburgerBar hb1"></div>
          <div class="hamburgerBar hb2"></div>
          <div class="hamburgerBar hb3"></div>
        </div>
      </div>
      <div class="menuList">
        <ul>
            <li><a href="./infochange.php">アカウント情報変更</a></li>
            <li class="admin-menu"><a href="./want.php">ほしいものリスト</a></li>
            <li class="admin-menu"><a href="./addmoney.php">貯金金額画面</a></li>
            <li class="admin-menu"><a href="./main.php">達成状況状況確認</a> </li>
            <li class="admin-menu"><a href="./myshift.php">シフトボード</a></li>
            <li><a href="./login.php" id="logout">ログアウト</a></li>
        </ul>
      </div>
    </div>
  </header>
  <main>
      <div class="main-contents">
        <div class="content">
          <h1 class="center">貯金金額画面</h1>
          <hr>
            <form method="POST" id="registrationForm">
              <div class="input-item">
                <label for="addmoney">
                  <input type="text" name="money" class="money">
                </label>
              </div>
              <div class="submit">
                <input type="submit" id="submit" name="submit" value="登録">
              </div>
            </form>  
        </div>
    </div>
  </main>
  <script src="./js/schedule.js"></script>
</body>

</html>