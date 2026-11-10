(function() {

    var carousel = document.getElementById('ab-carousel');
    if (!carousel) return;

    var slides = document.getElementById('ab-slides');
    var allSlides = slides.querySelectorAll('.ab-slide');
    var dots = carousel.querySelectorAll('.ab-dot');

    var prevBtn = carousel.querySelector('.ab-arrow-prev');
    var nextBtn = carousel.querySelector('.ab-arrow-next');

    var total = allSlides.length;
    var current = 0;

    var autoTimer = null;
    var startX = 0;
    var diffX = 0;

    if (total <= 1) return;

    function goTo(index) {

        if (index < 0) {
            index = total - 1;
        }

        if (index >= total) {
            index = 0;
        }

        current = index;

        slides.style.transform =
            'translateX(-' + (current * 100) + '%)';

        for (var i = 0; i < dots.length; i++) {

            dots[i].classList.toggle(
                'active',
                i === current
            );
        }
    }

    function next() {
        goTo(current + 1);
    }

    function prev() {
        goTo(current - 1);
    }

    function stopAuto() {
        clearInterval(autoTimer);
    }

    function startAuto() {
        stopAuto();
        autoTimer = setInterval(next, 4000);
    }

    /* DOTS */

    for (var i = 0; i < dots.length; i++) {

        (function(index) {

            dots[index].addEventListener('click', function() {

                goTo(index);
                startAuto();

            });

        })(i);
    }

    /* FLECHAS */

    if (prevBtn) {

        prevBtn.addEventListener('click', function() {

            prev();
            startAuto();

        });
    }

    if (nextBtn) {

        nextBtn.addEventListener('click', function() {

            next();
            startAuto();

        });
    }

    /* TOUCH */

    carousel.addEventListener('touchstart', function(e) {

        startX = e.touches[0].clientX;
        stopAuto();

    }, { passive: true });

    carousel.addEventListener('touchmove', function(e) {

        diffX = e.touches[0].clientX - startX;

    }, { passive: true });

    carousel.addEventListener('touchend', function() {

        if (diffX > 50) {
            prev();
        }
        else if (diffX < -50) {
            next();
        }

        diffX = 0;

        startAuto();

    });

    /* PAUSAR EN HOVER */

    carousel.addEventListener('mouseenter', stopAuto);

    carousel.addEventListener('mouseleave', startAuto);

    /* INICIO */

    goTo(0);
    startAuto();

})();
