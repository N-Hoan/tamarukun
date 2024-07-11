<?php

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // ログインページにリダイレクト
    exit();
}

$dsn = 'mysql:host=localhost;dbname=zenitamaru';
$db_user = 'root';
$db_password = "root";

if (isset($_SESSION['user_ID'])) {
    $userID = $_SESSION['user_ID'];
} else {
    die("セッションにユーザーIDが設定されていません");
}

$result = null;

$date = filter_input(INPUT_POST, "date");
$message = filter_input(INPUT_POST, "message");
$mail = $_SESSION['email'];

try {
    $dsn = "mysql:host=localhost;dbname=zenitamaru;charset=utf8";
    $db = new PDO($dsn, 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $passwordResult = "SELECT userID FROM USER WHERE EMAIL = :mail LIMIT 1";
    $stmt = $db->prepare($passwordResult);
    $stmt->bindParam(':mail', $mail, PDO::PARAM_STR); // プレースホルダーの修正
    $stmt->execute();
    $userid = $stmt->fetchColumn();

    if (isset($_POST['addShift'])) {
        // トランザクションを開始
        $db->beginTransaction();

        // 既にその日程に予定が登録されているかどうか
        $passwordResult = "SELECT count(*) FROM SCHEDULE WHERE userID = :userid AND date = :date LIMIT 1";
        $stmt = $db->prepare($passwordResult);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR); // 修正: 日付は文字列
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count != 0) {
            $sql = "UPDATE SCHEDULE SET detailText = :message WHERE userID = :userid AND date = :date";
        } else {
            $sql = "INSERT INTO SCHEDULE (userID, date, detailText) VALUES (:userid, :date, :message)";
        }

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR); // 修正: 日付は文字列
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->rowCount();

        if ($result === 1) {
            // トランザクション確定
            $db->commit();
            header("Location: myshift.php"); // myshift.phpへ画面遷移
            exit(); // 処理を終了させる
        } else {
            $db->rollBack();
        }
    }

} catch (PDOException $poe) {
    exit("DBエラー: " . $poe->getMessage());
}

date_default_timezone_set('Asia/Tokyo');

if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    $ym = date('Y-m');
}

$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

$today = date('Y-m-j');
$html_title = date('Y年n月', $timestamp);

$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) - 1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp) + 1, 1, date('Y', $timestamp)));

$day_count = date('t', $timestamp);
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));

$weeks = [];
$week = '';

$week .= str_repeat('<td></td>', $youbi);

for ($day = 1; $day <= $day_count; $day++, $youbi++) {
    $date = $ym . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);

    if ($today == $date) {
        $week .= '<td data-day="' . $day . '"> <div class="day" id="today">' . $day . '</div>';
    } else {
        $week .= '<td data-day="' . $day . '"> <div class="day">' . $day . '</div>';
    }

    try {
        $passwordResult = "SELECT count(*) FROM SCHEDULE WHERE userID = :userid AND date = :date LIMIT 1";
        $stmt = $db->prepare($passwordResult);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR); // 修正: 日付は文字列
        $stmt->execute();
        $count = $stmt->fetchColumn();
    } catch (PDOException $poe) {
        exit("DBエラー: " . $poe->getMessage());
    }

    if ($count == 1) {
        $sql = "SELECT detailText FROM SCHEDULE WHERE userID = :userid AND date = :date LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR); // 修正: 日付は文字列
        $stmt->execute();
        $scheduleText = $stmt->fetchColumn();
        $week .= '<div class="scheduleText">' . htmlspecialchars($scheduleText, ENT_QUOTES, 'UTF-8') . '</div></td>';
    }

    if ($youbi % 7 == 6 || $day == $day_count) {
        if ($day == $day_count) {
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }
        $weeks[] = '<tr>' . $week . '</tr>';
        $week = '';
    }
}

$stmt = null;
$db = null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/schedule.css?ver=20.60.1">
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
                    <li class="admin-menu"><a href="./want.php">ほしいものリスト</a></li>
                    <li class="admin-menu"><a href="./addmoney.php">貯金金額画面</a></li>
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
        <h3 class="month"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt;</a></h3>
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