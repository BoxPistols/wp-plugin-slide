/**
 * Job Slider フロントエンド JavaScript
 * v1.0.0
 */

(function ($) {
  'use strict';

  // Swiperインスタンスを保持する配列
  let sliders = [];

  // スライダーの初期化
  function initSliders() {
    $('.job-slider').each(function (index) {
      const $slider = $(this);
      const sliderId = $slider.attr('id') || `job-slider-${index}`;

      // 既存のスライダーを破棄
      if (sliders[sliderId]) {
        sliders[sliderId].destroy();
      }

      // 新しいスライダーを初期化
      sliders[sliderId] = new Swiper(`#${sliderId}`, {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: false,
        speed: 600,

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
          nextEl: `#${sliderId} .swiper-button-next`,
          prevEl: `#${sliderId} .swiper-button-prev`,
        },

        // アクセシビリティ
        a11y: {
          prevSlideMessage: '前の求人を表示',
          nextSlideMessage: '次の求人を表示',
          firstSlideMessage: '最初の求人です',
          lastSlideMessage: '最後の求人です',
        },

        // カードの高さを揃える
        on: {
          init: function () {
            setTimeout(equalizeCardHeights, 100);
          },
          resize: function () {
            setTimeout(equalizeCardHeights, 100);
          }
        }
      });
    });
  }

  // カードの高さを揃える
  function equalizeCardHeights() {
    $('.job-slider').each(function () {
      const $cards = $(this).find('.job-card');
      let maxHeight = 0;

      // リセット
      $cards.height('auto');

      // 最大高さを取得
      $cards.each(function () {
        maxHeight = Math.max(maxHeight, $(this).outerHeight());
      });

      // 高さを設定
      $cards.height(maxHeight);
    });
  }

  // もっと見るボタンの処理
  function setupLoadMore() {
    $('.load-more').on('click', function () {
      const $button = $(this);
      const $container = $button.closest('.job-slider-container');
      const page = parseInt($button.data('page'));
      const corporationId = $button.data('corporation');

      $button.prop('disabled', true)
        .text('読み込み中...');

      $.ajax({
        url: jobSlider.ajaxUrl,
        method: 'POST',
        data: {
          action: 'load_more_jobs',
          page: page,
          corporation_id: corporationId,
          nonce: jobSlider.nonce
        },
        success: function (response) {
          if (response.success) {
            // 新しいカードを追加
            $container.find('.swiper-wrapper')
              .append(response.data.html);

            // スライダーを更新
            initSliders();

            // もっと見るボタンの更新または削除
            if (response.data.hasMore) {
              $button.data('page', page + 1)
                .prop('disabled', false)
                .text('もっと見る');
            } else {
              $button.remove();
            }
          } else {
            console.error('Error loading more jobs:', response.data);
            $button.prop('disabled', false)
              .text('再試行');
          }
        },
        error: function (xhr, status, error) {
          console.error('Ajax error:', error);
          $button.prop('disabled', false)
            .text('再試行');
        }
      });
    });
  }

  // スキルタグのツールチップ
  function initTooltips() {
    $('.job-skill').each(function () {
      const $skill = $(this);
      if ($skill.data('tooltip')) {
        $skill.tooltipster({
          theme: 'tooltipster-noir',
          animation: 'fade',
          delay: 100,
          side: 'top'
        });
      }
    });
  }

  // 初期化
  $(document).ready(function () {
    initSliders();
    setupLoadMore();
    initTooltips();

    // Lazy loading images
    if ('loading' in HTMLImageElement.prototype) {
      const images = document.querySelectorAll('img[loading="lazy"]');
      images.forEach(img => {
        img.src = img.dataset.src;
      });
    } else {
      // Fallback for browsers that don't support lazy loading
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
      document.body.appendChild(script);
    }
  });

  // ウィンドウリサイズ時の処理
  let resizeTimer;
  $(window).on('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      initSliders();
    }, 250);
  });

})(jQuery);