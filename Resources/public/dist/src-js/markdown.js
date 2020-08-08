'use strict';

bsw.configure({
    logic: {
        markdown: function markdown(v) {
            var content = $('div.content-markdown > div.content');
            content.find('h3').each(function () {
                var h3 = $(this);
                var h3v = h3.html();
                var h2v = '';
                var h1v = '';
                var v = h3v;
                var h2 = h3.prev('h2');
                if (h2.length) {
                    h2v = h2.html();
                    v = h2v + '_' + v;
                    var h1 = h2.prev('h1');
                    if (h1.length) {
                        h1v = h1.html();
                        v = h1v + '_' + v;
                    }
                }
                v = md5(v);
                h3.html(h3v + ('<a class="anchor" href="#' + v + '">\u266A</a>'));
            });
        }
    }
});
