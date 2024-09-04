import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';

export class AdminFilesForm extends AdminTemplateForm {
    template = 'block/page/files';

    async init() {
        const dataDir = await this.getDirectory('/');

        this.syntax = (new Syntax(this.node, {
            successMessage: '',
            showSuccessMessage: false,
            pathDir: dataDir.pathDir,
            folderData: this.prepareSyntaxData(dataDir),
            changePathDir: this.changePathDir.bind(this),
            clickBackDir: this.clickBackDir.bind(this),
            clickEnterDir: this.clickEnterDir.bind(this),
            clickAddFolder: this.clickAddFolder.bind(this),
            changeUploadFile: this.changeUploadFile.bind(this),
            fileInfo: { show: false, url: '', path: '', size: '', type: '' },
            closeFileInfo: this.closeFileInfo.bind(this),
            isSave: false,
            onClose: this.onClose.bind(this),
        }));

        this.syntax.update();
    }

    closeFileInfo() {
        const fileInfo = this.syntax.get('fileInfo');
        fileInfo.show = false;
        this.syntax.update();
    }

    prepareSyntaxData(data) {
        data.files = data.listing.map((item) => {
            item.clickFolderItem = () => {
                this.syntax.set('pathDir', item.path);
                this.updateDir();
            }
            item.clickFileItem = () => {
                this.syntax.set('fileInfo', {
                    show: true,
                    url: item.url,
                    path: item.path,
                    size: item.size,
                    type: item.mimeType,
                });
                this.syntax.update();
            }
            item.clickRemove = async () => {
                const appendFormData = {
                    'path': item.path,
                    'pathDir': this.syntax.get('pathDir'),
                };

                const data = await this.itemAction('remove', appendFormData);
                this.updateDir(data);
            }
            return item;
        });

        return data;
    }

    async itemAction(action, appendFormData = null) {
        let responseJson = null;
        const formData = new FormData();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/api/json/files/${action}`, false);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());

        if (!appendFormData) {
            appendFormData = {};
        }

        Object.keys(appendFormData).forEach((key) => {
            formData.append(key, appendFormData[key]);
        });

        xhr.onload = function() {
            if (this.status !== 200) {
                return;
            }

            responseJson = JSON.parse(this.responseText);
        };
        xhr.send(formData);
        return responseJson;
    }

    async getDirectory(pathDir) {
        let responseJson = null;
        const formData = new FormData();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/json/files/getDirectory', false);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());
        formData.append('pathDir', pathDir);
        xhr.onload = function() {
            if (this.status !== 200) {
                return;
            }

            responseJson = JSON.parse(this.responseText);

            if (responseJson.error && responseJson.errorCode == 401) {
                localStorage.removeItem('adminToken');
            }
        };
        xhr.send(formData);

        return responseJson;
    }

    changePathDir(event) {
        const pathDir = event.target.value;
        this.syntax.set('pathDir', pathDir);
        this.updateDir();
    }

    clickBackDir() {
        const pathDir = this.syntax.get('pathDir');
        const pathDirArray = pathDir.split('/');
        pathDirArray.pop();

        let pathString = pathDirArray.join('/');
        if (pathString.trim() === '') {
            pathString = '/';
        }

        console.info('pathString', pathString);
        this.syntax.set('pathDir', pathString);
        this.updateDir();
    }

    clickEnterDir() {
        this.updateDir();
    }

    async clickAddFolder() {
        const pathDir = this.syntax.get('pathDir');
        const name = prompt('Enter name folder');
        if (!name) {
            return;
        }
        const pathDirString = pathDir.replace(/\/$/g, '');
        const appendFormData = {
            'path': `${pathDirString}/${name}`,
            'pathDir': this.syntax.get('pathDir'),
        };
        const data = await this.itemAction('createFolder', appendFormData);
        this.updateDir(data);
    }

    async changeUploadFile(event) {
        const files = event.target.files;
        let formData = {
            'pathDir': this.syntax.get('pathDir'),
        };
        for (let i = 0; i < files.length; i++) {
            formData['file'] = files[i];
        }

        const data = await this.itemAction('uploadFile', formData);
        this.updateDir(data);
        event.target.value = '';
    }

    async updateDir(data = null) {
        if (!data) {
            data = await this.getDirectory(
                this.syntax.get('pathDir')
            );
        }

        if (data.error) {
            this.syntax.set('folderData', this.prepareSyntaxData({
                listing: [],
                error: data.error,
                pathDir: this.syntax.get('pathDir'),

            }));
            this.syntax.update();
            return;
        }

        this.syntax.set('folderData', this.prepareSyntaxData(data));
        this.syntax.update();
    }
}
