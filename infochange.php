<?php

session_start();
$email=$_SESSION['email'] ;


require_once 'C:\Sites\第2フェーズ\html\def.php';
require_once __DIR__ . "/def.php";

// TODO:第2段階で追加（各入力値チェック）　↓↓↓-------------------
$result = [
  "status"  => true,
  "result"  => null,
//   "name" => null,
  "mail" => null,
  "username" => null,
  "password" => null,
  "password2" => null,
];

// $name=filter_input(INPUT_POST,"name");
$username=filter_input(INPUT_POST,"username");
$mail=filter_input(INPUT_POST,"mail");
$password=filter_input(INPUT_POST,"password");
$password2=filter_input(INPUT_POST,"password2");

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$db = new PDO($dsn, DB_USER, DB_PASS);
                
//2.属性変更
//例外を発生させる
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//静的プレスホルダの設定
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

$userid = "SELECT userID FROM USER WHERE EMAIL = :email LIMIT 1";
    $stmt = $db->prepare($userid);
    $stmt->bindParam('email', $email, PDO::PARAM_INT);
    //SQLの実行
    $stmt->execute();
    //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
    //処理を振り分ける
    $userid = $stmt->fetchColumn();

//アカウント名の空白文字を置き換え
// $accountname= str_replace(" ","",$accountname);
// $accountname= str_replace("　","",$accountname);
//新規登録ボタンが押されたかどうか
if (isset($_POST["infoChange"])) {
    //氏名が空かどうかのチェック
    // if(!$name){
    //     $result["status"]=false;
    // }

    //アドレスが空かどうかのチェック
    if(!$mail){
        $result["status"]=false;
    }

    //アカウント名が空かどうかのチェック
    if(!$username){
        $result["status"]=false;
    }

    //パスワードが空かどうかのチェック
    if(!$password){
        $result["status"]=false;
    }

    if(!$password2){
        $result["status"]=false;
    }

    // $userid = "SELECT userID FROM USER WHERE EMAIL = :mail LIMIT 1";
    // $stmt = $db->prepare($userid);
    // $stmt->bindParam('mail', $mail, PDO::PARAM_INT);
    // //SQLの実行
    // $stmt->execute();
    // //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
    // //処理を振り分ける
    // $userid = $stmt->fetchColumn();

    //DB接続
    //全部入力されてふたつのパスワードが同じだったら
    if($result["status"] && $password2==$password){
        
            try{

                $db->beginTransaction();
                
                $sqlUpdate = "UPDATE USER SET userName = :username , email = :mail , password = :password WHERE userID = :userid";
                //入力された商品番号のレコードがあるかチェックするSQL(LIMIT句はパフォーマンスのため今回はなくてもOK)
                // $sqlUpdate = "SELECT count(*) FROM USER WHERE EMAIL = :mail LIMIT 1";
                //SQLの準備とバインド
                $stmt = $db->prepare($sqlUpdate);
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                
                //SQLの実行
                $stmt->execute();
                //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
                //処理を振り分ける
                    $result["result"] = $stmt->rowCount();//直前のexecuteした件数

                    //挿入件数によってコミットかロールバックする
                    if ($result["result"] === 1) {
                        $_SESSION ['email'] = $mail;
                        $db->commit();
                        header("Location: myshift.php");	//accountRegist.phpへ画面遷移
                        exit;	// 処理を終了させる
                        // トランザクション確定
                        
                    }
                    //件数が違うのでロールバック
                    else {
                        // header("Location: myshift.php");	//accountRegist.phpへ画面遷移
                        // exit;
                        $db->rollBack();
                    }
                    // $db->commit();
                    // header("Location: infochange.php");	//accountRegist.phpへ画面遷移
                    // exit;
            }catch(PDOException $poe){
                exit("DBエラー".$poe->getMessage());
            }    
        
    }

}
//DB切断
$stmt=null;
$db=null;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/infochange.css?ver=2.3.5">
    <link rel="stylesheet" href="./styles/common.css?ver=2.3.5">
    <title>infochange</title>
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
                    <!-- <li><a href="./userInfo.html">ログイン</a></li> -->
                    <li><a href="./infochange.php">アカウント情報変更</a></li>
                    <li class="admin-menu"><a href="./manager.html">ほしいものリスト</a></li>
                    <li class="admin-menu"><a href="./manager.html">貯金金額画面</a></li>
                    <li class="admin-menu"><a href="./myshift.php">シフトボード</a></li>
                    <li><a href="./login.php" id="logout">ログアウト</a></li>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <div class="main-contents">
            <div class="content">
                <h1 class="center">情報変更<?=$userid?></h1>
                <hr>
                <form id="registrationForm" method="POST">
                    <div class="wrap-box flex-even">
                        <div class="center input-item">
                            <label for="memberId">
                                <h2>ACOUNT NAME</h2>
                            </label>
                            <div class="input-group">
                                <img src="/img/human.jpeg" alt="人ロゴ">
                                <input type="text" id="username" name="username" placeholder="アカウント名を入力してください" required>
                            </div>
                        </div>
                        <div class="center input-item">
                            <label for="memberId">
                                <h2>MAIL</h2>
                            </label>
                            <div class="input-group">
                                <img src="/img/mail.png" alt="メールロゴ">
                                <input type="text" id="mail" name="mail" placeholder="メールを入力してください" required>
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
                        <div class="center input-item">
                            <label for="password">
                                <h2>パスワード（再）</h2>
                            </label>
                            <div class="input-group">
                                <img src="/img/key.png" alt="鍵ロゴ">
                                <input type="password" id="password" name="password2" placeholder="パスワードを入力してください" required>
                            </div>
                        </div>
                        <div class="submit">
                            <button type="submit" id="submit" name="infoChange">変更</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <!-- <script>
        // document.getElementById('registrationForm').addEventListener('submit', function(event) {
        //     event.preventDefault();
        //     window.location.href = 'login.html'; // Thay đổi thành trang bạn muốn chuyển hướng tới
        // });
    </script> -->
    <script src="styles/schedule.js"></script>
</body>
</html>