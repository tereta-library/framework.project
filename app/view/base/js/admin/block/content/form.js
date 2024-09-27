import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';
import {Editor as EditorLibrary} from '../../../library/editor/editor.js';

export class AdminMenuForm extends AdminTemplateForm {
    template = 'block/content/form';
    id = null;
    onChangeKeyTimeout = [];

    show(config) {
        this.id = config.id;
        this.syntax.set('mode', config.id ? 'edit' : 'new');
        this.syntax.set('id', config.id);
        this.syntax.set('identifier', config.identifier);
        this.syntax.set('status', config.status);
        this.syntax.set('seoUri', config.seoUri);
        this.syntax.set('seoTitle', config.seoTitle);
        this.syntax.set('header', config.header);
        this.syntax.set('description', config.description);
        this.syntax.set('content', config.content);
        this.syntax.set('css', config.css);

        this.syntax.update();
        super.show();

        this.editorContentNode.update();
        this.editorDescriptionNode.update();
    }

    getMode(name) {
        if (!this.syntax.get('mode')) {
            return name;
        }
        return name + this.syntax.get('mode').charAt(0).toUpperCase() + this.syntax.get('mode').slice(1)
    }

    init() {
        this.syntax = (new Syntax(this.node, {
            successMessage    : '',
            errorMessage      : '',
            showSuccessMessage: false,
            showErrorMessage  : false,
            id                : '',
            identifier        : '',
            status            : '',
            seoUri            : '',
            seoTitle          : '',
            header            : '',
            description       : '',
            content           : '',
            css               : 'testCSS',
            isSave            : false,
            onRemove          : this.onRemove.bind(this),
            onClose           : this.onClose.bind(this),
            saveForm          : this.saveForm.bind(this),
            onKeyup           : this.onKeyup.bind(this),
            onChange          : this.onChange.bind(this),
            onPasteFile       : this.onPasteFile.bind(this),
            getMode           : this.getMode.bind(this)
        }));

        this.syntax.update();

        this.editorContentNode = new EditorLibrary(this.node.querySelector('.pageContentEditor'));
        this.editorContentNode.show();

        this.editorCssNode = new EditorLibrary(this.node.querySelector('.pageCssEditor'));
        this.editorCssNode.show();

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

            const responseText = JSON.parse(xhr.target.responseText);

            if (responseText.error) {
                this.syntax.set('errorMessage', responseText.error)
                    .set('showSuccessMessage', false)
                    .set('showErrorMessage', true)
                    .update();
            } else {
                this.syntax.set('successMessage', 'Page successfully removed')
                    .set('id', null)
                    .set('isSave', true)
                    .set('showSuccessMessage', true)
                    .set('showErrorMessage', false)
                    .update();
            }

            setTimeout(() => {
                this.syntax.set('showSuccessMessage', false).set('showSuccessMessage', false).update();
            }, 5000);

            if ('#' + responseText.seoUri == location.hash) {
                window.location.hash = '';
            }
        };

        xhr.send(JSON.stringify({
            id: this.syntax.get('id')
        }));
    }

    async onPasteFile(event, element, variable) {
        const clipboardItems = event.dataTransfer.items;
        const clipboardItem = clipboardItems[clipboardItems.length - 1];

        event.preventDefault();

        const file = clipboardItem.getAsFile();
        const cursorStart = element.selectionStart;
        const cursorEnd = element.selectionEnd;
        let text = element.value;

        if (cursorStart != cursorEnd) {
            text = text.substring(0, cursorStart) + text.substring(cursorEnd);
        }

        const fileResponse = this.insertFile(this.id, file);

        const insertTag = fileResponse.type == 'image' ?
            `<img src="${fileResponse.url}" />` :
            `<a href="${fileResponse.url}">${fileResponse.name}</a>`;

        text = text.substring(0, cursorStart) + insertTag + text.substring(cursorStart);
        element.value = text;
        element.setSelectionRange(cursorStart + insertTag.length, cursorStart + insertTag.length);
        this.syntax.set('content', text);
        this.syntax.set('isSave', true);
        this.syntax.update();
        element.cursorStart = element.selectionEnd = cursorStart;
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
        return responseJson;
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
            } else if (responseText.content.id) {
                this.syntax.set('successMessage', 'Page saved')
                    .set('id', responseText.content.id)
                    .set('isSave', false)
                    .set('showErrorMessage', false)
                    .set('showSuccessMessage', true).update();

                if ('#' + responseText.url.uri != window.location.hash) {
                    window.location.hash = responseText.url.uri;
                } else {
                    this.rootAdminJs.elementCanvas.contentWindow.location.reload();
                }
            }

            setTimeout(() => {
                syntax.set('showSuccessMessage', false);
                syntax.set('showErrorMessage', false);
                syntax.update();
            }, 5000);
        };

        xhr.send(JSON.stringify({
            id         : this.syntax.get('id'),
            identifier : this.syntax.get('identifier'),
            status     : this.syntax.get('status') === 'yes' ? true : false,
            seoUri     : this.syntax.get('seoUri'),
            seoTitle   : this.syntax.get('seoTitle'),
            header     : this.syntax.get('header'),
            description: this.syntax.get('description'),
            content    : this.syntax.get('content'),
            css        : this.syntax.get('css'),
        }));
    }
}
