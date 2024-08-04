import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';
import {Editor as EditorLibrary} from '../../library/editor/editor.js';

export class AdminMenuForm extends AdminTemplateForm {
    template = 'admin/page/form';
    id = null;
    onChangeKeyTimeout = [];

    show(config) {
        this.id = config.id;
        this.syntax.set('title', config.title);
        this.syntax.set('identifier', config.identifier);
        this.syntax.set('header', config.header);
        this.syntax.set('content', config.content);
        this.syntax.set('meta_keywords', config.meta_keywords);
        this.syntax.set('meta_description', config.meta_description);

        this.syntax.update();
        super.show();

        this.editorNode.update();
    }

    init() {
        this.syntax = (new Syntax(this.node, {
            successMessage: '',
            showSuccessMessage: false,
            title: '',
            identifier: '',
            header: '',
            content: '',
            meta_keywords: '',
            meta_description: '',
            isSave: false,
            onRemove: this.onRemove.bind(this),
            onClose: this.onClose.bind(this),
            saveForm: this.saveForm.bind(this),
            onKeyup: this.onKeyup.bind(this),
            onChange: this.onChange.bind(this),

            onPasteFile: this.onPasteFile.bind(this)
        }));

        this.syntax.update();

        this.editorNode = new EditorLibrary(this.node.querySelector('.page_editor'));
        this.editorNode.show();
    }

    onRemove(event, element, variable) {
        const token = this.rootAdminJs.getToken();
        const approve = confirm('Are you sure?');
        if (!approve) {
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/admin/page/remove', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.setRequestHeader('Authorization', "Bearer " + token);
        xhr.onload = (xhr) => {
            if (xhr.target.status !== 200) {
                return;
            }

            this.syntax.set('successMessage', 'Page removed')
                .set('isSave', false)
                .set('showSuccessMessage', true).update();

            setTimeout(() => {
                syntax.set('showSuccessMessage', false);
                syntax.update();
            }, 5000);

            const responseText = JSON.parse(xhr.target.responseText);
            window.location.href=responseText.redirect;
        };

        xhr.send(JSON.stringify({
            id: this.id
        }));
    }

    async onPasteFile(event, element, variable) {
        const clipboardItems = event.clipboardData.items;
        const clipboardItem = clipboardItems[clipboardItems.length - 1];

        if (!['image/png', 'image/jpg', 'image/jpeg', 'image/webp', 'image/gif'].includes(clipboardItem.type)) {
            return;
        }

        const file = clipboardItem.getAsFile();
        const cursorStart = element.selectionStart;
        const cursorEnd = element.selectionEnd;
        let text = element.value;

        if (cursorStart != cursorEnd) {
            text = text.substring(0, cursorStart) + text.substring(cursorEnd);
        }

        const fileUrl = this.insertFile(this.id, file);

        const insertTag = `<img src="${fileUrl}" />`;

        text = text.substring(0, cursorStart) + insertTag + text.substring(cursorStart);
        element.value = text;
        element.setSelectionRange(cursorStart + insertTag.length, cursorStart + insertTag.length);
    }

    insertFile(pageId, file) {
        let responseJson = null;
        const formData = new FormData();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/admin/page/uploadFile', false);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('Authorization', "Bearer " + this.rootAdminJs.getToken());
        formData.append('fileName', file.name);
        formData.append('pageId', pageId);
        formData.append('file', file);

        xhr.onload = function(xhr) {
            if (this.status !== 200) {
                return;
            }

            responseJson = JSON.parse(this.responseText);
        };
        xhr.send(formData);
        return responseJson.fileUrl;
    }

    saveForm(event, element, variable) {
        const token = this.rootAdminJs.getToken();
        const syntax = this.syntax;
        event.preventDefault();

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/admin/page', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.setRequestHeader('Authorization', "Bearer " + token);
        xhr.onload = (xhr) => {
            if (xhr.target.status !== 200) {
                return;
            }

            const responseText = JSON.parse(xhr.target.responseText);

            this.syntax.set('successMessage', 'Page saved')
                .set('isSave', false)
                .set('showSuccessMessage', true).update();

            if (responseText.redirect) {
                window.location.href=responseText.redirect;
            }

            setTimeout(() => {
                syntax.set('showSuccessMessage', false);
                syntax.update();
            }, 5000);
        };

        xhr.send(JSON.stringify({
            id: this.id,
            title: this.syntax.get('title'),
            identifier: this.syntax.get('identifier'),
            header: this.syntax.get('header'),
            content: this.syntax.get('content'),
            meta_keywords: this.syntax.get('meta_keywords'),
            meta_description: this.syntax.get('meta_description')
        }));
    }
}
