/**
 * フロントエンド用JavaScriptファイル
 * Swiperスライダーの初期化と設定を行います
 * 
 * @package JobSlider
 * @version 1.0.0
 */

document.addEventListener('DOMContentLoaded', function () {
  // すべてのスライダーを初期化
  const sliders = document.querySelectorAll('.job-slider');

  sliders.forEach(function (slider) {
    new Swiper(slider, {
      // 基本設定
      slidesPerView: 1,
      spaceBetween: 30,
      grabCursor: true,

      // レスポンシブ設定
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        }
      },

      // ナビゲーション
      navigation: {
        nextEl: slider.querySelector('.swiper-button-next'),
        prevEl: slider.querySelector('.swiper-button-prev'),
      },

      // アクセシビリティ
      a11y: {
        prevSlideMessage: '前の求人を表示',
        nextSlideMessage: '次の求人を表示',
        firstSlideMessage: '最初の求人です',
        lastSlideMessage: '最後の求人です',
      },

      // パフォーマンス最適化
      preloadImages: false,
      lazy: {
        loadPrevNext: true,
      },
      watchSlidesProgress: true,
    });
  });
});