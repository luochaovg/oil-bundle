/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-07-13 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}bsw.configure({method:{selectAll:function(){var a=this,b="persistenceForm";$.each(this.init.selectedList,function(c,d){var e=bsw.arrayIntersect(d,a.init.disabledList),f=a[b].getFieldValue(c),g=[],h=!0,i=!1,j=void 0;try{for(var k,l=d[Symbol.iterator]();!(h=(k=l.next()).done);h=!0){var m=k.value;e.includes(m)?f.includes(m)&&g.push(m):g.push(m)}}catch(a){i=!0,j=a}finally{try{!h&&l.return&&l.return()}finally{if(i)throw j}}a[b].setFieldsValue(_defineProperty({},c,g))})},unSelectAll:function(){var a=this,b="persistenceForm";$.each(this.init.selectedList,function(c,d){var e=bsw.arrayIntersect(d,a.init.disabledList),f=a[b].getFieldValue(c),g=[],h=!0,i=!1,j=void 0;try{for(var k,l=d[Symbol.iterator]();!(h=(k=l.next()).done);h=!0){var m=k.value;e.includes(m)&&f.includes(m)&&g.push(m)}}catch(a){i=!0,j=a}finally{try{!h&&l.return&&l.return()}finally{if(i)throw j}}a[b].setFieldsValue(_defineProperty({},c,g))})}}});