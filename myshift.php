<?php

session_start();

require_once __DIR__ . "/def.php";

$result=null;

$date=filter_input(INPUT_POST,"date");
$message=filter_input(INPUT_POST,"message");
$mail=$_SESSION['email'] ;
// if(!$mail){
//     header("Location: login.php");	//accountRegist.phpへ画面遷移
//     exit;	
// }
$userid;

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$db = new PDO($dsn, DB_USER, DB_PASS);
        
        //2.属性変更
        //例外を発生させる
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //静的プレスホルダの設定
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);


        $passwordResult = "SELECT userID FROM USER WHERE EMAIL = :mail LIMIT 1";
        $stmt = $db->prepare($passwordResult);
        $stmt->bindParam('mail', $mail, PDO::PARAM_INT);
        //SQLの実行
        $stmt->execute();
        //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
        //処理を振り分ける
        $userid = $stmt->fetchColumn();

if (isset($_POST['addShift'])) {
        //既にその日程に予定が登録されているかどうか   
        $passwordResult = "SELECT count(*) FROM SCHEDULE WHERE userID = :userid AND date = :date LIMIT 1";
        $stmt = $db->prepare($passwordResult);
        $stmt->bindParam('userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam('date', $date, PDO::PARAM_INT);
        //SQLの実行
        $stmt->execute();
        //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
        //処理を振り分ける
        $count = $stmt->fetchColumn();

        if($count !== 0){
            try{
                  $sql = "UPDATE SCHEDULE SET detailText = :message WHERE userID = :userid AND date = :date";
          
                  //SQLの準備
                  $stmt = $db->prepare($sql);
                  //プレースホルダーのバインド
                  $stmt->bindParam('userid', $userid, PDO::PARAM_STR);
                  $stmt->bindParam('date', $date, PDO::PARAM_STR);
                  $stmt->bindParam('message', $message, PDO::PARAM_STR);
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
        }else{
            try{
        
                $sql = "INSERT INTO SCHEDULE VALUES(:userid,:date,:message)";
          
                  //SQLの準備
                  $stmt = $db->prepare($sql);
                  //プレースホルダーのバインド
                  $stmt->bindParam('userid', $userid, PDO::PARAM_STR);
                  $stmt->bindParam('date', $date, PDO::PARAM_STR);
                  $stmt->bindParam('message', $message, PDO::PARAM_STR);
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
}   

date_default_timezone_set('Asia/Tokyo');

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // 今月の年月を表示
    $ym = date('Y-m');
}

// タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// 今日の日付 フォーマット　例）2021-06-3
$today = date('Y-m-j');

// カレンダーのタイトルを作成　例）2021年6月
$html_title = date('Y年n月', $timestamp);

// 前月・次月の年月を取得
// 方法１：mktimeを使う mktime(hour,minute,second,month,day,year)
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));

// 方法２：strtotimeを使う
// $prev = date('Y-m', strtotime('-1 month', $timestamp));
// $next = date('Y-m', strtotime('+1 month', $timestamp));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// １日が何曜日か　0:日 1:月 2:火 ... 6:土
// 方法１：mktimeを使う
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));
// 方法２
// $youbi = date('w', $timestamp);


// カレンダー作成の準備
$weeks = [];
$week = '';

// 第１週目：空のセルを追加
// 例）１日が火曜日だった場合、日・月曜日の２つ分の空セルを追加する
$week .= str_repeat('<td></td>', $youbi);

for ( $day = 1; $day <= $day_count; $day++, $youbi++) {

    // 2021-06-3
    if($day >= 1 && $day <= 9 ){
        $date = $ym . '-0' . $day;
    }else{
        $date = $ym . '-' . $day;
    }

    if ($today == $date) {
        // 今日の日付の場合は、class="today"をつける
        $week .= '<td data-day="' . $day . '"> <div class="day" id="today">' . $day .'</div>';
    } else {
        $week .= '<td data-day="' . $day . '"> <div class="day">' . $day .'</div>';
    }

    //その日に予定が入ってるかチェック
    try{
        $passwordResult = "SELECT count(*) FROM SCHEDULE WHERE userID = :userid AND date = :date LIMIT 1";
        $stmt = $db->prepare($passwordResult);
        $stmt->bindParam('userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam('date', $date, PDO::PARAM_INT);
        //SQLの実行
        $stmt->execute();
        //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
        //処理を振り分ける
        $count = $stmt->fetchColumn();
    }catch(PDOException $poe){
        exit("DBエラー".$poe->getMessage());
    }
        if($count = 1){
            // $scheduleCheck = "SELECT count(*) FROM SCHEDULE WHERE userID = 1 AND date = 2024-06-04  LIMIT 1";
            $sql = "SELECT detailText FROM SCHEDULE WHERE userID = :userid AND date = :date LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('userid', $userid, PDO::PARAM_INT);
            $stmt->bindParam('date', $date, PDO::PARAM_INT);
            //SQLの実行
            $stmt->execute();
            //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
            //処理を振り分ける
            $scheduleText = $stmt->fetchColumn();
            $week .= '<div class="scheduleText">' . $scheduleText . '</div></td>';//. $scheduleText .
        }


    // 週終わり、または、月終わりの場合
    if ($youbi % 7 == 6 || $day == $day_count) {

        if ($day == $day_count) {
            // 月の最終日の場合、空セルを追加
            // 例）最終日が水曜日の場合、木・金・土曜日の空セルを追加
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }

        // weeks配列にtrと$weekを追加する
        $weeks[] = '<tr>' . $week . '</tr>';

        // weekをリセット
        $week = '';
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/schedule.css?ver=2.67.1">
    <title>PHPカレンダー</title>
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
    
    <div class="form">
            <form action="" method="POST" class="add-form">
                <div class="mt-1">
                    <label for="">日にち</label>
                    <input type="date" name="date" value=<?=$today?>>
                </div>
                <div class="mt-1">
                    <label for="">詳細</label>
                    <input type="text" name="message">
                </div>
                <button type="submit" name="addShift">予定を追加</button>
            </form>
    </div>
    <div class="calender">
        <!-- <div><a href="scheduleDetail.php" target="_blank">予定を変更</a></div> -->
        <h3 class="mb-4"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt;</a></h3>
        <table class="table table-bordered" cellpadding="0" cellspacing="0" >
            
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php
                foreach ($weeks as $week) {
                    echo $week;
                }
            ?>
        </table>
    </div>
    </main>
    <script src="styles/schedule.js"></script>
</body>
</html>