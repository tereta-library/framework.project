import Syntax from '../../../syntax.js';

export class AdminSiteFormTheme {
    slider = null;
    parent = null;
    locked = false;
    themeId = 1;
    themeItemTemplate = null;

    constructor(parent, slider, themeItemTemplate) {
        this.slider = slider;
        this.parent = parent;
        this.themeItemTemplate = themeItemTemplate;
    }

    render() {
        this.themeLoad(
            this.themeId,
            (data, themeId) => {
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
                const content = this.renderItem(data);
                this.slider.previous(content, this.themeLoaded.bind(this, themeId))
            }
        );
    }

    next() {
        this.themeLoad(
            this.themeId + 1,
            (data, themeId) => {
                const content = this.renderItem(data);
                this.slider.next(content, this.themeLoaded.bind(this, themeId));
            }
        );
    }

    renderItem(data) {
        const element = document.createElement('div');
        element.innerHTML = this.themeItemTemplate;

        (new Syntax(element, data)).update();

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
        xhr.open('GET', '/api/json/site/configuration/theme/' + themeId, true);
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