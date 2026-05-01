<!-- 投稿された写真を閲覧する「画像一覧」ページ 
1:公開管理フラグが「公開」になっている投稿された画像を一覧で表示する。
2:画像はタイトル名とともに表示し、タイトル名が分かるようにする。
3:画像投稿ページへ遷移する事ができる。

-->

<?php
// --- データベース接続設定 ---
    define('DB_HOST', 'localhost');
    define('DB_USER', '********');
    define('DB_PASS', '********');
    define('DB_NAME', '********');


//クラス化
class imageManager {
    private $pdo;
    public function __construct() {
        $this->pdo = $this->get_db_connection();
    }
//PDOで接続
    private function get_db_connection() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            return new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            exit('データベース接続失敗: ' . $e->getMessage());
        }
    }

    // 共通クエリ実行機能 (データ取得・挿入・更新を統合)
    private function db_query($sql, $params = [], $isSelect = true) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $isSelect ? $stmt->fetchAll() : $stmt->rowCount();
    }

    // 1: 公開管理フラグが「公開」のものを取得（status=1と仮定）
    public function get_public_images() {
      $sql = "SELECT * FROM images WHERE public_flg = 1 ORDER BY image_id DESC";
      return $this->db_query($sql);
    }
// 画像データをBase64エンコード
    public function encode_image_data($data) {
      return 'data:image/jpeg;base64,' . base64_encode($data);
    }
}


// --- 2. 表示用ヘルパー関数 ---

// メッセージ表示機能
function display_message($messages, $type = 'error') {
    if (empty($messages)) return '';
    $color = ($type === 'success') ? '#28a745' : '#dc3545';
    $html = "<ul style='color: $color;'>";
    foreach ((array)$messages as $msg) {
        $html .= "<li>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "</li>";
    }
    return $html . "</ul>";
}

// 画像一覧表示機能
function render_gallery($images) {
    if (empty($images)) return '<p>画像がありません。</p>';
    
    $html = '<div class="gallery" style="display: flex; flex-wrap: wrap; gap: 20px;">';
    foreach ($images as $img) {
        $base64 = base64_encode($img['image_data']);
        $title = htmlspecialchars($img['title'], ENT_QUOTES, 'UTF-8');
        $html .= "<div class='item' style='width: 200px; text-align: center; border: 1px solid #ccc; padding: 10px;'>";
        $html .= "<p><strong>$title</strong></p>";
        $html .= "<img src='data:image/jpeg;base64,$base64' style='max-width: 100%;'>";
        $html .= "</div>";
    }
    return $html . '</div>';
}
  // 実行ロジック
$manager = new imageManager();
$errors = [];
$success = "";
$images = $manager->get_public_images();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>画像一覧</title>
    <style>
        .gallery { display: flex; flex-wrap: wrap; gap: 20px; }
        .item { width: 200px; border: 1px solid #ccc; padding: 10px; text-align: center; }
        .item img { max-width: 100%; height: auto; display: block; margin-bottom: 5px; }
        .btn-post { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <section class="top">    
            <h1>画像一覧</h1>
            <!-- 画像投稿ページへ遷移する事ができる。 -->
            <a href="./work36_gallery.php">画像投稿ページへ</a>
        </section>
        <!-- ここに画像を表示するコードを追加 -->
        <div class="gallery">
        <!-- 画像一覧表示 -->
        <?php echo render_gallery($images) ?>
        </div>
    </div>
</body>
</html>



