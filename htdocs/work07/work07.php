<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>WORK07</title>
</head>
<body>
    <?php
    /* ①ランダムに生成された1~100の正の整数が判定され、下記のように表示される。
3の倍数かつ6の倍数である場合：「3と6の倍数です」と表示
3の倍数であるが、6の倍数ではない場合：「3の倍数で、6の倍数ではありません」と表示
3の倍数ではない場合：「倍数ではありません」と表示 */
     $score = rand(1, 100);
     ?>
    <p><?php echo "<p> $score </p>"; ?></p>
    <?php if ($score % 6 === 0) : ?>
        <p>3と6の倍数です</p>
    <?php elseif ($score % 3 === 0) : ?>
        <p>3の倍数で、6の倍数ではありません</p>
    <?php else : ?>
        <p>倍数ではありません</p>
    <?php endif; ?>

<!-- ランダムに生成された1~10の正の整数（random01, random02）が判定され、下記の例ように(1)生成された数(2)どちらが大きいか(3)3の倍数の個数が表示される。
違う数の場合「random01 = 3, random02 = 4 です。 random02の方が大きいです。2つの数字の中には3の倍数が1つ含まれています。」
同じ数の場合「random01 = 3, random02 = 3 です。 2つは同じ数です。2つの数字の中には3の倍数が2つ含まれています。」
3の倍数が含まれていない場合、後半の文章は「2つの数字の中に3の倍数が含まれていません」と表示 -->
     <?php
      $random01 = rand(1, 10);
      $random02 = rand(1, 10);
      $message1 = "random01 = {$random01}, random02 = {$random02} です。";
      ?>
      <?php if ($random01 > $random02) : ?>
        <?php $message1 .= "random01の方が大きいです。"; ?>
      <?php elseif ($random01 < $random02) : ?>
        <?php $message1 .= "random02の方が大きいです。"; ?>
      <?php else : ?>
        <?php $message1 .= "2つは同じ数です。"; ?>
      <?php endif; ?>
      <!-- 3. 3の倍数の個数をカウント (条件式を数値化して足し算) -->
       <?php
      $count = ($random01 % 3 === 0) + ($random02 % 3 === 0);
      ?>
      <!-- 4. 個数に応じたメッセージ分岐 -->
      <?php if ($count == 0) : ?>
        <?php $message2 = "2つの数字のうち、3の倍数となる数はありません。"; ?>
      <?php elseif ($count == 1) : ?>
        <?php $message2 = "2つの数字のうち、3の倍数となる数字は1つです。"; ?>
      <?php else : ?>
        <?php $message2 = "2つの数字のうち、3の倍数となる数字は2つです。"; ?>
      <?php endif; ?>
      <p><?php echo $message1.$message2 ?></p>
</body> 
</html>