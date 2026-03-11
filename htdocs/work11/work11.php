<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>WORK11</title>
  </head>
  <body>
    <?php
    // 1. 1から100までの数を順番に表示 (条件分岐でFizz, Buzz, Fizz Buzzを表示)
    for ($i = 1; $i <= 100; $i++) :
      if ($i % 12 === 0) :
        echo "Fizz Buzz<br>";
      elseif ($i % 3 === 0) :
        echo "Fizz<br>";
      elseif ($i % 4 === 0) :
        echo "Buzz<br>";
      else :
        echo "$i<br>";
      endif;
    endfor;
    echo "<br>"; // 区切りの改行
    // 2. 九九を表示 (二重ループで1*1から9*9まで)
    for ($j = 1; $j <= 9; $j++) :
      for ($k = 1; $k <= 9; $k++) :
        echo "$j * $k = " . ($j * $k) . "<br>";
      endfor;
    endfor;
    echo "<br>";
    /* 3: 2の下に、下記のように、「*」と「!」が交互に表示され、「*」は行ごとに1つずつ数をふやしていくプログラム */
      for ($i = 1; $i <= 10; $i++) :
        $line1 = '';
        for ($j = 0; $j < $i; $j++) :
          $line1 .= '*';
        endfor;
        $line2 = '!';
        echo "<p>$line1<br>$line2</p>";
      endfor;
    ?>
  </body>
</html>