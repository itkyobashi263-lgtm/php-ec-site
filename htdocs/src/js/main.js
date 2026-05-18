// ハンバーガーメニューとスムーススクロールの機能
document.addEventListener("DOMContentLoaded", () => {
  const hamburger = document.getElementById("hamburger");
  const navMenu = document.getElementById("nav-menu");

  // ハンバーガーボタンをクリックした時の処理
  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active"); // アイコンを×にする
    navMenu.classList.toggle("active");   // メニューを表示/非表示
  });

  // ページ内のリンク（#から始まるhref）をクリックした時のスムーススクロール処理
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener("click", function(e) {
      // デフォルトの瞬間的な移動をキャンセル
      e.preventDefault();

      // モバイル用メニューが開いていたら閉じる
      hamburger.classList.remove("active");
      navMenu.classList.remove("active");

      // スクロール先のIDを取得して要素を探す
      const targetId = this.getAttribute("href");
      const targetElement = document.querySelector(targetId);

      if (targetElement) {
        // 対象要素の位置へスムーススクロールする
        window.scrollTo({
          top: targetElement.offsetTop,
          behavior: "smooth"
        });
      }
    });
  });
});
