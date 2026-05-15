<?php
require_once '../../include/app.php';
/* 
 * アクセス制限: 
 * 未ログイン、または一般ユーザー（管理者以外）がアクセスした場合は
 * ログインページに強制的にリダイレクトさせる。
 */
if (!is_login() || !is_admin()) {
  to_page('login.php');
}
/* 
 * 登録済み商品の取得:
 * データベースからすべての商品情報（商品ID、名前、値段、在庫数、公開状態、画像）を取得する。
 */
$rows = h_array(get_admin_products($pdo));

// ページ全体のレイアウト（ヘッダー部分）を出力。「商品管理」というタイトルを渡す。
render_header('商品管理');
/* メッセージの表示: $messageと$errorに応じて、成功メッセージやエラーメッセージを表示する。成功メッセージは緑色、エラーメッセージは赤色で表示する */
if ($message !== '') { echo '<p class="msg ok">' . h($message) . '</p>'; }
if ($error !== '') { echo '<p class="msg err">' . h($error) . '</p>'; }
?>
<h1>商品管理</h1>
<!-- 
  商品追加フォーム:
  新しい商品を登録するための入力フォーム。
  画像ファイルを送信するため、enctype="multipart/form-data" を必ず指定する。
-->
<form method="post" action="admin.php" enctype="multipart/form-data" class="card"> 
  <input type="hidden" name="action" value="add_product">
  <div class="row">
    <input name="product_name" placeholder="商品名" required>
    <input name="price" type="number" min="0" placeholder="値段" required>
    <input name="stock_qty" type="number" min="0" placeholder="在庫数" required>
    <select name="public_flg" required><option value="1">公開</option><option value="0">非公開</option></select>
    <input type="file" name="image" accept="image/jpeg,image/png" required>
    <button type="submit">商品追加</button>
  </div>
</form>
<!-- 
  商品一覧の表示エリア: 
  取得した商品データ ($rows) をループで1件ずつテーブルに表示する。
  各行には、在庫数更新・公開ステータス切替・削除のそれぞれのボタン（フォーム）を設置する。
-->
<table>
  <tr><th>画像</th><th>商品名</th><th>値段</th><th>在庫</th><th>公開</th><th>操作</th></tr>
  <?php foreach ($rows as $r) { ?>
  <tr>
    <td><?php if ($r['image_name'] !== '') { ?><img src="<?php echo h(UPLOAD_DIR . '/' . $r['image_name']); ?>" alt="img"><?php } ?></td>
    <td><?php echo $r['product_name']; ?></td>
    <td><?php echo $r['price']; ?>円</td>
    <td>
      <form method="post" action="admin.php" class="row">
        <input type="hidden" name="action" value="update_stock">
        <input type="hidden" name="product_id" value="<?php echo $r['product_id']; ?>">
        <input type="number" name="stock_qty" min="0" value="<?php echo $r['stock_qty']; ?>">
        <button type="submit">在庫更新</button>
      </form>
    </td>
    <td><?php echo ((int)$r['public_flg'] === 1) ? '公開' : '非公開'; ?></td>
    <td class="row">
      <form method="post" action="admin.php">
        <input type="hidden" name="action" value="toggle_public">
        <input type="hidden" name="product_id" value="<?php echo $r['product_id']; ?>">
        <input type="hidden" name="public_flg" value="<?php echo $r['public_flg']; ?>">
        <button type="submit">公開切替</button>
      </form>
      <form method="post" action="admin.php">
        <input type="hidden" name="action" value="delete_product">
        <input type="hidden" name="product_id" value="<?php echo $r['product_id']; ?>">
        <button type="submit">削除</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</table>
<?php render_footer(); ?>
