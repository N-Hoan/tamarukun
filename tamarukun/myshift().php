<?php

$name=filter_input(INPUT_POST,"name");
$time=filter_input(INPUT_POST,"time");
$message=filter_input(INPUT_POST,"message");

  
if (isset($_POST["signUp"])) {

}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./styles/myshift.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <h2>MYSHIFT表</h2>
        <div class="month">      
          <ul>
            <li class="prev">&#10094;</li>
            <li class="next">&#10095;</li>
            <li style="text-align:center">
               6月<br>
              <span style="font-size:18px">JUNE2024</span>
            </li>
          </ul>
        </div>
        <ul class="weekdays">
          <li>sun</li>
          <li>mon</li>
          <li>tue</li>
          <li>wed</li>
          <li>thu</li>
          <li>fri</li>
          <li>sat</li>
        </ul>
       <hr>
        <ul class="days">  
          <li data-day="1"><span>1</span><div class="note"></div></li>
          <li data-day="2"><span>2</span><div class="note"></div></li>
          <li data-day="3"><span>3</span><div class="note"></div></li>
          <li data-day="4"><span>4</span><div class="note"></div></li>
          <li data-day="4"><span class="active">4</span><div class="note"></div></li>
          <li data-day="5"><span>5</span><div class="note"></div></li>
          <li data-day="6"><span>6</span><div class="note"></div></li>
          <li data-day="7"><span>7</span><div class="note"></div></li>
          <li data-day="8"><span>8</span><div class="note"></div></li>
          <li data-day="9"><span>9</span><div class="note"></div></li>
          <li data-day="10"><span>10</span><div class="note"></div></li>
          <li data-day="11"><span>11</span><div class="note"></div></li>
          <li data-day="12"><span>12</span><div class="note"></div></li>
          <li data-day="13"><span>13</span><div class="note"></div></li>
          <li data-day="14"><span>14</span><div class="note"></div></li>
          <li data-day="15"><span>15</span><div class="note"></div></li>
          <li data-day="16"><span>16</span><div class="note"></div></li>
          <li data-day="17"><span>17</span><div class="note"></div></li>
          <li data-day="18"><span>18</span><div class="note"></div></li>
          <li data-day="19"><span>19</span><div class="note"></div></li>
          <li data-day="20"><span>20</span><div class="note"></div></li>
          <li data-day="21"><span>21</span><div class="note"></div></li>
          <li data-day="22"><span>22</span><div class="note"></div></li>
          <li data-day="23"><span>23</span><div class="note"></div></li>
          <li data-day="24"><span>24</span><div class="note"></div></li>
          <li data-day="25"><span>25</span><div class="note"></div></li>
          <li data-day="26"><span>26</span><div class="note"></div></li>
          <li data-day="27"><span>27</span><div class="note"></div></li>
          <li data-day="28"><span>28</span><div class="note"></div></li>
          <li data-day="29"><span>29</span><div class="note"></div></li>
          <li data-day="30"><span>30</span><div class="note"></div></li>
          <li data-day="31"><span>31</span><div class="note"></div></li>
        </ul>
      </div>


      <dialog id="dialog" class="dialog">
        <div id="dialog-container" class="dialog-container">
        <form action="" method="POST" class="add-form"></form>
                <h2>詳細</h2>
                <p>時間: <input type="text" id="memoInput" name="time"></p>
                <p>勤務先: <input type="text" id="nameInput" name="name"></p>
                <p>メッセージ: <input type="text" id="messageInput" name="message"></p>
                <div class="button-container">
                    <button type="submit" name="regist" id="saveAppointment">保存</button>
                    <button id="dialog-close-btn">閉じる</button>
                </div>
            </form>
        </div>
        </div>
      </dialog>
      <script src="./styles/sample.js"></script>
    <script src="./styles/myshift.js"></script>
</body>
</html> 