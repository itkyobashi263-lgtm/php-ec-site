<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>WORK08</title>
</head>
<body>
    <?php 
    //①ランダムに生成された1~100の正の整数が判定され、下記のように表示される。
      $score = rand(1, 100);
      echo "<p>$score</p>";
      switch ($score % 6) {
        case 0:
          // 余りが0の場合（例：6, 12, 18...）
          echo "<p>3と6の倍数です</p>";
          break;
        case 3:
          // 余りが3の場合（例：3, 9, 15...）
          echo "<p>3の倍数で、6の倍数ではありません</p>";
          break;
        default:
          // 余りが1, 2, 4, 5の場合（3の倍数ではない）
          echo "<p>倍数ではありません</p>";
          break;
        }
//② ランダムに生成された1~10の正の整数（random01, random02）が判定され、下記の例ように(1)生成された数(2)どちらが大きいか(3)3の倍数の個数が表示される。
      $random01 = rand(1, 10);
      $random02 = rand(1, 10);
      $message1 = "random01 = {$random01}, random02 = {$random02} です。";
      switch (true) {
        case $random01 > $random02: // 結果が正 (random01 > random02)
          $message1 .= "random01の方が大きいです。";
          break;
        case $random01 < $random02: // 結果が負 (random01 < random02)
          $message1 .= "random02の方が大きいです。";
         break;
        default: // 結果が0 (同じ)
          $message1 .= "2つは同じ数です。";
          break;
      }

      // 3. 3の倍数の個数をカウント (条件式を数値化して足し算)
       $count = ($random01 % 3 === 0) + ($random02 % 3 === 0);

          // 4. 個数に応じたメッセージ分岐
      switch ($count) {
        case 0:
          $message2 = "2つの数字のうち、3の倍数となる数はありません。";
          break;
        case 1:
          $message2 = "2つの数字のうち、3の倍数となる数字は1つです。";
          break;
        case 2:
          $message2 = "2つの数字のうち、3の倍数となる数字は2つです。";
          break;
      }
        echo "<p>{$message1}{$message2}</p>";
    ?> 
</body>
</html>