<!-- 投稿された写真を閲覧する「画像一覧」ページ 
1:公開管理フラグが「公開」になっている投稿された画像を一覧で表示する。
2:画像はタイトル名とともに表示し、タイトル名が分かるようにする。
3:画像投稿ページへ遷移する事ができる。
-->
<?php
// --- データベース接続設定 ---
    $host = 'localhost';
    $login_user = '********';
    $password = '********';
    $database = '********';

    $link = mysqli_connect($host, $login_user, $password, $database);
    if (!$link) {
        exit('データベース接続失敗: ' . mysqli_connect_error());
    }
    mysqli_set_charset($link, 'utf8');

    // 1: 公開管理フラグが「公開」のものを取得（status=1と仮定）
    $sql = "SELECT title, file_name, image_data FROM images WHERE public_flg = 1 ORDER BY image_id DESC";
    $result = mysqli_query($link, $sql);
    // ※取得したデータ（行数）を確認
    $count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>画像一覧<</title>
    <style>
        .gallery { display: flex; flex-wrap: wrap; gap: 20px; }
        .item { width: calc(25% - 15px); /* 4枚横並びの計算 */ border: 1px solid #ccc; padding: 10px; text-align: center; }
        .item img { max-width: 100%; height: auto; display: block; margin-bottom: 5px; }
        .btn-post { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <section class="top">    
            <h1>画像一覧</h1>
            <!-- 画像投稿ページへ遷移する事ができる。 -->
            <a href="./work30_gallery.php">画像投稿ページへ</a>
        </section>
        <!-- ここに画像を表示するコードを追加 -->
        <div class="gallery">
            <?php if ($count > 0): ?>
                 <!--mysqli_fetch_assocを繰り返し呼び出して1行ずつ取り出す -->
                <?php while ($img = mysqli_fetch_assoc($result)): ?>
                    <div class="item">
                        <p><?php echo htmlspecialchars($img['title']) ?></p>
                        <?php if ($img['image_data'] !== null): ?>
                          <?php
                            // 画像データをブラウザで表示するためにbase64エンコード
                            $ext = strtolower(pathinfo($img['file_name'], PATHINFO_EXTENSION));
                            $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                          ?>
                          <img src="data:<?php echo $mime; ?>;base64,<?php echo base64_encode($img['image_data']); ?>" alt="<?= htmlspecialchars($img['title']) ?>">
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>公開されている画像はありません。</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($link); ?>



