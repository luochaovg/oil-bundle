bsw.configure({
    data: {
        selected_list: {},
        disabled_list: []
    },
    method: {
        selectAll() {
            let that = this;
            $.each(this.selected_list, function (key, meta) {
                let disabled = bsw.arrayIntersect(meta, that.disabled_list);
                let selected = that.persistence_form.getFieldValue(key);
                let values = [];
                for (let v of meta) {
                    if (disabled.includes(v)) {
                        if (selected.includes(v)) {
                            values.push(v);
                        }
                    } else {
                        values.push(v);
                    }
                }
                that.persistence_form.setFieldsValue({[key]: values});
            });
        },
        unSelectAll() {
            let that = this;
            $.each(this.selected_list, function (key, meta) {
                let disabled = bsw.arrayIntersect(meta, that.disabled_list);
                let selected = that.persistence_form.getFieldValue(key);
                let values = [];
                for (let v of meta) {
                    if (disabled.includes(v) && selected.includes(v)) {
                        values.push(v);
                    }
                }
                that.persistence_form.setFieldsValue({[key]: values});
            });
        },
    },
});