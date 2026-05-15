<?php
require_once '../../include/app.php';
/* 
 * アクセス制限: 
 * 未ログインのユーザーがアクセスした場合はログインページに強制遷移させる。
 */
if (!is_login()) {
  to_page('login.php');
}

// 検索キーワードの取得と、公開されている商品一覧の取得
$query = trim($_GET['q'] ?? '');
$rows = h_array(get_public_products($pdo, $query));

// ページ全体のレイアウト（ヘッダー部分）を出力
render_header('商品一覧');
if ($message !== '') { echo '<p class="msg ok">' . h($message) . '</p>'; }
if ($error !== '') { echo '<p class="msg err">' . h($error) . '</p>'; }
?>
<h1>商品一覧</h1>
<!-- 
  商品検索フォーム:
  商品名で絞り込み検索を行う。メソッドは GET を使用。
-->
<form method="get" action="products.php" class="row">
  <input type="text" name="q" value="<?php echo h($query); ?>" placeholder="商品名検索">
  <button type="submit">検索</button>
</form>
<p><a href="cart.php">ショッピングカートへ</a></p>
<!-- 
  商品リストの表示:
  取得した商品 ($rows) をループで1件ずつカード型で表示する。
-->
<?php foreach ($rows as $r) { ?>
  <div class="card">
    <?php if ($r['image_name'] !== '') { ?><img src="<?php echo h(UPLOAD_DIR . '/' . $r['image_name']); ?>" alt="img"><?php } ?>
    <h3><?php echo $r['product_name']; ?></h3>
    <p><?php echo $r['price']; ?>円</p>
    <?php if ((int)$r['stock_qty'] <= 0) { ?>
      <p>売り切れ</p>
    <?php } else { ?>
      <form method="post" action="products.php">
        <input type="hidden" name="action" value="add_cart">
        <input type="hidden" name="product_id" value="<?php echo $r['product_id']; ?>">
        <button type="submit">カートに入れる</button>
      </form>
    <?php } ?>
  </div>
<?php } ?>
<?php render_footer();