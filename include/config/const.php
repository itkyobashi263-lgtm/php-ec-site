<?php
// ==========================================
// ECサイト 共通設定 (定数定義ファイル)
// ==========================================

/* データベース接続設定 */
define('DB_HOST', 'localhost');         // データベースのホスト名
define('DB_NAME', '********');    // データベース名
define('DB_USER', '********');    // データベース接続ユーザー名
define('DB_PASS', '********');        // データベース接続パスワード
define('DB_CHARSET', 'utf8mb4');        // データベースの文字コード

/* デフォルトの管理者アカウント情報 */
define('ADMIN_USER', 'ec_admin');       // 管理者用ユーザー名
define('ADMIN_PASS', 'ec_admin');       // 管理者用パスワード

/* アプリケーション固有の設定 */
define('UPLOAD_DIR', 'uploads');        // 商品画像のアップロード先ディレクトリ名
define('MAX_USERNAME_LEN', 50);         // ユーザー名の最大文字数制限
define('MAX_PASSWORD_LEN', 255);        // パスワードの最大文字数制限



