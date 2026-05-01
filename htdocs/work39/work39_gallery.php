<?php
require_once '../../include/model/work39_model.php';
require_once '../../include/view/work39_view.php';

$model = new Work39Model();
$msg_success = "";
$msg_error = "";

// 投稿リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_image'])) {
    $err = $model->validate_upload($_POST['title'], $_FILES['image']);
    if ($err) {
        $msg_error = $err;
    } else {
        if ($model->post_image($_POST['title'], $_FILES['image'])) {
            $msg_success = "投稿が完了しました。";
        }
    }
}

// 更新リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if ($model->update_status($_POST['id'], $_POST['status'])) {
        $msg_success = "ステータスを更新しました。";
    }
}

// 全データを取得して表示
$images = $model->get_images(false);
Work39View::render_admin_gallery($images, $msg_success, $msg_error);