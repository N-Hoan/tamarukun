<?php

require_once __DIR__ . "/def.php";

// TODO:第2段階で追加（各入力値チェック）　↓↓↓-------------------
$result = [
  "status"  => true,
  "message" => null,
  "messageNa" => null,
  "result"  => null,
];

$date=filter_input(INPUT_POST,"date");
$text=filter_input(INPUT_POST,"text");




//アカウント名の空白文字を置き換え
// $accountname= str_replace(" ","",$accountname);
// $accountname= str_replace("　","",$accountname);
//新規登録ボタンが押されたかどうか
if (isset($_POST["signUp"])) {
  //氏名が空かどうかのチェック
  if(!$date){
    $result["status"]=false;
    $result["message"].="入力してください<br>";
  }

  if(!$text){
      $result["status"]=false;
      $result["messageNa"].="入力してください<br>";
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

      //入力された商品番号のレコードがあるかチェックするSQL(LIMIT句はパフォーマンスのため今回はなくてもOK)
      $sqlSelect = "SELECT text FROM schedule WHERE MAIL = :mail LIMIT 1";
      //SQLの準備とバインド
      $stmt = $db->prepare($sqlSelect);
      $stmt->bindParam('mail', $mail, PDO::PARAM_INT);
      //SQLの実行
      $stmt->execute();
      //SQLの実行結果$countの中に上記SQLに実行結果がセットされているので、その内容でINSERTするかどうか
      //処理を振り分ける
      $count = $stmt->fetchColumn();

      if($count===1){
        $result["messageMa"].="同じメールアドレスが既に使われています。<br>";
      }else{

        $sql = "INSERT INTO ACCOUNT VALUES(:name,:mail,:accountname,:password)";

        //SQLの準備
        $stmt = $db->prepare($sql);
        //プレースホルダーのバインド
        $stmt->bindParam('name', $name, PDO::PARAM_STR);
        $stmt->bindParam('mail', $mail, PDO::PARAM_STR);
        $stmt->bindParam('accountname', $accountname, PDO::PARAM_STR);
        $stmt->bindParam('password', $password, PDO::PARAM_STR);
        //SQL実行
        $stmt->execute();
        //SQL実行結果件数の取得
        $result["result"] = $stmt->rowCount();//直前のexecuteした件数

        //挿入件数によってコミットかロールバックする
        if ($result["result"] === 1) {
          header("Location: accountRegist.php");	//accountRegist.phpへ画面遷移
          exit;	// 処理を終了させる
            // トランザクション確定
            $db->commit();
            // $result["message"] = "アカウントの登録に成功しました!
            //                       ログインしてください<br>";
        }
        //件数が違うのでロールバック
        else {
            $db->rollBack();
            $result["message"] = "アカウントの登録に失敗しました<br>";;
        }
        //DB切断
        $stmt=null;
        $db=null;
      }
    }catch(PDOException $poe){
      exit("DBエラー".$poe->getMessage());
    }
  }
}

// タイムゾーンを設定
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
    $date = $ym . '-' . $day;

    if ($today == $date) {
        // 今日の日付の場合は、class="today"をつける
        $week .= '<td class="today" data-day="' . $day . '">' . $day;
    } else {
        $week .= '<td data-day="' . $day . '">' . $day;
    }
    $week .= '<br>aaaaaaaaaaaaaaaaaa</td>';

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
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sys.css?ver=9.0.1">
    <title>PHPカレンダー</title>
</head>
<body>
    <!-- Menu選択 -->
    <img src="./img/home.jpg" class="home" alt="ホーム" width="100px" height="100px">
    <nav>
        <ul>  
            <li><button class="back">✕</button></li> 
            <li><a href="retrustOrHold.php" target="_blank">ホーム</a></li>
            <li>予定</li>
            <li>個人情報</li>
            <li>チャット</li>
            <li><button class="logout" onclick="location.href='petlogin.html'">ログアウト</button></li> 
        </ul>
    </nav>
    <!-- Menu選択 -->
    <div id="schedule"><a href="#">予定を変更</a></div>

    <div class="calender">
        <nav>
        
        </nav>
        <h3 class="mb-4"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt;</a></h3>
        <table class="table table-bordered" >
            
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
    <dialog id="loginDialog">
    <div id="loginWrapper">
      <header id="loginHeader">
        <h2>ログイン</h2>
        <button type="button" id="closeBtn">×</button>
      </header>
        <form action="" method="POST" class="add-form">
            <div>
                <label>日にち</label>
                <input type="date" name="date">
            </div>
            <div>
                <label>詳細</label>
                <textarea name="text" id=""></textarea>
            </div>
            <button type="submit" id="submitBtn">予定を登録</button>
            </div>
        </form>  
  </dialog>
    <!-- <script src="js/retrustOrHold.js"></script> -->
    <script src="./sys.js?ver=9.0.1"></script>
</body>
</html>