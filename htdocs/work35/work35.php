<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TRY49</title>
</head>
<body>
    <?php 

    //1~10の乱数を引数として渡し、引数が偶数の場合はその数に10を掛け、奇数の場合は100を掛けて、計算結果を返す関数を作成
    //関数を呼び出し、echoなどで画面上に表示させる
    $random_num = rand(1, 10);
    $result = calculate_result($random_num);
    echo "<p>引数：".$random_num."、計算結果：".$result."</p>";
    function calculate_result($num){
        if($num % 2 == 0){
            return $num * 10;
        } else {
            return $num * 100;
        }
    }
    
    ?>
</body>
</html>