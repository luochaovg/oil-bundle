/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2020-04-08 */
"use strict";function _defineProperty(a,b,c){return b in a?Object.defineProperty(a,b,{value:c,enumerable:!0,configurable:!0,writable:!0}):a[b]=c,a}bsw.configure({data:{selected_list:{},disabled_list:[]},method:{selectAll:function(){var a=this;$.each(this.selected_list,function(b,c){var d=bsw.arrayIntersect(c,a.disabled_list),e=a.persistence_form.getFieldValue(b),f=[],g=!0,h=!1,i=void 0;try{for(var j,k=c[Symbol.iterator]();!(g=(j=k.next()).done);g=!0){var l=j.value;d.includes(l)?e.includes(l)&&f.push(l):f.push(l)}}catch(a){h=!0,i=a}finally{try{!g&&k.return&&k.return()}finally{if(h)throw i}}a.persistence_form.setFieldsValue(_defineProperty({},b,f))})},unSelectAll:function(){var a=this;$.each(this.selected_list,function(b,c){var d=bsw.arrayIntersect(c,a.disabled_list),e=a.persistence_form.getFieldValue(b),f=[],g=!0,h=!1,i=void 0;try{for(var j,k=c[Symbol.iterator]();!(g=(j=k.next()).done);g=!0){var l=j.value;d.includes(l)&&e.includes(l)&&f.push(l)}}catch(a){h=!0,i=a}finally{try{!g&&k.return&&k.return()}finally{if(h)throw i}}a.persistence_form.setFieldsValue(_defineProperty({},b,f))})}}});