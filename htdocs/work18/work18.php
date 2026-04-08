<?php
define('MAX','3'); // 1ページの表示数

$customers = array( // 表示データの配列
    array('name' => '佐藤', 'age' => '10'),
    array('name' => '鈴木', 'age' => '15'),
    array('name' => '高橋', 'age' => '20'),
    array('name' => '田中', 'age' => '25'),
    array('name' => '伊藤', 'age' => '30'),
    array('name' => '渡辺', 'age' => '35'),
    array('name' => '山本', 'age' => '40'),
);

$customers_num = count($customers); // トータルデータ件数
$max_page = ceil($customers_num / MAX); // トータルページ数

// 現在のページを取得
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// ページ番号が範囲外にならないよう調整
$current_page = max(1, min($current_page, $max_page));

// 表示するデータを切り出し
$start_index = ($current_page - 1) * MAX;
$disp_data = array_slice($customers, $start_index, MAX);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>顧客リスト</title>
    <style>
        table { border-collapse: collapse; width: 300px; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .pagination a { margin-right: 10px; text-decoration: none; }
        .active { font-weight: bold; color: red; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>名前</th>
                <th>年齢</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($disp_data as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($customer['age'], ENT_QUOTES, 'UTF-8'); ?>歳</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $max_page; $i++): ?>
            <?php if ($i === $current_page): ?>
                <span class="active"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

</body>
</html>