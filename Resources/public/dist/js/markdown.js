/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-08-14 */
"use strict";bsw.configure({logic:{init:function(a){hljs.initHighlighting();var b=$(".markdown-content .index li"),c=bsw.leftTrim(window.location.hash,"#");if(c.length){var d=$("li.id-"+c),e=$("#"+c);d.length&&($(".markdown-content .index").scrollTop(bsw.offset(d).top),d.addClass("current")),e.length&&$(".markdown-content .content").scrollTop(bsw.offset(e).top)}b.click(function(){var a=$(this),c=a.find("a").attr("href"),d=bsw.parseQueryString(c,!0),e=bsw.parseQueryString(null,!0);d.hostPart===e.hostPart&&(b.removeClass("current"),a.addClass("current"),setTimeout(function(){return bsw.prominentAnchor()},100))})}}});