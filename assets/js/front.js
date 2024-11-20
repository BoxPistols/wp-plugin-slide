// assets/js/front.js
document.addEventListener('DOMContentLoaded', function () {
  const sliders = document.querySelectorAll('.job-slider');

  sliders.forEach(function (slider) {
    new Swiper(slider, {
      slidesPerView: 1,
      spaceBetween: 30,
      loop: false,
      speed: 600,

      // autoplay: {
      //   delay: 5000,
      //   disableOnInteraction: false,
      //   pauseOnMouseEnter: true
      // },

      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        }
      },

      navigation: {
        nextEl: slider.querySelector('.swiper-button-next'),
        prevEl: slider.querySelector('.swiper-button-prev'),
      },

      effect: 'slide',

      a11y: {
        prevSlideMessage: '前の求人を表示',
        nextSlideMessage: '次の求人を表示',
        firstSlideMessage: '最初の求人です',
        lastSlideMessage: '最後の求人です',
      }
    });
  });
});