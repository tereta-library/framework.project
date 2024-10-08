import Syntax from '../../../syntax.js';
import {Editor as EditorLibrary} from "../../../../library/editor/editor.js";

export class AdminSiteFormTheme {
    slider = null;
    parent = null;
    locked = false;
    themeId = null;
    themeItemTemplate = null;
    themeIdentifier = null;
    themeIdentifierSelected = null;
    reloadCurrentAttempted = false;
    editorContentNode = null;
    customCss = '';
    themeCssEditorElement = null;

    constructor(parent, slider, themeItemTemplate) {
        this.slider = slider;
        this.parent = parent;
        this.themeItemTemplate = themeItemTemplate;
        this.themeCssEditorElement = parent.node.querySelector('.themeCssEditor');
        this.editorContentNode = new EditorLibrary(this.themeCssEditorElement);
        this.editorContentNode.show();
    }

    setSiteData(configs) {
        this.customCss = configs.style;
        this.themeCssEditorElement.value = this.customCss;
    }

    themeSelect() {
        this.themeIdentifierSelected = this.themeIdentifier;
        this.parent.syntax.set('isSave', true).update();
        this.currentSyntax.set('isLocked', true).update();
    }

    save(formData) {
        formData.append(`additionalConfig[view.template]`, this.themeIdentifier);
        formData.append(`style`, this.customCss);
    }

    changeCss(event, element) {
        this.customCss = element.value;
        this.parent.syntax.set('isSave', true).update();
    }

    render(canLock = true) {
        this.themeLoad(
            this.themeId,
            (data, themeId) => {
                if (this.reloadCurrentAttempted && data.error && data.errorCode === 404) {
                    this.slider.render(data.error);
                    return;
                }

                if (data.errorCode && data.errorCode === 404) {
                    this.themeId = 0;
                    this.reloadCurrentAttempted = true;
                    this.render(false);
                    return;
                }

                this.themeIdentifierSelected = data.identifier;
                if (!canLock) {
                    data.isLocked = false;
                }
                const content = this.renderItem(data);
                this.slider.render(content);
                this.themeLoaded(themeId);
            }
        );
    }

    previous() {
        this.themeLoad(
            '<' + (this.themeId - 1),
            (data, themeId) => {
                if (data.errorCode && data.errorCode === 404) {
                    this.slider.previous(data.error);
                    return;
                }

                const content = this.renderItem(data);
                this.slider.previous(content, this.themeLoaded.bind(this, themeId))
            }
        );
    }

    next() {
        this.themeLoad(
            this.themeId + 1,
            (data, themeId) => {
                if (data.errorCode && data.errorCode === 404) {
                    this.slider.next(data.error);
                    return;
                }

                const content = this.renderItem(data);
                this.slider.next(content, this.themeLoaded.bind(this, themeId));
            }
        );
    }

    renderItem(data) {
        const element = document.createElement('div');
        element.innerHTML = this.themeItemTemplate;

        data.themeSection = this.parent.themeSection;
        if (data.isLocked === undefined) {
            data.isLocked = (data.identifier === this.themeIdentifierSelected);
        }
        this.currentSyntax = (new Syntax(element, data));
        this.currentSyntax.update();

        this.themeIdentifier = data.identifier;
        return element;
    }

    themeLoaded(themeId) {
        this.locked = false;
        this.parent.syntax.update();
        this.themeId = themeId;
    }

    themeLoad(themeId, callback) {
        this.locked = true;
        this.parent.syntax.update();

        const token = this.parent.rootAdminJs.getToken();

        const xhr = new XMLHttpRequest();
        if (themeId === null) {
            xhr.open('GET', '/api/json/site/configuration/theme/current', true);
        } else {
            xhr.open('GET', '/api/json/site/configuration/theme/' + themeId, true);
        }
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', token);

        xhr.onload = function(xhr) {
            if (this.status !== 200) {
                return;
            }

            const jsonResponse = JSON.parse(this.responseText);
            callback(jsonResponse, parseInt(jsonResponse.id));
        }

        xhr.send();
    }
}