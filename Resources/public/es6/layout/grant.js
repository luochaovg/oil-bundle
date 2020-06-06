bsw.configure({
    data: {
        selectedList: {},
        disabledList: []
    },
    method: {
        selectAll() {
            let that = this;
            let form = 'persistenceForm';
            $.each(this.selectedList, function (key, meta) {
                let disabled = bsw.arrayIntersect(meta, that.disabledList);
                let selected = that[form].getFieldValue(key);
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
                that[form].setFieldsValue({[key]: values});
            });
        },
        unSelectAll() {
            let that = this;
            let form = 'persistenceForm';
            $.each(this.selectedList, function (key, meta) {
                let disabled = bsw.arrayIntersect(meta, that.disabledList);
                let selected = that[form].getFieldValue(key);
                let values = [];
                for (let v of meta) {
                    if (disabled.includes(v) && selected.includes(v)) {
                        values.push(v);
                    }
                }
                that[form].setFieldsValue({[key]: values});
            });
        },
    },
});