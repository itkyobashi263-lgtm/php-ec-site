<?php
class Work39View {
    // 共通ヘッダー
    public static function header($title) {
        echo "<!DOCTYPE html><html lang='ja'><head><meta charset='UTF-8'><title>$title</title>";
        echo "<style>.gallery { display: flex; flex-wrap: wrap; gap: 20px; } .item { width: 250px; border: 1px solid #ccc; padding: 10px; text-align: center; } img { max-width: 100%; }</style></head><body>";
    }

    // 閲覧用ギャラリー表示
    public static function render_public_gallery($images) {
        self::header("画像一覧（閲覧用）");
        echo "<h1>画像一覧</h1><a href='work39_gallery.php'>管理・投稿ページへ</a><hr><div class='gallery'>";
        foreach ($images as $img) {
            $base64 = base64_encode($img['image_data']);
            echo "<div class='item'><strong>" . htmlspecialchars($img['title']) . "</strong><br>";
            echo "<img src='data:image/jpeg;base64,$base64'></div>";
        }
        echo "</div></body></html>";
    }

    // 管理・投稿用表示
    public static function render_admin_gallery($images, $success, $error) {
        self::header("画像管理");
        echo "<h1>画像投稿・管理</h1><a href='work39.php'>閲覧ページへ</a>";
        if ($success) echo "<p style='color:green;'>$success</p>";
        if ($error) echo "<p style='color:red;'>$error</p>";
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            タイトル：<input type="text" name="title">
            画像：<input type="file" name="image" accept=".jpg,.jpeg,.png">
            <button type="submit" name="post_image">投稿</button>
        </form>
        <hr>
        <div class="gallery">
            <?php foreach ($images as $img): ?>
                <div class="item" style="<?= $img['public_flg'] ? '' : 'background:#eee;' ?>">
                    <p>タイトル：<?= htmlspecialchars($img['title']) ?></p>
                    <img src="data:image/jpeg;base64,<?= base64_encode($img['image_data']) ?>">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?= $img['image_id'] ?>">
                        <select name="status">
                            <option value="1" <?= $img['public_flg'] == 1 ? 'selected' : '' ?>>公開</option>
                            <option value="0" <?= $img['public_flg'] == 0 ? 'selected' : '' ?>>非表示</option>
                        </select>
                        <button type="submit" name="update">更新</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        </body></html>
        <?php
    }
}