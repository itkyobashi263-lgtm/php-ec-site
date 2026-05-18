<?php
// データベース接続やデータ操作の関数を定義するファイルを呼び出す
require_once __DIR__ . '/../config/const.php';
/**
 * データベースへの接続 (PDOインスタンスの生成)
 * 接続エラー時にはスクリプトを停止してメッセージを表示する
 */
function get_connection() {
  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  } catch (PDOException $e) {
    exit('DB接続に失敗しました。');
  }
  return $pdo;
}

/**
 * ECサイト内のグローバルなエラーメッセージを保存する
 * @param string $message 記録したいエラーメッセージ
 */
function set_last_ec_error($message) {
  $GLOBALS['ec_last_error'] = (string)$message;
}
/**
 * ECサイト内のグローバルなエラーメッセージを取得する
 * @return string 直前に保存されたエラーメッセージ
 */
function get_last_ec_error() {
  return $GLOBALS['ec_last_error'] ?? '';
}

/**
 * XSS対策: 特殊文字を安全なHTMLエンティティに変換する関数
 * @param string $str 変換元の文字列
 */
function h($str) {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
/**
 * データベースから取得した2次元配列の全要素に一括で h() (XSS対策) を適用する
 * @param array $rows 変換元の2次元配列
 */
function h_array($rows) {
  foreach ($rows as $i => $row) {
    foreach ($row as $k => $v) {
      $rows[$i][$k] = h($v);
    }
  }
  return $rows;
}

/**
 * データベースの初期化
 * users, products, images, stocks, cartsテーブルを作成する（存在しない場合のみ）。
 * また、初期の管理者ユーザーも作成する。
 */
function init_ec_tables($pdo) {
  $pdo->exec('CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    create_date DATETIME NOT NULL,
    update_date DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

  $pdo->exec('CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    public_flg TINYINT(1) NOT NULL DEFAULT 0,
    create_date DATETIME NOT NULL,
    update_date DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

  $pdo->exec('CREATE TABLE IF NOT EXISTS images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    create_date DATETIME NOT NULL,
    update_date DATETIME NOT NULL,
    CONSTRAINT fk_images_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

  $pdo->exec('CREATE TABLE IF NOT EXISTS stocks (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    stock_qty INT NOT NULL,
    create_date DATETIME NOT NULL,
    update_date DATETIME NOT NULL,
    CONSTRAINT fk_stocks_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

  $pdo->exec('CREATE TABLE IF NOT EXISTS carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    product_qty INT NOT NULL,
    create_date DATETIME NOT NULL,
    update_date DATETIME NOT NULL,
    UNIQUE KEY uq_cart_user_product (user_id, product_id),
    CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_carts_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

  $stmt = $pdo->prepare('SELECT user_id FROM users WHERE user_name = ? LIMIT 1');
  $stmt->execute([ADMIN_USER]);
  if (!$stmt->fetch()) {
    $now = date('Y-m-d H:i:s');
    $ins = $pdo->prepare('INSERT INTO users (user_name, password, create_date, update_date) VALUES (?, ?, ?, ?)');
    $ins->execute([ADMIN_USER, password_hash(ADMIN_PASS, PASSWORD_DEFAULT), $now, $now]);
  }
}
/**
 * ユーザー名のバリデーション（入力値検証）
 * 必須チェックおよび半角英数字・アンダースコア（5文字以上）かどうかをチェックする
 * @param string $name 変換元の文字列
 * @return string エラーメッセージ（エラーがない場合は空
 */
function validate_username($name) {
  if ($name === '') {
    return 'ユーザー名を入力してください。';
  }
  if (!preg_match('/^[a-zA-Z0-9_]{5,}$/', $name)) {
    return 'ユーザー名は5文字以上の半角英数字とアンダースコアのみ使用できます。';
  }
  return '';
}
/**
 * パスワードのバリデーション（入力値検証）
 * 必須チェックおよび半角英数字・アンダースコア（8文字以上）かどうかをチェックする
 * @param string $pass 変換元の文字列
 * @return string エラーメッセージ（エラーがない場合は空
 */
function validate_password($pass) {
  if ($pass === '') {
    return 'パスワードを入力してください。';
  }
  if (!preg_match('/^[a-zA-Z0-9_]{8,}$/', $pass)) {
    return 'パスワードは8文字以上の半角英数字とアンダースコアのみ使用できます。';
  }
  return '';
}
/**
 * ユーザー名によるユーザー検索
 * @param PDO $pdo
 * @param string $name 検索するユーザー名
 * @return array|false 見つかった場合はユーザー情報の配列、見つからなければfalse
 */
function find_user_by_name($pdo, $name) {
  $stmt = $pdo->prepare('SELECT * FROM users WHERE user_name = ? LIMIT 1');
  $stmt->execute([$name]);
  return $stmt->fetch();
}

/**
 * 新規ユーザーの登録処理
 * パスワードは自動的にハッシュ化されて安全に保存される
 * @param PDO $pdo
 * @param string $name 登録するユーザー名
 * @param string $pass 登録するパスワード（平文）
 * @return bool 登録成功でtrue
 */
function create_user($pdo, $name, $pass) {
  $now = date('Y-m-d H:i:s');
  $stmt = $pdo->prepare('INSERT INTO users (user_name, password, create_date, update_date) VALUES (?, ?, ?, ?)');
  return $stmt->execute([$name, password_hash($pass, PASSWORD_DEFAULT), $now, $now]);
}
/**
 * 管理者用の商品一覧取得
 * products, stocks, images テーブルを結合し、すべての商品情報を取得する
 * @param PDO $pdo
 * @return array 商品情報の配列
 */
function get_admin_products($pdo) {
  $sql = 'SELECT products.product_id, products.product_name, products.price, products.public_flg, stocks.stock_qty, images.image_name 
          FROM products 
          JOIN stocks ON products.product_id = stocks.product_id
          LEFT JOIN images ON products.product_id = images.product_id
          ORDER BY products.product_id DESC';
  return $pdo->query($sql)->fetchAll();
}

/**
 * 新規商品の追加
 * トランザクションを用いて products, stocks, images の3テーブルに安全に一括登録する
 * @param PDO $pdo
 * @param string $name 商品名
 * @param int $price 値段
 * @param int $stock 初期在庫数
 * @param int $publicFlg 公開フラグ(1:公開, 0:非公開)
 * @param string $imageName 保存された画像ファイル名
 * @return bool 成功でtrue、失敗でfalse（エラー詳細はset_last_ec_errorに保存）
 */
function create_product($pdo, $name, $price, $stock, $publicFlg, $imageName) {
  set_last_ec_error('');
  $now = date('Y-m-d H:i:s');
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare('INSERT INTO products (product_name, price, public_flg, create_date, update_date) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $price, $publicFlg, $now, $now]);
    $pid = (int)$pdo->lastInsertId();

    $st = $pdo->prepare('INSERT INTO stocks (product_id, stock_qty, create_date, update_date) VALUES (?, ?, ?, ?)');
    $st->execute([$pid, $stock, $now, $now]);

    // 画像情報を保存
    $im = $pdo->prepare('INSERT INTO images (product_id, image_name, create_date, update_date) VALUES (?, ?, ?, ?)');
    $im->execute([$pid, $imageName, $now, $now]);

    $pdo->commit();
    return true;
  } catch (Exception $e) {
    $pdo->rollBack();
    set_last_ec_error($e->getMessage());
    return false;
  }
}
/**
 * 商品の在庫数を更新する
 * @param PDO $pdo
 * @param int $productId 商品ID
 * @param int $qty 新しい在庫数
 * @return bool 成功でtrue
 */
function update_stock_qty($pdo, $productId, $qty) {
  $stmt = $pdo->prepare('UPDATE stocks SET stock_qty = ?, update_date = ? WHERE product_id = ?');
  return $stmt->execute([$qty, date('Y-m-d H:i:s'), $productId]);
}

/**
 * 商品の公開/非公開ステータスを更新する
 * @param PDO $pdo
 * @param int $productId 商品ID
 * @param int $flg 公開フラグ(1:公開, 0:非公開)
 * @return bool 成功でtrue
 */
function update_public_flg($pdo, $productId, $flg) {
  $stmt = $pdo->prepare('UPDATE products SET public_flg = ?, update_date = ? WHERE product_id = ?');
  return $stmt->execute([$flg, date('Y-m-d H:i:s'), $productId]);
}

/**
 * 商品を削除する
 * 外部キー制約（ON DELETE CASCADE）により、関連するstocks, images, cartsも自動的に削除される
 * @param PDO $pdo
 * @param int $productId 商品ID
 * @return bool 成功でtrue
 */
function delete_product($pdo, $productId) {
  $stmt = $pdo->prepare('DELETE FROM products WHERE product_id = ?');
  return $stmt->execute([$productId]);
}

/**
 * 一般ユーザー用の公開商品一覧を取得する（キーワード検索対応）
 * @param PDO $pdo
 * @param string $keyword 検索キーワード（空なら全件）
 * @return array 商品情報の配列
 */
function get_public_products($pdo, $keyword = '') {
  $sql = 'SELECT products.product_id, products.product_name, products.price, stocks.stock_qty, images.image_name 
          FROM products
          JOIN stocks ON products.product_id = stocks.product_id
          LEFT JOIN images ON products.product_id = images.product_id
          WHERE products.public_flg = 1';
  $params = [];
  if ($keyword !== '') {
    $sql .= ' AND products.product_name LIKE ?';
    $params[] = '%' . $keyword . '%';
  }
  $sql .= ' ORDER BY products.product_id DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll();
}
/**
 * ショッピングカートに商品を追加する
 * 既にカートにある場合は数量を+1し、無い場合は新規に追加する
 * トランザクション・排他制御（FOR UPDATE）を用いて安全に処理する
 * @param PDO $pdo
 * @param int $userId ユーザーID
 * @param int $productId 商品ID
 * @return bool 成功でtrue
 */
function add_to_cart($pdo, $userId, $productId) {
  $pdo->beginTransaction();
  try {
    $sel = $pdo->prepare('SELECT cart_id, product_qty FROM carts WHERE user_id = ? AND product_id = ? FOR UPDATE');
    $sel->execute([$userId, $productId]);
    $row = $sel->fetch();
    $now = date('Y-m-d H:i:s');

    if ($row) {
      $upd = $pdo->prepare('UPDATE carts SET product_qty = product_qty + 1, update_date = ? WHERE cart_id = ?');
      $upd->execute([$now, $row['cart_id']]);
    } else {
      $ins = $pdo->prepare('INSERT INTO carts (user_id, product_id, product_qty, create_date, update_date) VALUES (?, ?, 1, ?, ?)');
      $ins->execute([$userId, $productId, $now, $now]);
    }
    $pdo->commit();
    return true;
  } catch (Exception $e) {
    $pdo->rollBack();
    return false;
  }
}
/**
 * 指定ユーザーのカート内商品一覧を取得する
 * @param PDO $pdo
 * @param int $userId ユーザーID
 * @return array カートアイテムの配列
 */
function get_cart_items($pdo, $userId) {
  // カート、商品、在庫、画像の4テーブルを結合して必要なデータを取得
  $sql = 'SELECT carts.cart_id, carts.product_id, carts.product_qty, products.product_name, products.price, products.public_flg, stocks.stock_qty, images.image_name 
          FROM carts
          JOIN products ON carts.product_id = products.product_id
          JOIN stocks ON carts.product_id = stocks.product_id
          LEFT JOIN images ON carts.product_id = images.product_id
          WHERE carts.user_id = ?
          ORDER BY carts.cart_id DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userId]);
  return $stmt->fetchAll();
}

/**
 * カート内の商品数量を更新する
 * @param PDO $pdo
 * @param int $cartId カートID
 * @param int $userId ユーザーID（他人のカートを弄れないようにするため）
 * @param int $qty 変更後の数量
 * @return bool 成功でtrue
 */
function update_cart_qty($pdo, $cartId, $userId, $qty) {
  $stmt = $pdo->prepare('UPDATE carts SET product_qty = ?, update_date = ? WHERE cart_id = ? AND user_id = ?');
  return $stmt->execute([$qty, date('Y-m-d H:i:s'), $cartId, $userId]);
}

/**
 * カートから特定の商品を削除する
 * @param PDO $pdo
 * @param int $cartId カートID
 * @param int $userId ユーザーID（他人のカートを弄れないようにするため）
 * @return bool 成功でtrue
 */
function delete_cart_item($pdo, $cartId, $userId) {
  $stmt = $pdo->prepare('DELETE FROM carts WHERE cart_id = ? AND user_id = ?');
  return $stmt->execute([$cartId, $userId]);
}

/**
 * カート内の商品をすべて購入（チェックアウト）する
 * トランザクションを用いて、在庫数の減算とカートの空化をアトミックに実行する
 * 在庫が不足している場合はエラーとする
 * @param PDO $pdo
 * @param int $userId ユーザーID
 * @return array 結果ステータス('ok', 'message', 'items')の連想配列
 */
function checkout($pdo, $userId) {
  $items = get_cart_items($pdo, $userId);
  if (!$items) {
    return ['ok' => false, 'message' => 'カートに商品がありません。', 'items' => []];
  }

  $pdo->beginTransaction();
  try {
    foreach ($items as $item) {
      $sel = $pdo->prepare('SELECT stock_qty FROM stocks WHERE product_id = ? FOR UPDATE');
      $sel->execute([$item['product_id']]);
      $stock = (int)$sel->fetchColumn();
      if ($stock < (int)$item['product_qty']) {
        throw new RuntimeException('在庫がなくなった商品があります: ' . $item['product_name']);
      }
    }

    foreach ($items as $item) {
      $upd = $pdo->prepare('UPDATE stocks SET stock_qty = stock_qty - ?, update_date = ? WHERE product_id = ?');
      $upd->execute([(int)$item['product_qty'], date('Y-m-d H:i:s'), (int)$item['product_id']]);
    }

    $del = $pdo->prepare('DELETE FROM carts WHERE user_id = ?');
    $del->execute([$userId]);

    $pdo->commit();
    return ['ok' => true, 'message' => '購入が完了しました。', 'items' => $items];
  } catch (Exception $e) {
    $pdo->rollBack();
    return ['ok' => false, 'message' => $e->getMessage(), 'items' => []];
  }
}
