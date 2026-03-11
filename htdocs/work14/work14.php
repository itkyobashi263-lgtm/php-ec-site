<?php
/* 配列に5つの乱数（1〜100）を格納します。ここでは、まとめて宣言するのではなく、繰り返しとarray_push()メソッド（マニュアルはこちら）を使用してください。 */
  $numbers = array();
  for ($i = 0; $i < 5; $i++) {
    array_push($numbers, rand(1, 100));
  }

/* 格納した配列の各要素を検証し、偶数である場合は「20（偶数）」奇数である場合は「25（奇数）」（20, 25はそれぞれ例としての仮の数）と、一つずつ要素を表示します。 */
  foreach ($numbers as $number) {
    if ($number % 2 == 0) {
      echo $number . '（偶数）';
    } else {
      echo $number . '（奇数）';
    }
  }

?>