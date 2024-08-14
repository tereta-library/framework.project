import Block from './block.js';
import { singletone as syntaxTranslateSingletone } from './syntax/translate.js';

const adminSingletone = new class {
    constructor(element = null) {
        this.instance = [];
    }

    register(instance) {
        this.instance.push(instance);
    }

    dispatch(func, ...vars) {
        this.instance.forEach((item) => {
            item[func].apply(item, vars);
        })
    }
}

export { adminSingletone };

export class Manager {
    syntax           = null;
    elementCanvas    = null;
    containerPanel   = null;
    elementPanel     = null;
    loginPopup       = null;
    languageDirectory= null;

    constructor(elementCanvas, containerPanel, languageDirectory) {
        this.elementCanvas = elementCanvas;
        this.containerPanel = containerPanel;
        this.languageDirectory = languageDirectory;
        this.block = new Block(this);

        adminSingletone.register(this);
    }

    async getBlock(path) {
        return await this.block.getBlock(path);
    }

    /**
     * Show admin panel
     */
    async show()
    {
        if (!localStorage.getItem('adminToken')) {
            return this.showLogin();
        }

        if (!this.elementPanel) {
            this.elementPanel = await this.getBlock('block/panel');
            await this.elementPanel.render(this.containerPanel);
            setTimeout(() => this.elementPanel.show(), 1000);
        } else {
            this.elementPanel.show()
        }
    }

    /**
     *
     * @param logoutAction
     * @param params
     * @returns {Promise<*>}
     */
    async showLogin(logoutAction = null, params = [])
    {
        if (params.length == 1) {
            this.authorizeByKey(params[0], logoutAction);
            return;
        }

        if (this.loginPopup) {
            this.loginPopup.show();
            return;
        }

        const loginPopup = await this.getBlock('block/login');
        await loginPopup.render(this.containerPanel);
        loginPopup.show();
        loginPopup.setLogoutAction(logoutAction);

        this.loginPopup = loginPopup;
        return loginPopup;
    }

    /**
     * @param code
     * @returns {Promise<void>}
     */
    async loadTranslation(code = null)
    {
        let translationMap = {};

        if (!code) {
            code = localStorage.getItem('language');
        }

        if (!code) {
            code = 'en_US';
        }

        await fetch(`${this.languageDirectory}/${code}.json`, {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            translationMap = json;
        });

        syntaxTranslateSingletone.setTranslateMap(translationMap);
    }

    showCss(documentUrl)
    {
        const link = document.createElement('link');

        if (document.head.querySelector('link[href="' + documentUrl + '"]')) {
            return;
        }

        link.setAttribute('rel', 'stylesheet');
        link.setAttribute('href', documentUrl);

        document.head.appendChild(link);
    }

    async authorizeByKey(key, logoutAction = null)
    {
        fetch('/api/admin/token', {
            method: "POST",
            body: JSON.stringify({key: key}),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then(async (json) => {
            if (!json.token) {
                await this.showLogin(logoutAction, []);
                this.loginPopup.syntax.
                    set('errorMessage', json.message).
                    set('isError', true).
                    update();

                return;
            }

            localStorage.setItem('adminToken', json.token);
            if (logoutAction) {
                logoutAction();
            }
            this.show();
        });
    }

    hideLogin() {
        if (!this.loginPopup) {
            return;
        }

        this.loginPopup.hide();
    }

    getToken()
    {
        const token = localStorage.getItem('adminToken');
        if (!token) {
            return null;
        }

        return token;
    }

    clearToken()
    {
        localStorage.removeItem('adminToken');
    }
}
