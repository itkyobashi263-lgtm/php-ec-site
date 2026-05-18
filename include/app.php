<?php
// 共通処理: app.phpを作成して、全ページで共通の処理や関数をまとめる
// データベース接続、セッション管理、共通関数の定義などを行う
// session_start()を呼び出して、セッションを開始する
session_start();

// モデルとビューの読み込み: ec_site_model.phpとec_site_view.phpをrequire_onceで読み込む
require_once __DIR__ . '/model/ec_site_model.php';
require_once __DIR__ . '/view/ec_site_view.php';

// データベース接続の確立: PDOを使ってデータベースに接続し、変数 $pdo に接続オブジェクトを保存する
$pdo = get_connection();

// データベースの初期化: 必要なテーブルが存在しない場合は作成し、初期設定を行う
init_ec_tables($pdo);

// アップロードディレクトリの作成: UPLOAD_DIR定数で指定されたディレクトリが存在しない場合は、mkdir()関数を使って作成する
$uploadPath = __DIR__ . '/../htdocs/ec_site/' . UPLOAD_DIR;
if (!is_dir($uploadPath)) {
  mkdir($uploadPath, 0755, true);
}

// グローバル変数の初期化: $message、$error、$checkoutResultなどのグローバル変数を初期化する
$message = '';
$error = '';
$checkoutResult = null;

/** is_login()やis_admin()などの共通関数の定義: ユーザーのログイン状態や管理者権限を判定する関数を定義する */
function is_login() {
  return !empty($_SESSION['user']);
} 
function is_admin() {
  return is_login() && $_SESSION['user']['user_name'] === ADMIN_USER;
}


/**
 * to_page()関数の定義: 指定されたURLにリダイレクトする関数を定義する 
 * @param string $file リダイレクト先のURL
 */

function to_page($file) {
  header('Location: ' . $file);
  exit;
}


// POSTリクエストの処理: $_POST['action']の値に応じて、ログイン、ユーザー登録、商品追加、カート操作などの処理を行う
$action = $_POST['action'] ?? '';

// ログアウト処理: actionがlogoutの場合は、セッションを破棄して、login.phpにリダイレクトする
if ($action === 'logout') {
  $_SESSION = [];
  session_destroy();
  to_page('login.php');
}

// ログイン状態のチェック:  actionがloginの場合はユーザーがログインしているかどうかを判定する
if ($action === 'login') {
  $name = trim($_POST['user_name'] ?? '');
  $pass = trim($_POST['password'] ?? '');
  $user = find_user_by_name($pdo, $name);
  /* 
   * パスワード認証: 
   * 入力されたパスワードとデータベースのハッシュ化パスワードを照合する。
   * 成功した場合はセッションにユーザー情報を保存し、権限に応じたページへ遷移する。
   */
  if ($user && password_verify($pass, $user['password'])) {
    $_SESSION['user'] = ['user_id' => (int)$user['user_id'], 'user_name' => $user['user_name']];
    if ($user['user_name'] === ADMIN_USER) {
      to_page('admin.php');
    }
    to_page('products.php');
  }
  $error = 'ユーザー名またはパスワードが一致しません。';
}

// ユーザー登録処理: actionがregisterの場合は、ユーザー名とパスワードのバリデーションを行い、問題がなければcreate_user()関数を呼び出してユーザーを作成する
if ($action === 'register') {
  $name = trim($_POST['user_name'] ?? '');
  $pass = trim($_POST['password'] ?? '');
  $e1 = validate_username($name);
  $e2 = validate_password($pass);
  /* ユーザー名は英数字とアンダースコアのみ、5文字以上。パスワードは英数字とアンダースコアのみ、8文字以上。ユーザー名が既に存在する場合はエラーとする。エラーがない場合はcreate_user()関数を呼び出してユーザーを作成し、成功メッセージを設定する */
  if ($e1 !== '') {$error = $e1;}
  if ($error === '' && $e2 !== '') { $error = $e2; }
  if ($error === '' && find_user_by_name($pdo, $name)) {
    $error = 'このユーザー名は既に使用されています。';
  }
  if ($error === '') {
    create_user($pdo, $name, $pass);
    $message = 'ユーザー登録が完了しました。ログインしてください。';
  }
}

// 商品追加処理: actionがadd_productの場合は、商品名、値段、在庫数、公開ステータス、商品画像のバリデーションを行い、問題がなければcreate_product()関数を呼び出して商品を作成する
if ($action === 'add_product' && is_admin()) {
  $name = trim($_POST['product_name'] ?? '');
  $price = $_POST['price'] ?? '';
  $stock = $_POST['stock_qty'] ?? '';
  $publicFlg = (int)($_POST['public_flg'] ?? 0);
  $file = $_FILES['image'] ?? null;

  /* 
   * 入力値のバリデーション (検証):
   * すべての項目が入力されているか、数値が正しいか、画像ファイルが適切な形式かをチェックする。
   */
  if ($name === '' || $price === '' || $stock === '' || !$file || $file['error'] !== UPLOAD_ERR_OK) {
    $error = '商品名・値段・在庫数・公開ステータス・商品画像をすべて入力してください。';
  } elseif (!ctype_digit((string)$price) || !ctype_digit((string)$stock)) {
    $error = '値段と在庫数は0以上の整数で入力してください。';
  } else {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
      $error = '商品画像はJPEGまたはPNGのみアップロードできます。';
    } else {
      // アップロードされたファイルが被らないように、一意（ユニーク）なファイル名を生成する
      $saveName = uniqid('p_', true) . '.' . $ext; 
      // 保存先の絶対パスを指定する
      $dest = $uploadPath . '/' . $saveName; 
      
      // 画像ファイルを指定したディレクトリへ移動して保存する
      if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $error = '画像の保存に失敗しました。'; 
      } else {
        if (create_product($pdo, $name, (int)$price, (int)$stock, $publicFlg === 1 ? 1 : 0, $saveName)) {
          $message = '商品を追加しました。';
        } else {
          $detail = get_last_ec_error();
          $error = '商品追加に失敗しました。' . ($detail !== '' ? ' (' . $detail . ')' : '');
        }
      }
    }
  }
}

