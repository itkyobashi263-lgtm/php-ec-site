<!-- 写真を投稿・表示管理を行う「画像投稿」ページ 
work30_gallery.phpをPDO,関数でリファクタリングしてみましょう。
-->

<?php
// --- データベース接続設定 ---
    const DB_HOST = 'localhost';
    const DB_USER = '********';
    const DB_PASS = '********';
    const DB_NAME = '********';
    const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png'];
//PDOで接続
function get_db_connection() {
    try {
      $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        return new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // エラー時に例外を投げる
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 配列形式で取得
        ]);
    } catch (PDOException $e) {
        exit('データベース接続失敗: ' . $e->getMessage());
    }
}

/* SQL実行共通関数 (挿入・更新用)
 */
function db_execute($pdo, $sql, $params) {
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}


/* 全データ取得*/
function db_select_all($pdo) {
    $sql = "SELECT * FROM images ORDER BY image_id DESC";
    return $pdo->query($sql)->fetchAll();
}

// --- [3] バリデーション・ロジック関数 ---

/**
 * 投稿内容のバリデーション
 */
function validate_upload($title, $file) {
    if (empty($title) || empty($file['name'])) {
        return "タイトル名と画像を選択してください。";
    }
    if (!in_array(mime_content_type($file['tmp_name']), ALLOWED_MIME_TYPES)) {
        return "JPEG/PNG形式のみ投稿可能です。";
    }
    return null; // エラーなし
}
// --- 1. 画像投稿処理 ---
function post_image($pdo, $title, $file) {
  // 要件7: システムが自動でユニークな名前を生成
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_name = uniqid('img_', true) . '.' . $ext;
            
    // 画像本体をバイナリとして読み込む
    $img_binary = file_get_contents($file['tmp_name']);

    // file_nameには名前を、image_dataには画像本体を保存
    $sql = "INSERT INTO images (title, file_name, image_data, public_flg, create_date, update_date) 
                    VALUES (?, ?, ?, 1, CURDATE(), CURDATE())";
    return db_execute($pdo, $sql, [$title, $unique_name, $img_binary]) ;
}

// --- 2. 公開フラグ更新処理 ---
function update_public_flag($pdo, $id, $status) {
    $sql = "UPDATE images SET public_flg = :status, update_date = CURDATE() WHERE image_id = :id";
    return db_execute($pdo, $sql, [$status, $id]) ;
}

// --- [4] 表示関連関数 ---

/**
 * メッセージ表示用HTML生成
 */
function render_message($msg, $type = 'success') {
    if (empty($msg)) return "";
    $class = ($type === 'error') ? 'error-msg' : 'success-msg';
    return "<p class='{$class}'>" . htmlspecialchars($msg) . "</p>";
}

// --- 3. 画像一覧取得 ---
function render_gallery($images) {
    if (empty($images)) return "<p>投稿された画像はありません。</p>";

    $html = '<div class="image-list">';
    foreach ($images as $img) {
        $item_class = $img['public_flg'] ? 'image-item' : 'image-item private';
        $mime = (pathinfo($img['file_name'], PATHINFO_EXTENSION) === 'png') ? 'image/png' : 'image/jpeg';
        $base64 = base64_encode($img['image_data']);
        $title = htmlspecialchars($img['title']);

        $html .= "
        <div class='{$item_class}'>
            <p>タイトル：{$title}</p>
            <img src='data:{$mime};base64,{$base64}' alt='{$title}'>
            <p>状態：" . ($img['public_flg'] ? '公開' : '非表示') . "</p>
            <form action='' method='post'>
                <input type='hidden' name='id' value='{$img['image_id']}'>
                <select name='status'>
                    <option value='1'" . ($img['public_flg'] == 1 ? ' selected' : '') . ">公開</option>
                    <option value='0'" . ($img['public_flg'] == 0 ? ' selected' : '') . ">非表示</option>
                </select>
                <button type='submit' name='update'>状態を更新</button>
            </form>
        </div>";
    }
    $html .= '</div>';
    return $html;
}


// --- メイン処理 ---
$pdo = get_db_connection();
$message = "";
$error = "";

// 投稿リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_image'])) {
    $err = validate_upload($_POST['title'], $_FILES['image']);
    if ($err) {
        $msg_error = $err;
    } else {
        if (post_image($pdo, $_POST['title'], $_FILES['image'])) {
            $msg_success = "正常に投稿されました。";
        }
    }
}

// 更新リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  if (update_public_flag($pdo, $_POST['id'], $_POST['status'])) {
        $msg_success = "公開フラグを更新しました。";
    }
}
$images = db_select_all($pdo);
?>

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
        <?php echo render_message($msg_success, 'success'); ?>
        <?php echo render_message($msg_error, 'error'); ?>

        <form action="" method="post" enctype="multipart/form-data"> 
          <!-- enctype="multipart/form-data"は、ファイルをアップロードする際に必要な属性です。 -->
          <div>
            <label>画像タイトル：</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? '') ?>">
          </div>
          <div style="margin-top:10px;">
            <label>画像：</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png">
          </div>
          <button type="submit" name="post_image" style="margin-top:10px;">画像投稿</button>
        </form>
        <!-- 画像一覧ページへ遷移する事ができる。 -->
        <a href="./work36.php">画像投稿ページへ</a>
      </section>

      <section class="gallery">
        <h2>投稿された画像</h2>
        <?php echo render_gallery($images); ?>
      </section>
    </div>
</body>
</html>
