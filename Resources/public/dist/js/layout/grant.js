/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-08-14 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}bsw.configure({method:{changeSelectedList:function(a){var b=this,c="persistenceForm";$.each(b.init.selectedList,function(d,e){var f=bsw.arrayIntersect(e,b.init.disabledList),g=b[c].getFieldValue(d),h=[],i=!0,j=!1,k=void 0;try{for(var l,m=e[Symbol.iterator]();!(i=(l=m.next()).done);i=!0){var n=l.value,o=a(n,f,g);o&&h.push(n)}}catch(a){j=!0,k=a}finally{try{!i&&m.return&&m.return()}finally{if(j)throw k}}b[c].setFieldsValue(_defineProperty({},d,h))})},selectAll:function(){this.changeSelectedList(function(a,b,c){return!b.includes(a)||c.includes(a)})},unSelectAll:function(){this.changeSelectedList(function(a,b,c){return b.includes(a)&&c.includes(a)})}}});