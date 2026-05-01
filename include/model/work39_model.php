<?php
// DB接続設定[cite: 3, 4]
const DB_HOST = 'localhost';
const DB_USER = '********';
const DB_PASS = '********';
const DB_NAME = '********';

class Work39Model {
    private $pdo;

    public function __construct() {
        $this->pdo = $this->get_db_connection();
    }

    // データベース接続[cite: 3, 4]
    private function get_db_connection() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            return new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            exit('データベース接続失敗: ' . $e->getMessage());
        }
    }

    // 画像取得：公開のみ
    public function get_images($only_public = false) {
    if ($only_public === true) {
        // 閲覧ページ用：公開のみ取得
        $sql = "SELECT * FROM images WHERE public_flg = 1 ORDER BY image_id DESC";
    } else {
        // 管理ページ用：非表示も含め全件取得
        $sql = "SELECT * FROM images ORDER BY image_id DESC";
    }
    return $this->pdo->query($sql)->fetchAll();
}
    // 投稿バリデーション[cite: 4]
    public function validate_upload($title, $file) {
        if (empty($title) || empty($file['name'])) {
            return "タイトル名と画像を選択してください。";
        }
        $allowed = ['image/jpeg', 'image/png'];
        if (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
            return "JPEG/PNG形式のみ投稿可能です。";
        }
        return null;
    }

    // 画像投稿処理[cite: 4]
    public function post_image($title, $file) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_name = uniqid('img_', true) . '.' . $ext;
        $img_binary = file_get_contents($file['tmp_name']);

        $sql = "INSERT INTO images (title, file_name, image_data, public_flg, create_date, update_date) 
                VALUES (?, ?, ?, 1, CURDATE(), CURDATE())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $unique_name, $img_binary]);
    }

    // 公開フラグ更新処理[cite: 4]
    public function update_status($id, $status) {
        $sql = "UPDATE images SET public_flg = :status, update_date = CURDATE() WHERE image_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}