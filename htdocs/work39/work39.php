<?php
require_once '../../include/model/work39_model.php';
require_once '../../include/view/work39_view.php';

$model = new Work39Model();
// 公開フラグが「公開」のものだけ取得
$images = $model->get_images(true);

Work39View::render_public_gallery($images);