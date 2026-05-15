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
 * 購入完了情報の取得: 
 * セッションに保存された直前の購入情報（商品名、値段、数量、画像名など）を取得する。
 */
$items = h_array($_SESSION['last_purchase'] ?? []);
$total = 0; // 合計金額計算用の変数

// ページ全体のレイアウト（ヘッダー部分）を出力
render_header('購入完了');
/* メッセージの表示: $messageと$errorに応じて、成功メッセージやエラーメッセージを表示する。成功メッセージは緑色、エラーメッセージは赤色で表示する */
if ($message !== '') { echo '<p class="msg ok">' . h($message) . '</p>'; }
if ($error !== '') { echo '<p class="msg err">' . h($error) . '</p>'; }
?>

<h1>購入完了</h1>
<table>
  <tr><th>画像</th><th>商品名</th><th>値段</th><th>数量</th><th>小計</th></tr>
  <!-- 
    購入アイテムの表示: 
    セッションから取得した購入内容をループで1つずつ表示する。
    ここで小計と合計金額も合わせて算出する。
  -->
   <?php foreach ($items as $item) { $sub = (int)$item['price'] * (int)$item['product_qty']; $total += $sub; ?>
    <tr>
      <td><?php if ($item['image_name'] !== '') { ?><img src="<?php echo h(UPLOAD_DIR . '/' . $item['image_name']); ?>" alt="img"><?php } ?></td>
      <td><?php echo $item['product_name']; ?></td>
      <td><?php echo $item['price']; ?>円</td>
      <td><?php echo $item['product_qty']; ?></td>
      <td><?php echo $sub; ?>円</td>
    </tr>
  <?php } ?>
</table>
<p>合計: <?php echo $total; ?>円</p>
<!-- 商品一覧ページへのリンク -->
<p><a href="products.php">商品一覧へ戻る</a></p>
<?php render_footer();
