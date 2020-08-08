'use strict';

bsw.configure({
    logic: {
        init: function init(v) {
            hljs.initHighlighting();
            var allLi = $('.markdown-content .index li');
            var anchor = bsw.leftTrim(window.location.hash, '#');
            var currentLi = $('li.id-' + anchor);
            var currentMd = $('#' + anchor);
            $('.markdown-content .index').scrollTop(bsw.offset(currentLi).top);
            $('.markdown-content .content').scrollTop(bsw.offset(currentMd).top);
            currentLi.addClass('current');
            allLi.click(function () {
                var thisLi = $(this);
                var url = thisLi.find('a').attr('href');
                var urlItems = bsw.parseQueryString(url, true);
                var currentItems = bsw.parseQueryString(null, true);
                if (urlItems['hostPart'] !== currentItems['hostPart']) {
                    return;
                }
                allLi.removeClass('current');
                thisLi.addClass('current');
                setTimeout(function () {
                    return bsw.prominentAnchor();
                }, 100);
            });
        }
    }
});
