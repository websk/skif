(() => {
    'use strict';

    const enhanceResponsiveTables = () => {
        document.querySelectorAll('.app-content .table-responsive').forEach((wrapper) => {
            const isScrollable = wrapper.scrollWidth > wrapper.clientWidth + 1;
            wrapper.classList.toggle('table-scrollable', isScrollable);

            if (isScrollable) {
                wrapper.setAttribute('tabindex', '0');
                wrapper.setAttribute('aria-label', 'Таблица с горизонтальной прокруткой');
            } else {
                wrapper.removeAttribute('tabindex');
                wrapper.removeAttribute('aria-label');
            }
        });
    };

    let scheduled = false;
    const scheduleEnhancement = () => {
        if (scheduled) {
            return;
        }
        scheduled = true;
        window.requestAnimationFrame(() => {
            scheduled = false;
            enhanceResponsiveTables();
        });
    };

    document.addEventListener('DOMContentLoaded', scheduleEnhancement);
    window.addEventListener('resize', scheduleEnhancement);
    new window.MutationObserver(scheduleEnhancement).observe(document.documentElement, {childList: true, subtree: true});
})();
