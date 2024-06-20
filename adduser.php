<?php

session_start();

require_once __DIR__ . "/def.php";

// TODO:第2段階で追加（各入力値チェック）　↓↓↓-------------------
$result = [
  "status"  => true,
  "result"  => 0,
//   "name" => null,
//   "mail" => null,
//   "accountname" => null,
//   "rule" => null,
//   "password" => null,
//   "mynumbercard" => null,
];

$name=filter_input(INPUT_POST,"name");
$username=filter_input(INPUT_POST,"username");
$mail=filter_input(INPUT_POST,"mail");
$password=filter_input(INPUT_POST,"password");
$password2=filter_input(INPUT_POST,"password2");



//アカウント名の空白文字を置き換え
// $accountname= str_replace(" ","",$accountname);
// $accountname= str_replace("　","",$accountname);
//新規登録ボタンが押されたかどうか
if (isset($_POST["addUser"])) {
    //氏名が空かどうかのチェック
    if(!$name){
        $result["status"]=false;
    }

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

//   $result["name"]=$name;
//   $result["accountname"]=$accountname;
//   $result["mail"]=$mail;
//   $result["npassword"]=$password;
//   $result["mynumbercard"]=$mynumbercard;

    //DB接続
    //全部入力されたら
    if($result["status"]){
        //ふたつのパスワードが同じだったら
        if($password2==$password){
            try{
                //1.PDOクラスのインスタンス化
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $db = new PDO($dsn, DB_USER, DB_PASS);
                
                //2.属性変更
                //例外を発生させる
                $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                //静的プレスホルダの設定
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

                //入力された商品番号のレコードがあるかチェックするSQL(LIMIT句はパフォーマンスのため今回はなくてもOK)
                $sqlSelect = "SELECT count(*) FROM USER WHERE EMAIL = :mail LIMIT 1";
                //SQLの準備とバインド
                $stmt = $db->prepare($sqlSelect);
                $stmt->bindParam('mail', $mail, PDO::PARAM_INT);
                //SQLの実行
                $stmt->execute();
                //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
                //処理を振り分ける
                $count = $stmt->fetchColumn();

                if($count!==1){

                    $sql = "INSERT INTO USER(name,email,accountName,password) VALUES(:name,:mail,:username,:password)";

                    //SQLの準備
                    $stmt = $db->prepare($sql);
                    //プレースホルダーのバインド
                    $stmt->bindParam('name', $name, PDO::PARAM_STR);
                    $stmt->bindParam('mail', $mail, PDO::PARAM_STR);
                    $stmt->bindParam('username', $username, PDO::PARAM_STR);
                    $stmt->bindParam('password', $password, PDO::PARAM_STR);
                    //SQL実行
                    $stmt->execute();
                    //SQL実行結果件数の取得
                    $result["result"] = $stmt->rowCount();//直前のexecuteした件数

                    //挿入件数によってコミットかロールバックする
                    if ($result["result"] === 1) {
                        $_SESSION ['email'] = $mail;
                        header("Location: myshift.php");	//accountRegist.phpへ画面遷移
                        exit;	// 処理を終了させる
                        // トランザクション確定
                        $db->commit();
                    }
                    //件数が違うのでロールバック
                    else {
                        $db->rollBack();
                    }
                }
            }catch(PDOException $poe){
                exit("DBエラー".$poe->getMessage());
            }
        }else{
            header("Location: infochange.php");	//accountRegist.phpへ画面遷移
                    exit;
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
    <link rel="stylesheet" href="./styles/common.css">
    <title>adduser</title>
</head>

<body>
    <header>
        <h1>貯まる君</h1>
    </header>
    <main>
        
        <div class="main-contents">
            <div class="content">
                <h1 class="center">新規登録</h1>
                <hr>
                
                <form method="POST">
                    <div class="wrap-box flex-even">
                        <div class="center input-item">
                            <label for="memberId">
                                <h2>NAME</h2>
                            </label>
                            <div class="input-group">
                                <img src="/img/human.jpeg" alt="人ロゴ">
                                <input type="text" id="name" name="name" placeholder="名前を入力してください" required>
                            </div>
                        </div>
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
                                <h2>MALL</h2>
                            </label>
                            <div class="input-group">
                                <img src="/img/mail.png" alt="メールロゴ">
                                <input type="text" id="mail" name="mail" placeholder="メールアドレスを入力してください" required>
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
                                <h2>パスワード(再)</h2>
                            </label>
                            <div class="input-group">
                                <img src="/img/key.png" alt="鍵ロゴ">
                                <input type="password" id="password" name="password2" placeholder="パスワードを入力してください" required>
                            </div>
                        </div>
                        <div class="submit">
                            <button type="submit" name="addUser">登録</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <!-- <script> -->
        <!-- // document.getElementById('registrationForm').addEventListener('submit', function(event) {
        //     event.preventDefault();
        //     window.location.href = './login.html'; // Thay đổi thành trang bạn muốn chuyển hướng tới
        // }); -->
    <!-- </script> -->
</body>

</html>