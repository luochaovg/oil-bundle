"use strict";

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

app.configure({

    data: {
        selected_list: {},
        disabled_list: []
    },

    method: {
        selectAll: function selectAll() {

            var that = this;
            var data = that[that.keyForForm];

            $.each(this.selected_list, function (key, meta) {

                var disabled = bsw.arrayIntersect(meta, that.disabled_list);
                var selected = data.getFieldValue(key);

                var values = [];
                var _iteratorNormalCompletion = true;
                var _didIteratorError = false;
                var _iteratorError = undefined;

                try {
                    for (var _iterator = meta[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                        var v = _step.value;

                        if (disabled.includes(v)) {
                            if (selected.includes(v)) {
                                values.push(v);
                            }
                        } else {
                            values.push(v);
                        }
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return) {
                            _iterator.return();
                        }
                    } finally {
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }

                data.setFieldsValue(_defineProperty({}, key, values));
            });
        },
        unSelectAll: function unSelectAll() {

            var that = this;
            var data = that[that.keyForForm];

            $.each(this.selected_list, function (key, meta) {

                var disabled = bsw.arrayIntersect(meta, that.disabled_list);
                var selected = data.getFieldValue(key);

                var values = [];
                var _iteratorNormalCompletion2 = true;
                var _didIteratorError2 = false;
                var _iteratorError2 = undefined;

                try {
                    for (var _iterator2 = meta[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                        var v = _step2.value;

                        if (disabled.includes(v) && selected.includes(v)) {
                            values.push(v);
                        }
                    }
                } catch (err) {
                    _didIteratorError2 = true;
                    _iteratorError2 = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion2 && _iterator2.return) {
                            _iterator2.return();
                        }
                    } finally {
                        if (_didIteratorError2) {
                            throw _iteratorError2;
                        }
                    }
                }

                data.setFieldsValue(_defineProperty({}, key, values));
            });
        }
    }
});
