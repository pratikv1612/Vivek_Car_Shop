/* Homepage product slider — prev/next arrows scroll the product tracks */
(function () {
    function setup(nav) {
        var section = nav.closest('.va-section');
        var track = section ? section.querySelector('.va-slider-track') : null;
        if (!track) return;
        var prev = nav.querySelector('.va-slider-prev');
        var next = nav.querySelector('.va-slider-next');
        function step() {
            var card = track.firstElementChild;
            if (!card) return track.clientWidth * 0.8;
            var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            return card.getBoundingClientRect().width + gap;
        }
        function update() {
            if (prev) prev.disabled = track.scrollLeft <= 2;
            if (next) next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
        }
        if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
        if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    }
    function init() { document.querySelectorAll('.va-slider-nav').forEach(setup); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
