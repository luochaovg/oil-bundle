/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2019-12-28 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}app.configure({data:{selected_list:{},disabled_list:[]},method:{selectAll:function(){var a=this,b=a[a.keyForForm];$.each(this.selected_list,function(c,d){var e=bsw.arrayIntersect(d,a.disabled_list),f=b.getFieldValue(c),g=[],h=!0,i=!1,j=void 0;try{for(var k,l=d[Symbol.iterator]();!(h=(k=l.next()).done);h=!0){var m=k.value;e.includes(m)?f.includes(m)&&g.push(m):g.push(m)}}catch(a){i=!0,j=a}finally{try{!h&&l.return&&l.return()}finally{if(i)throw j}}b.setFieldsValue(_defineProperty({},c,g))})},unSelectAll:function(){var a=this,b=a[a.keyForForm];$.each(this.selected_list,function(c,d){var e=bsw.arrayIntersect(d,a.disabled_list),f=b.getFieldValue(c),g=[],h=!0,i=!1,j=void 0;try{for(var k,l=d[Symbol.iterator]();!(h=(k=l.next()).done);h=!0){var m=k.value;e.includes(m)&&f.includes(m)&&g.push(m)}}catch(a){i=!0,j=a}finally{try{!h&&l.return&&l.return()}finally{if(i)throw j}}b.setFieldsValue(_defineProperty({},c,g))})}}});