<?php
require_once '../../include/app.php';
/* 
 * アクセス制限: 
 * 未ログインのユーザーがアクセスした場合はログインページに強制遷移させる。
 */
if (!is_login()) {
  to_page('login.php');
}
/* 
 * カートアイテムの取得: 
 * 現在ログインしているユーザーのカートに入っている商品情報を取得する。
 * XSS対策のため h_array() でエスケープ処理をしてから変数に格納する。
 */
$items = h_array(get_cart_items($pdo, (int)$_SESSION['user']['user_id']));
$total = 0; // 合計金額を計算するための変数を初期化

// ページ全体のレイアウト（ヘッダー部分）を出力
render_header('ショッピングカート');
if ($message !== '') { echo '<p class="msg ok">' . h($message) . '</p>'; }
if ($error !== '') { echo '<p class="msg err">' . h($error) . '</p>'; }
?>
<h1>ショッピングカート</h1>
<p><a href="products.php">商品一覧へ</a></p>
<table>
  <tr><th>画像</th><th>商品名</th><th>値段</th><th>数量</th><th>小計</th><th>操作</th></tr>
  <!-- 
    カートアイテムの表示エリア:
    カート内の商品をループで表示。同時に（単価 × 数量）で小計を算出し、
    全体の合計金額 ($total) に加算していく。
  -->
  <?php foreach ($items as $it) { $sub = (int)$it['price'] * (int)$it['product_qty']; $total += $sub; ?>
    <tr>
      <td><?php if ($it['image_name'] !== '') { ?><img src="<?php echo h(UPLOAD_DIR . '/' . $it['image_name']); ?>" alt="img"><?php } ?></td>
      <td><?php echo $it['product_name']; ?></td>
      <td><?php echo $it['price']; ?>円</td>
      <td>
        <form method="post" action="cart.php" class="row">
          <input type="hidden" name="action" value="update_cart">
          <input type="hidden" name="cart_id" value="<?php echo $it['cart_id']; ?>">
          <input type="number" name="product_qty" min="1" value="<?php echo $it['product_qty']; ?>">
          <button type="submit">数量変更</button>
        </form>
      </td>
      <td><?php echo $sub; ?>円</td>
      <td>
        <form method="post" action="cart.php">
          <input type="hidden" name="action" value="delete_cart">
          <input type="hidden" name="cart_id" value="<?php echo $it['cart_id']; ?>">
          <button type="submit">削除</button>
        </form>
      </td>
    </tr>
  <?php } ?>
</table>
<!-- 
  合計金額の表示と購入フォーム: 
  カート内の全商品の合計金額を表示し、購入手続き（checkout）に進むためのボタンを設置。
-->
<p>合計: <?php echo $total; ?>円</p>
<form method="post" action="cart.php">
  <input type="hidden" name="action" value="checkout">
  <button type="submit">購入する</button>
</form>
<?php render_footer();
