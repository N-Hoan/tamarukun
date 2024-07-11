<?php
session_start(); // セッションを開始

require_once __DIR__ . "/def.php";
$result=true;

$thingName=filter_input(INPUT_POST,"thingName");
$price=filter_input(INPUT_POST,"price");
$startDate=filter_input(INPUT_POST,"startDate");
$goalDate=filter_input(INPUT_POST,"goalDate");

date_default_timezone_set('Asia/Tokyo');
// 今日の日付 フォーマット　例）2021-06-3
$today = date('Y-m-j');
$today2 = date('Y-m-j');

$userid=$_SESSION['id'] ;

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$db = new PDO($dsn, DB_USER, DB_PASS);
        
//2.属性変更
//例外を発生させる
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//静的プレスホルダの設定
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

if (isset($_POST['add'])) {
    //氏名が空かどうかのチェック
    if(!$thingName){
        $result=false;
    }

    //アドレスが空かどうかのチェック
    if(!$price){
        $result=false;
    }

    //アカウント名が空かどうかのチェック
    if(!$startDate){
        $result=false;
    }

    //パスワードが空かどうかのチェック
    if(!$goalDate){
        $result=false;
    }

    if($result){
        try {
            //既にその日程に予定が登録されているかどうか   
            $count = "SELECT count(*) FROM WANT WHERE userID = :userid LIMIT 1";
            $stmt = $db->prepare($count);
            $stmt->bindParam('userid', $userid, PDO::PARAM_INT);

            //SQLの実行
            $stmt->execute();
            //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
            //処理を振り分ける
            $count = $stmt->fetchColumn();

            if($count == 1){
                try{

                // SQL クエリの準備
                $sql = "UPDATE WANT SET thingName = :thingName , price = :price , startDate = :startDate , goalDate = :goalDate , status = 0 WHERE userID = :userid";
                $stmt = $db->prepare($sql);

                // パラメータをバインドしてクエリを実行
                $stmt->bindParam('thingName', $thingName, PDO::PARAM_STR);
                $stmt->bindParam('price', $price, PDO::PARAM_STR);
                $stmt->bindParam('startDate', $startDate, PDO::PARAM_STR);
                $stmt->bindParam('goalDate', $goalDate, PDO::PARAM_STR);
                $stmt->bindParam('userid', $userid, PDO::PARAM_STR);

                //SQL実行
                $stmt->execute();
                //SQL実行結果件数の取得
                $result= $stmt->rowCount();//直前のexecuteした件数
        
                //挿入件数によってコミットかロールバックする
                if ($result === 1) {
                  header("Location: myshift.php");	//accountRegist.phpへ画面遷移
                  exit;	// 処理を終了させる
                    // トランザクション確定
                    $db->commit();
                }
                //件数が違うのでロールバック
                else {
                    $db->rollBack();
                }

                }catch(PDOException $e){
                    echo "エラー: " . $e->getMessage();
                }
            }else{
                try{
        
                    $sql = "INSERT INTO WANT (userID,thingName,price,startDate,goalDate,status)VALUES(:userid,:thingName,:price,:startDate,:goalDate,0)";
              
                      //SQLの準備
                      $stmt = $db->prepare($sql);
                      //プレースホルダーのバインド
                      $stmt->bindParam('userid', $userid, PDO::PARAM_STR);
                      $stmt->bindParam('thingName', $thingName, PDO::PARAM_STR);
                      $stmt->bindParam('price', $price, PDO::PARAM_STR);
                      $stmt->bindParam('startDate', $startDate, PDO::PARAM_STR);
                      $stmt->bindParam('goalDate', $goalDate, PDO::PARAM_STR);

                      //SQL実行
                      $stmt->execute();
                      //SQL実行結果件数の取得
                      $result= $stmt->rowCount();//直前のexecuteした件数
              
                      //挿入件数によってコミットかロールバックする
                      if ($result === 1) {
                        header("Location: myshift.php");	//accountRegist.phpへ画面遷移
                        exit;	// 処理を終了させる
                          // トランザクション確定
                          $db->commit();
                      }
                      //件数が違うのでロールバック
                      else {
                          $db->rollBack();
                      }
                }catch(PDOException $poe){
                    exit("DBエラー".$poe->getMessage());
                }

            }    
            

        } catch (PDOException $e) {
            echo "エラー: " . $e->getMessage();
        }
    }
}

?>



<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/want.css?ver=702.390.100">
    <title>たまる君</title>
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
        <h1>欲しいものリスト</h1>
        <form id="registration-form" method="POST">
            <div id="registrationDialog">
                <div class="thingName">
                    <input type="text" id='thingName' name="thingName" placeholder="欲しいものの名前" />
                </div>
                <div class="price">
                    <input type="text" id='price' name="price" placeholder="金額" />
                </div>
                <div class="startDate">  
                    <input type="date" id="startDate" name="startDate" value=<?=$today?>>
                </div>
                <div class="goalDate">
                    <input type="date" id="goalDate" name="goalDate"  value=<?=$today?>>
                </div>
                <div class="submit">
                    <input type="submit" name="add" value="登録">
                </div>    
            </div>
        </form>
    </main>
    <script src="./js/schedule.js"></script>
    <!-- <script src="./styles/today.js"></script> -->
    <script src="./js/tomorrow.js"></script>
</body>


</html>