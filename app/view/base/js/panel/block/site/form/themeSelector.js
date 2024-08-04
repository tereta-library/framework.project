import Template from '../../../template.js';
import Syntax from '../../../syntax.js';

export default class ThemeSelector extends Template
{
    template = 'admin/site/form/themeSelector';

    page = 0;
    element = null;
    initialThemeId = null;
    scope = null;
    parent = null;
    variablesMap = {
        'header': {
            'listing': 'themeHeaderListing',
            'value': 'themeHeader'
        },
        'body': {
            'listing': 'themeBodyListing',
            'value': 'themeBody'
        },
        'footer': {
            'listing': 'themeFooterListing',
            'value': 'themeFooter'
        }
    };

    constructor(parent) {
        super();

        this.parent = parent;
    }

    async render(element) {
        await super.render(element);

        document.body.appendChild(element);

        this.syntax = new Syntax(element, {
            'close': this.close.bind(this),
            'showPrevious': this.showPrevious.bind(this),
            'showNext': this.showNext.bind(this),
            'applyCurrent': this.applyCurrent.bind(this),
            'themeData': null,
            'themeTitle': '',
            'themeThumbnail': '',
            'themeId': null,
            'initialThemeId': null,
            'chooseThemeId': () => {
                return (this.syntax.get('themeId') && this.syntax.get('initialThemeId') != this.syntax.get('themeId'));
            }
        });
        this.syntax.update();

        return this;
    }

    async open(element, scope, themeId = null) {
        this.element = element;
        this.scope = scope;
        this.page = 0;

        if (!themeId) {
            await this.load(scope, 1, true, this.initialThemeId);
        } else {
            this.initialThemeId = themeId;
            this.syntax.set('initialThemeId', themeId);
            await this.load(scope, themeId);
        }

        this.node.parentNode.classList.add('show');
    }

    close()
    {
        this.node.parentNode.classList.remove('show');
    }

    async load(scope, themeId, modePage=false, exclude=null) {
        let formData;
        let url = `/api/admin/theme?themeScope=${scope}&themeId=${themeId}`;
        if (modePage) {
            url = `/api/admin/theme?themeScope=${scope}&themePage=${themeId}&themeExclude=${exclude}`;
        }

        const initialThumbnail = this.syntax.get('themeThumbnail');
        this.syntax.set('themeThumbnail', '').update();

        await fetch(url, {
            method: "GET",
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        if (!formData.themeId) {
            this.syntax.set('themeThumbnail', initialThumbnail).update();
            return false;
        }

        this.syntax.set('themeThumbnail', formData.thumbnail)
            .set('themeTitle', formData.title)
            .set('themeId', formData.themeId)
            .set('themeData', formData)
        this.syntax.update();
        return true;
    }

    showPrevious() {
        this.page = this.page > 1 ? this.page - 1 : 1;

        if (this.page == 1 && this.initialThemeId) {
            this.load(this.scope, this.initialThemeId);
            return;
        } else if (this.page == 1) {
            this.load(this.scope, 1, true, this.initialThemeId);
        }

        this.load(this.scope, this.page, true, this.initialThemeId);
    }

    async showNext() {
        if (!(await this.load(this.scope, this.page + 1, true, this.initialThemeId))) {
            return;
        }

        this.page = this.page + 1;
    }

    applyCurrent() {
        const map = this.variablesMap[this.scope];
        const chosenThemeData = this.syntax.get('themeData');
        const themeId = chosenThemeData.themeId;
        const themeListing = this.parent.syntax.get(map.listing);

        let themeListingFound = false;

        themeListing.forEach((item, key)=> {
            if (item.themeId === themeId) {
                themeListingFound = true;
            }
        });

        if (!themeListingFound) {
            themeListing.push(chosenThemeData);
        }

        this.parent.syntax.set('isSave', true).set(map.value, themeId).update();
        this.close();
    }
}
