<?php
session_start(); // セッションを開始

require_once __DIR__ . "/def.php";

$dsn = 'mysql:host=localhost;dbname=zenitamaru';
$db_user = 'root';
$db_password = 'root';

if (isset($_SESSION['id'])) {
    $userID = $_SESSION['id'];
} else {
    die("セッションにユーザーIDが設定されていません");
}
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$db = new PDO($dsn, DB_USER, DB_PASS);
        
//2.属性変更
//例外を発生させる
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//静的プレスホルダの設定
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

try {
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 貯金金額の値抽出
    $Savings_stmt = $pdo->prepare('SELECT status FROM want WHERE userID = :userID LIMIT 1');
    $Savings_stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $Savings_stmt->execute();
    $Savings = $Savings_stmt->fetch(PDO::FETCH_ASSOC);

    // 目標金額の値抽出
    $price_stmt = $pdo->prepare('SELECT price FROM want WHERE userID = :userID LIMIT 1');
    $price_stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $price_stmt->execute();
    $price = $price_stmt->fetch(PDO::FETCH_ASSOC);

    $Savings_want = $Savings ? $Savings['status'] : 'データが見つかりませんでした。';
    $price_want = $price ? $price['price'] : 'データが見つかりませんでした。';

    if($price_want<$Savings_want){
        $Savings_want="達成しました!";
    }

} catch (PDOException $e) {
    echo 'DB接続失敗: ' . $e->getMessage();
    $Savings_want = 'DB接続失敗';
    $price_want = 'DB接続失敗';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/main.css?ver2.2.20">
    <title>達成率状況確認</title>
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
        <h1>達成率状況確認</h1>
        <div class="SavingsStatus">
            <h2>あなたの現在の貯金状況です</h2>
        </div>
        <div>
            
        </div>
         <!-- 貯金金額表示 -->
        <div class="h2">
            <h2>貯金金額</h2>
            <h2 class="status"><?php echo htmlspecialchars($Savings_want, ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
        <div> 
            
        </div>
        <!-- 目標金額表示 -->
        <div class="h2">
            <h2>目標金額</h2>
            <p class="goal"><?php echo htmlspecialchars($price_want, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </main>
    <script src="./js/schedule.js"></script>
</body>



</html>