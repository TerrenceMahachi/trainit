(function () {
    'use strict';

    document.documentElement.classList.add('opportunity-motion-ready');

    var items = document.querySelectorAll('.opportunity-reveal');
    if (!items.length) {
        return;
    }

    items.forEach(function (item) {
        var delay = parseInt(item.getAttribute('data-delay') || '0', 10);
        item.style.setProperty('--reveal-delay', delay + 'ms');
    });

    if (!('IntersectionObserver' in window) ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        items.forEach(function (item) {
            item.classList.add('is-visible');
        });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.12
    });

    items.forEach(function (item) {
        observer.observe(item);
    });
}());
