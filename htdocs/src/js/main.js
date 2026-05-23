// ハンバーガーメニューとスムーススクロールの機能
// ※ defer属性により、DOM構築完了後に自動実行されるためDOMContentLoadedは不要

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

    // href="#" の場合はページ最上部へスクロール
    if (targetId === "#") {
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
      return;
    }

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

// Image Popup Modal Lightbox Functionality
const modal = document.getElementById("imageModal");
const modalImg = document.getElementById("modalImg");
const modalCaption = document.getElementById("modalCaption");
const modalClose = document.getElementById("modalClose");

// Open modal
const openModal = (src, caption) => {
  modalImg.src = src;
  modalCaption.textContent = caption;
  modal.style.display = "flex";
  // Force a browser reflow to trigger CSS opacity transition
  modal.offsetHeight; 
  modal.classList.add("show");
  modal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden"; // Prevent background scrolling
};

// Close modal
const closeModal = () => {
  modal.classList.remove("show");
  modal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = ""; // Restore background scrolling
  // Hide modal element from layout after CSS transition finishes
  setTimeout(() => {
    if (!modal.classList.contains("show")) {
      modal.style.display = "none";
    }
  }, 300); // Matches the 0.3s transition duration
};

// Attach click event to triggers
document.querySelectorAll(".popup-trigger").forEach(trigger => {
  trigger.addEventListener("click", function(e) {
    e.preventDefault(); // Prevent default link navigation
    const imgSrc = this.getAttribute("href");
    const captionText = this.getAttribute("data-caption") || "";
    openModal(imgSrc, captionText);
  });
});

// Close modal when close button is clicked
modalClose.addEventListener("click", closeModal);

// Close modal when clicking outside the image content
modal.addEventListener("click", function(e) {
  if (e.target === modal) {
    closeModal();
  }
});

// Close modal when Escape key is pressed
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && modal.classList.contains("show")) {
    closeModal();
  }
});
