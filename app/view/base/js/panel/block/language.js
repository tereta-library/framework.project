import AdminTemplate from './template.js';
import Syntax from "../syntax.js";
import { singletone as syntaxTranslateSingletone } from '../syntax/translate.js';

export default class AdminLanguage extends AdminTemplate {
    template = 'admin/language';
    listing = [];

    async init() {
        const languageList = await this.loadList();

        languageList.forEach((item) => {
            item.choose = this.chooseLanguage.bind(this, item);
        });

        this.syntax = (new Syntax(this.node, {
            'languageList': languageList
        }));
        this.syntax.update();
    }

    async chooseLanguage(item) {
        this.hide();

        localStorage.setItem('language', item.code);
        await this.rootAdminJs.loadTranslation(item.code);
        syntaxTranslateSingletone.update();
    }

    isShown() {
        if (this.config.container.classList.contains('show')) {
            return true;
        }

        return false;
    }

    show() {
        this.config.container.classList.add('show');
    }

    hide() {
        this.config.container.classList.remove('show');
    }

    async loadList() {
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        await fetch('/api/admin/translate', {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "Authorization": "Bearer " + token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        return formData;
    }
}
