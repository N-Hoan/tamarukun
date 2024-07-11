<?php
session_start();

$dsn = 'mysql:host=localhost;dbname=zenitamaru';
$db_user = 'root';
$db_password = "root";

if (isset($_SESSION['user_ID'])) {
  $userID = $_SESSION['user_ID'];
} else {
  die("セッションにユーザーIDが設定されていません");
}

try {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームデータを取得
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // statusが数値であることを確認
    if (!is_numeric($status)) {
      die("貯金金額は数値でなければなりません");
    }

    // PDO接続を作成する
    $PDO = new PDO($dsn, $db_user, $db_password);
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 現在のstatus値を取得
    $sql = "SELECT status FROM want WHERE userID = :userID";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $currentStatus = $stmt->fetchColumn();

    if ($currentStatus === false) {
      die("ユーザーIDが見つかりません");
    }

    // 新しいstatus値を計算
    $newStatus = $currentStatus + $status;

    // SQL クエリの準備
    $sql = "UPDATE want SET status = :newStatus WHERE userID = :userID";
    $stmt = $PDO->prepare($sql);

    // パラメータをバインドしてクエリを実行
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_INT);

    if ($stmt->execute()) {
      header("Location: ./main.php");
      exit;
    } else {
      echo "登録に失敗しました";
    }
  }
} catch (PDOException $e) {
  echo "エラー: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/addmoney.css">
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
        <li>
        <li><img src="/./img/login.jpg" alt="login"><a href="./login.php">ログイン</a></li>
                <li><img src="/img/acount.png" alt="acount"><a href="./infochange.php">アカウント情報変更</a></li>
                <li class="admin-menu"><img src="/img/list.jpg" alt="list"><a href="./manager.php">ほしいものリスト</a></li>
                <li class="admin-menu"><img src="/img/gold.png" alt="貯金金額"><a href="./addmoney.php">貯金金額画面</a></li>
                <li class="admin-menu"><img src="/img/home.png" alt="home"><a href="./main.php">達成状況状況確認</a> </li>
                <li class="admin-menu"><img src="/img/shift.jpeg" alt="shift"><a href="./myshift.php">シフトボード</a></li>
                <li><img src="/img/logout.jpg" alt="logout"><a href="./logout.php" id="logout">ログアウト</a></li>
        </ul>
      </div>
    </div>
  </header>
  <main>
    <div class="main-contents">
      <div class="content">
        <h1 class="center">貯金金額画面</h1>
        <hr>
        <form method="post" id="registrationForm">
          <div class="center input-item">
            <label for="status">
              <textarea id="status" name="status" placeholder="貯金金額を入力してください" required></textarea>
            </label>
          </div>
          <div class="submit">
            <input type="submit" id="submit" value="登録">
          </div>
      </div>
      </form>
    </div>
    </div>
  </main>
  <script src="./js/addmoney.js"></script>
</body>
</html>