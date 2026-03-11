<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>WORK13</title>
  </head>
  <body>
    <?php
    // 1:1から100までの数を順番に表示します。ただし、3の倍数である場合は、数字の代わりに「Fizz」、4の倍数である場合は、数字の代わりに「Buzz」、3と4の倍数である場合は、数字の代わりに「Fizz Buzz」と表示させてください。
      $i = 1;
      while ($i <= 100) :
        if ($i % 12 === 0) :
          echo '<p>Fizz Buzz</p>';
        elseif ($i % 3 === 0) :
          echo '<p>Fizz</p>';
        elseif ($i % 4 === 0) :
          echo '<p>Buzz</p>';
        else :
          echo "<p>$i</p>";
        endif;
        $i++;
      endwhile;
      echo "<br>"; // 区切りの改行
      // 2:九九が1 * 1 = 1…… 9 * 9 = 81という形式で表示されるプログラムを書いてください。（ヒント：二重ループ）
      $ii = 1;
      while ($ii <= 9) :
        $j = 1;
        while ($j <= 9) :
          echo "<p>$ii * $j = " . ($ii * $j) . "</p>";
          $j++;
        endwhile;
        $ii++;
      endwhile;
      echo "<br>"; // 区切りの改行
      // 3:コンソールで、下記のように、「*」と「!」が交互に表示され、「*」は行ごとに1つずつ数をふやしていくプログラムを書いてください。
      $iii = 1;
      while ($iii <= 10) :
        $line1 = '';
        $j = 0;
        while ($j < $iii) :
          $line1 .= '*';
          $j++;
        endwhile;
        $line2 = '!';
        echo "<p>$line1<br>$line2</p>";
        $iii++; 
      endwhile
    ?>
  </body>
</html>