// 在庫数更新処理: actionがupdate_stockの場合は、在庫数のバリデーションを行い、問題がなければupdate_stock_qty()関数を呼び出して在庫数を更新する
if ($action === 'update_stock' && is_admin()) {
  // 在庫数のバリデーション: 在庫数が0以上の整数であることを確認する。バリデーションに失敗した場合は、エラーメッセージを設定する
  $pid = (int)($_POST['product_id'] ?? 0); // 商品IDを取得する
  $qty = $_POST['stock_qty'] ?? ''; // 在庫数を取得する
  if (!ctype_digit((string)$qty)) {
    $error = '在庫数は0以上の整数で入力してください。';
  } else {
    update_stock_qty($pdo, $pid, (int)$qty); // update_stock_qty()関数を呼び出して在庫数を更新する
    $message = '在庫数を更新しました。'; // 成功メッセージを設定する
  }
}

// 公開ステータスの切り替え処理: actionがtoggle_publicの場合は、現在の公開ステータスを取得して、update_public_flg()関数を呼び出して公開ステータスを切り替える
if ($action === 'toggle_public' && is_admin()) {
  $pid = (int)($_POST['product_id'] ?? 0);
  $flg = (int)($_POST['public_flg'] ?? 0) === 1 ? 0 : 1;
  update_public_flg($pdo, $pid, $flg);
  $message = '公開ステータスを更新しました。';
}

// 商品削除処理: actionがdelete_productの場合は、delete_product()関数を呼び出して商品を削除する
if ($action === 'delete_product' && is_admin()) {
  $pid = (int)($_POST['product_id'] ?? 0);
  delete_product($pdo, $pid);
  $message = '商品を削除しました。';
}

// カート関連の処理: actionがadd_cart、update_cart、delete_cart、checkoutの場合は、それぞれadd_to_cart()、update_cart_qty()、delete_cart_item()、checkout()関数を呼び出してカートの操作や購入処理を行う
// actionがadd_cartの場合は、add_to_cart()関数を呼び出してカートに商品を追加する。エラーが発生した場合はエラーメッセージを設定する。
if ($action === 'add_cart' && is_login()) {
  add_to_cart($pdo, (int)$_SESSION['user']['user_id'], (int)($_POST['product_id'] ?? 0));
  $message = 'カートに追加しました。';
}

// カートの数量更新処理: actionがupdate_cartの場合は、数量のバリデーションを行い、問題がなければupdate_cart_qty()関数を呼び出してカート内の商品の数量を更新する
if ($action === 'update_cart' && is_login()) {
  $qty = $_POST['product_qty'] ?? ''; // 数量を取得する
  // 数量のバリデーション: 数量が正の整数であることを確認する。バリデーションに失敗した場合は、エラーメッセージを設定する
  if (!preg_match('/^[1-9][0-9]*$/', (string)$qty)) {
    $error = '数量は正の整数で入力してください。';
  } else {
    update_cart_qty($pdo, (int)($_POST['cart_id'] ?? 0), (int)$_SESSION['user']['user_id'], (int)$qty); // update_cart_qty()関数を呼び出してカート内の商品の数量を更新する
    $message = '数量を更新しました。';
  }
}

// カートからの削除処理: actionがdelete_cartの場合は、delete_cart_item()関数を呼び出してカート内の商品を削除する
if ($action === 'delete_cart' && is_login()) {
  delete_cart_item($pdo, (int)($_POST['cart_id'] ?? 0), (int)$_SESSION['user']['user_id']); 
  $message = '商品をカートから削除しました。';
}

// 購入処理: actionがcheckoutの場合は、checkout()関数を呼び出して購入処理を行い、結果に応じてメッセージを設定する
if ($action === 'checkout' && is_login()) {
  $checkoutResult = checkout($pdo, (int)$_SESSION['user']['user_id']); 
  // 購入処理の結果に応じたメッセージの設定: checkout()関数の戻り値がokの場合は、セッションに購入情報を保存して、complete.phpへリダイレクトする。okでない場合は、エラーメッセージを設定する
  if ($checkoutResult['ok']) {
    $_SESSION['last_purchase'] = $checkoutResult['items'];
    $_SESSION['flash_message'] = $checkoutResult['message'];
    to_page('complete.php');
  }
  $error = $checkoutResult['message'];
}

// フラッシュメッセージの処理: セッションに保存されたフラッシュメッセージがある場合は、$messageにセットして、セッションから削除する
if (!empty($_SESSION['flash_message']) && $message === '') {
  $message = $_SESSION['flash_message'];
  unset($_SESSION['flash_message']);
}

