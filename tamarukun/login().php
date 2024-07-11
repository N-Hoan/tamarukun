<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./styles/common().css">
  <link rel="stylesheet" href="../styles/header.css">
  <title>login</title>
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
              <li><img src="/img/login.jpg" alt="login"><a href="/tamarukun/html/login.html">ログイン</a></li>
              <li><img src="/img/acount.png" alt="acount"><a href="/tamarukun/html/infochange.html">アカウント情報変更</a></li>
              <li class="admin-menu"><img src="/img/home.png" alt="home"><a href="./manager.html">達成状況確認画面</a></li> <!-- 完成ファイルがないのである人入れ替えてください。 -->
              <li class="admin-menu"><img src="/img/list.jpg" alt="list"><a href="./manager.html">ほしいものリスト</a></li> <!-- 完成ファイルがないのである人入れ替えてください。 -->
              <li class="admin-menu"><img src="/img/gold.png" alt="貯金金額"><a href="/tamarukun/html/addmoney.html">貯金金額画面</a></li>
              <li class="admin-menu"><img src="/img/shift.jpeg" alt="shift"><a href="./correction.html">シフトボード</a></li>
              <li><img src="/img/logout.jpg" alt="logout"><a href="/tamarukun/html/login.html" id="logout">ログアウト</a></li>
            </ul>
          </div>
      </div>
  </header>
  <main>
    <div class="main-contents">
      <div class="content">
        <h1 class="center">アカウントログイン</h1>
        <hr>
        <form id="registrationForm" method="POST">
          <div class="wrap-box flex-even">
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
            <div class="submit">
              <input type="submit" id="submit" value="ログイン">
            </div>
            <div><a href="./adduser.html">新規登録はこちら</a></div>
          </div>
        </form>
      </div>
    </div>
  </main>
  <script src="./styles/humberger.js"></script>
  <script>
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        window.location.href = './style/main.html'; // Thay đổi thành trang bạn muốn chuyển hướng tới
    });
    
</script>
</body>

</html>