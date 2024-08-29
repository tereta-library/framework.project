import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';
import {Editor as EditorLibrary} from '../../../library/editor/editor.js';

export class AdminMenuForm extends AdminTemplateForm {
    template = 'block/content/form';
    id = null;
    onChangeKeyTimeout = [];

    show(config) {
        this.id = config.id;
        this.syntax.set('id', config.id);
        this.syntax.set('identifier', config.identifier);
        this.syntax.set('status', config.status);
        this.syntax.set('seoUri', config.seoUri);
        this.syntax.set('seoTitle', config.seoTitle);
        this.syntax.set('header', config.header);
        this.syntax.set('description', config.description);
        this.syntax.set('content', config.content);

        this.syntax.update();
        super.show();

        this.editorContentNode.update();
        this.editorDescriptionNode.update();
    }

    init() {
        this.syntax = (new Syntax(this.node, {
            successMessage    : '',
            showSuccessMessage: false,
            id                : '',
            identifier        : '',
            status            : '',
            seoUri            : '',
            seoTitle          : '',
            header            : '',
            description       : '',
            content           : '',
            isSave            : false,
            onRemove          : this.onRemove.bind(this),
            onClose           : this.onClose.bind(this),
            saveForm          : this.saveForm.bind(this),
            onKeyup           : this.onKeyup.bind(this),
            onChange          : this.onChange.bind(this),

            onPasteFile       : this.onPasteFile.bind(this)
        }));

        this.syntax.update();

        this.editorContentNode = new EditorLibrary(this.node.querySelector('.pageContentEditor'));
        this.editorContentNode.show();

        this.editorDescriptionNode = new EditorLibrary(this.node.querySelector('.pageDescriptionEditor'));
        this.editorDescriptionNode.show();
    }

    onRemove(event, element, variable) {
        const token = this.rootAdminJs.getToken();
        const approve = confirm('Are you sure?');
        if (!approve) {
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/json/content/remove', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());
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
        xhr.open('POST', '/api/json/content/uploadFile', false);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());
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
        xhr.open('POST', '/api/json/content/save', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());
        xhr.onload = (xhr) => {
            if (xhr.target.status !== 200) {
                return;
            }

            const responseText = JSON.parse(xhr.target.responseText);

            if (responseText.error) {
                this.syntax.set('errorMessage', responseText.error)
                    .set('isSave', false)
                    .set('showSuccessMessage', false)
                    .set('showErrorMessage', true).update();

                return;
            } else if (responseText.id) {
                this.syntax.set('successMessage', 'Page saved')
                    .set('id', responseText.id)
                    .set('isSave', false)
                    .set('showErrorMessage', false)
                    .set('showSuccessMessage', true).update();
            }

            setTimeout(() => {
                syntax.set('showSuccessMessage', false);
                syntax.set('showErrorMessage', false);
                syntax.update();
            }, 5000);
        };

        xhr.send(JSON.stringify({
            id: this.syntax.get('id'),
            identifier: this.syntax.get('identifier'),
            status: this.syntax.get('status') === 'yes' ? true : false,
            seoUri: this.syntax.get('seoUri'),
            seoTitle: this.syntax.get('seoTitle'),
            header: this.syntax.get('header'),
            description: this.syntax.get('description'),
            content: this.syntax.get('content')
        }));
    }
}
