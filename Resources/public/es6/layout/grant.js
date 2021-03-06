bsw.configure({
    method: {
        changeSelectedList(handler) {
            let that = this;
            let form = 'persistenceForm';
            $.each(that.init.selectedList, function (key, meta) {
                let disabled = bsw.arrayIntersect(meta, that.init.disabledList);
                let selected = that[form].getFieldValue(key);
                let values = [];
                for (let v of meta) {
                    let result = handler(v, disabled, selected);
                    if (result) {
                        values.push(v);
                    }
                }
                that[form].setFieldsValue({[key]: values});
            });
        },
        selectAll() {
            this.changeSelectedList(function (v, disabled, selected) {
                if (disabled.includes(v)) {
                    return selected.includes(v);
                }
                return true;
            });
        },
        unSelectAll() {
            this.changeSelectedList(function (v, disabled, selected) {
                return disabled.includes(v) && selected.includes(v);
            });
        },
    },
});