//
// For ck-editor 5
//

class FileUploadAdapter {

    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('file', this.loader.file);
            data.append('type', 'img');

            bsw.request(ajaxUploadAction, data, null, true).then(function (res) {
                if (res.errorCode) {
                    reject(res.message);
                } else {
                    resolve({
                        default: res.file
                    });
                }
            });
        });
    }

    abort() {
    }
}