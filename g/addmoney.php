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
            <a href="./adduser.php">ログイン</a></li>
            <li><a href="./list.php">アカウント情報変更</a></li>
            <li class="admin-menu"><a href="./manager.php">ほしいものリスト</a></li>
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
        <form method="post" id="registrationForm">
          <div class="center input-item">
            <label for="addmoney">
              <textarea id="status_message" name="message" placeholder="貯金金額を入力してください" required></textarea>
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