<!-- 写真を投稿・表示管理を行う「画像投稿」ページ 
1:「タイトル名」「画像」を入力し、画像を投稿することができる。
2: 投稿された画像の「タイトル名」「画像」を一覧で表示する。
3: 一覧から指定した画像の公開フラグを「公開」「非公開」に変更することができる。
4: 画像の投稿もしくは指定した画像の公開フラグが正常に更新された場合、完了のメッセージを表示する。
5: 画像投稿ボタンクリック時、「タイトル名」「画像」が設定されていない場合、エラーメッセージを表示し、画像投稿されない。
6: 投稿できる画像の形式は「JPEG」「PNG」のみとする。「JPEG」「PNG」以外の場合、エラーメッセージを表示し、画像投稿されない。
7: 「ファイル名」は、元のファイル名とは別に、システムが自動でユニークな名前を生成することで、重複しないようにします。
8: 非表示の場合、背景色を変更し、表示・非表示の状態が見てわかるようにする。
9: 画像一覧ページへ遷移する事ができる。
  -->
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
//PHPの標準的な命令（mysqliのプロシージャル形式）
$link = mysqli_connect($host, $login_user, $password, $database);
if (!$link) {
  exit('データベース接続失敗: ' . mysqli_connect_error());
}
mysqli_set_charset($link, 'utf8');

// 簡易的なメッセージ管理
$message = "";
$error = "";

// --- 1. 画像投稿処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_image'])) {
    $title = $_POST['title'];
    $file = $_FILES['image'];

    // 未入力チェック（要件5）
    if ($title === '' || $file['name'] === '') {
        $error = "タイトル名と画像を選択してください。";
    } else {
        $mime = mime_content_type($file['tmp_name']);
        // 形式チェック（要件6）
        if (($mime !== 'image/jpeg') && ($mime !== 'image/png')) {
            $error = "JPEG/PNG形式のみ投稿可能です。";
        } else {
            // 要件7: システムが自動でユニークな名前を生成
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $unique_name = uniqid('img_', true) . '.' . $ext;
            
            // 画像本体をバイナリとして読み込む
            $img_binary = file_get_contents($file['tmp_name']);

            // // SQL用にエスケープ処理
            $safe_title = mysqli_real_escape_string($link, $title);
            $safe_name = mysqli_real_escape_string($link, $unique_name);
            $safe_data = mysqli_real_escape_string($link, $img_binary);

            //de保存
            $sql = "INSERT INTO images (title, file_name, image_data, public_flg, create_date, update_date) 
                    VALUES ('$safe_title', '$safe_name', '$safe_data', 1, CURDATE(), CURDATE())";
            if (mysqli_query($link, $sql)) {
                $message = "正常に投稿されました。";
            } else {
                $error = "データベースへの保存に失敗しました。";
            }
        }
    }
}

// --- 2. 公開フラグ更新処理 ---
if (isset($_POST['update'])) {
  $id = mysqli_real_escape_string($link, $_POST['id']);
    $new_status = mysqli_real_escape_string($link, $_POST['status']);
    
    $sql = "UPDATE images SET public_flg = $new_status, update_date = CURDATE() WHERE image_id = $id";
    if (mysqli_query($link, $sql)) {
        $message = "公開フラグを更新しました。";
    } else {
        $error = "公開フラグの更新に失敗しました。";
    }
}

// --- 3. 画像一覧取得 ---
$sql = "SELECT * FROM images ORDER BY image_id DESC";
$result = mysqli_query($link, $sql);
?>

    <!-- ここに画像を投稿するコードを追加 -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>画像一覧</title>
    <style>
        .container { width: 90%; margin: 0 auto; }
        /* 写真を並べる親要素 */
        .image-list {
          display: flex;         /* 横並びにする */
          flex-wrap: wrap;       /* 画面幅が狭くなったら折り返す */
          gap: 20px;             /* 画像間の隙間 */
        }

          /* 各画像アイテムの幅調整 */
        .image-item {
           width: calc(33.33% - 20px); /* 3枚並べる設定（隙間分を引く） */
          order: 1px solid #ccc;
          padding: 15px;
          border-radius: 5px;
          box-sizing: border-box; /* パディングを幅に含める */
        }

        /* 画像が枠からはみ出ないように調整 */
        .image-item img {
          max-width: 100%;
          height: auto;
          display: block;
          margin: 10px auto;
        }
        .private { background-color: #f0f0f0; color: #888; } /* 非表示時の背景色 */
        .error { color: #d9534f; font-weight: bold; }
        .success { color: #5cb85c; font-weight: bold; }
        img { max-width: 300px; display: block; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
      <section class="uploadForm">
        <h1>画像投稿</h1>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($message): ?><p class="success"><?= htmlspecialchars($message) ?></p><?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data"> 
          <!-- enctype="multipart/form-data"は、ファイルをアップロードする際に必要な属性です。 -->
          <div>
            <label>画像タイトル：</label>
            <input type="text" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
          </div>
          <div style="margin-top:10px;">
            <label>画像：</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png">
          </div>
          <button type="submit" name="post_image" style="margin-top:10px;">画像投稿</button>
        </form>
        <!-- 画像一覧ページへ遷移する事ができる。 -->
        <a href="./work30.php">画像投稿ページへ</a>
      </section>

      <section class="gallery">
        <h2>投稿された画像</h2>
        <?php if (mysqli_num_rows($result) === 0): ?>
          <p>投稿された画像はありません。</p>
        <?php else: ?>
          <div class="image-list">
            <?php while ($img = mysqli_fetch_assoc($result)): ?>
            <!-- 要件8: public_flgを見てクラスを切り替え -->
              <div class="image-item <?= $img['public_flg'] ? '' : 'private' ?>">
                <p>タイトル：<?= htmlspecialchars($img['title']) ?></p>
                <?php if ($img['image_data'] !== null): ?>
                  <?php
                  // 画像データをブラウザで表示するためにbase64エンコード
                  $ext = strtolower(pathinfo($img['file_name'], PATHINFO_EXTENSION));
                  $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                  $base64 = base64_encode($img['image_data']);
                  ?>
                  <img src="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
                <?php endif; ?>
                
                <p>状態：<?= $img['public_flg'] ? '公開' : '非表示' ?></p>
                <form action="" method="post">
                  <input type="hidden" name="id" value="<?= $img['image_id'] ?>">
                  <select name="status">
                    <option value="1" <?= $img['public_flg'] ? 'selected' : '' ?>>公開</option>
                    <option value="0" <?= !$img['public_flg'] ? 'selected' : '' ?>>非表示</option>
                  </select>
                  <button type="submit" name="update">状態を更新</button>
                </form>
              </div>
            <?php endwhile; ?>
          </div>
        <?php endif; ?>
      </section>
    </div>
</body>
</html>
<?php mysqli_close($link); ?>
