<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>WORK15</title>
</head>
<body>
    <?php
    /* 二つの教室を想定した配列class01とclass02を定義し、class01には「tokugawa」「oda」「toyotomi」「takeda」、class02には「minamoto」「taira」「sugawara」「fujiwara」の4名ずつに、それぞれテストの点数として1〜100の乱数を持たせてください。*/
      $class01 = array(
        'tokugawa' => rand(1, 100),
        'oda' => rand(1, 100),
        'toyotomi' => rand(1, 100),
        'takeda' => rand(1, 100)
      );
      $class02 = array(
      'minamoto' => rand(1, 100),
      'taira' => rand(1, 100),
      'sugawara' => rand(1, 100),
      'fujiwara' => rand(1, 100)
      );
      // class01とclass02をschoolという配列に格納してください。
      $school = array($class01, $class02);
/* class01のodaさんの点数と、class02のsugawaraさんの点数を比較し、それぞれの点数とどちらの点数が高いかを出力させてください。それぞれの値はschool配列から取得してください。   */
      $oda_score = $school[0]['oda'];
      $sugawara_score = $school[1]['sugawara'];
      echo "odaさんの点数: " . $oda_score . "<br>";
      echo "sugawaraさんの点数: " . $sugawara_score . "<br>";
      if ($oda_score > $sugawara_score) {
        echo "odaさんの方が高い点数です。";
      } elseif ($oda_score < $sugawara_score) {
        echo "sugawaraさんの方が高い点数です。";
      } else {
        echo "odaさんとsugawaraさんは同じ点数です。";
      }
      //改行
      
      echo "<br>";
    /* class01・class02それぞれの平均点を出力させてください。それぞれの値はschool配列から取得してください。 */
      echo "class01の平均点: " . array_sum($school[0]) / count($school[0]) . "<br>";
      echo "class02の平均点: " . array_sum($school[1]) / count($school[1]) . "<br>";
    ?>
</body>
</html>