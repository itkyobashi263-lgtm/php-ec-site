<?php

/**
 * 全ページ共通のHTMLヘッダー（<head> 〜 ページ上部）を出力する
 * @param string $title ページの<title>タグに入る文字列
 */
function render_header($title) {
  ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo h($title); ?></title>
  <style>
    :root {
      --space-1: 8px;
      --space-2: 12px;
      --space-3: 16px;
      --radius: 8px;
      --border: #d9d9d9;
    }

    * { box-sizing: border-box; }

    body {
      font-family: "Yu Gothic UI", "Yu Gothic", "Hiragino Kaku Gothic ProN", sans-serif;
      margin: 0;
      background: #f7f8fa;
      color: #222;
      line-height: 1.5;
    }

    .wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: 24px;
    }

    h1 { margin-top: 0; }

    .msg { padding: 10px 12px; border-radius: var(--radius); margin: 8px 0; }
    .ok { background: #e6ffed; }
    .err { background: #ffeaea; }

    .row {
      display: flex;
      gap: var(--space-1);
      align-items: center;
      flex-wrap: wrap;
    }

    input, select, button {
      font: inherit;
      padding: 8px 10px;
      border-radius: 6px;
      border: 1px solid #b9bcc3;
    }

    button {
      background: #0f6f5c;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    button:hover { opacity: 0.9; }

    table {
      border-collapse: collapse;
      width: 100%;
      background: #fff;
    }

    th, td {
      border: 1px solid var(--border);
      padding: 8px;
      text-align: left;
      vertical-align: top;
    }

    img {
      max-width: 120px;
      height: auto;
      border-radius: 4px;
    }

    .card {
      border: 1px solid var(--border);
      background: #fff;
      border-radius: var(--radius);
      padding: var(--space-2);
      margin: 10px 0;
    }

    /* Tablet: 768px - 1024px */
    @media (max-width: 1024px) {
      .wrap {
        max-width: 900px;
        padding: 16px;
      }

      img { max-width: 100px; }

      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
    }

    /* Smartphone: <= 767px */
    @media (max-width: 767px) {
      .wrap { padding: 12px; }

      h1 { font-size: 1.4rem; }

      .row {
        flex-direction: column;
        align-items: stretch;
      }

      input, select, button {
        width: 100%;
      }

      .card { padding: 10px; }

      table, thead, tbody, tr, th, td {
        display: block;
        width: 100%;
      }

      thead { display: none; }

      tr {
        margin-bottom: 10px;
        border: 1px solid var(--border);
        border-radius: 6px;
        background: #fff;
        padding: 8px;
      }

      td {
        border: none;
        border-bottom: 1px solid #eee;
        white-space: normal;
        padding: 6px 0;
      }

      td:last-child { border-bottom: none; }

      img {
        max-width: 100%;
      }
    }
      header button{
        flex-direction: flex;
        background: #0f6f5c;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
      }

      footer {
        text-align: center;
        padding: 12px;
        background: #f1f1f1;
        margin-top: 20px;
      }

  </style>
</head>
<header>
  <h1>ECサイト ディーキャリアITエキスパート京橋</h1>
  <!-- ログインしている場合は商品一覧とログアウトボタンを表示 -->
  <?php if (function_exists('is_login') && is_login()) { ?>
  <div style="display: flex; gap: 8px;">
    <?php if (function_exists('is_admin') && !is_admin()) { ?>
    <button onclick="location.href='products.php'">商品一覧</button>
    <?php } ?>
    <form method="post" action="login.php" style="margin: 0;">
      <input type="hidden" name="action" value="logout">
      <button type="submit">ログアウト</button>
    </form>
  </div>
  <?php } ?>
</header>
<body><div class="wrap">
  <?php
}



/**
 * 全ページ共通のHTMLフッター（ページ下部 〜 </html>）を出力する
 */
function render_footer() {
  echo '
  </div>
  </body>
  <footer>
    <p>ECサイト ディーキャリアITエキスパート京橋</p>
  </footer>
  </html>
  ';
}
