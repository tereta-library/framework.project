import AdminTemplate from './template.js';
import Syntax from '../syntax.js';
import AdminLanguage from "./language.js";

export class Admin extends AdminTemplate{
    buttonLogout = null;
    containerList = null;
    template = 'block/toolbar';

    onCanvasLoaded = () => {};

    show() {
        if (!localStorage.getItem('adminToken')) {
            console.warn('Token not found');
            return;
        }

        this.node.classList.add('show');

        this.handleBlocks();
    }

    hide() {
        this.node.classList.remove('show');
    }

    showForms() {
        this.containerPages.classList.add('show');
    }

    hideForms() {
        this.containerPages.classList.remove('show');
    }

    showMenu() {
        this.hideForms();
        this.containerList.classList.add('show');
    }

    toggleMenu() {
        if (this.containerList.classList.contains('show')) {
            this.hideMenu();
        } else {
            this.showMenu();
        }
    }

    hideMenu() {
        this.containerList.classList.remove('show');
        this.languageBlock.hide();
    }

    hideAllElements() {
        this.hideMenu();
        this.hideForms();
        this.containerPages.childNodes.forEach((item) => {
            item.handler.hide();
        });
    }

    useInitialComment(node, adminList) {
        const item = node.nodeValue.match(/^ ?@dataAdmin +({.*}) *$/);
        if (!item) {
            return;
        }

        adminList.push({
            'element': node.parentNode,
            'init': JSON.parse(item[1])
        });
    }

    handleInitialComments(node, adminList) {
        if (node.nodeType === Node.COMMENT_NODE) {
            this.useInitialComment(node, adminList);
        }

        for (let child = node.firstChild; child; child = child.nextSibling) {
            this.handleInitialComments(child, adminList);
        }
    }

    async handleBlocks() {
        const hash = window.location.hash.startsWith('#') ? window.location.hash.substring(1) : window.location.hash;
        const hashUrl = window.location.origin + (
            hash ? (
                hash.startsWith('/') ? hash : '/' + hash
            ) : '/'
        );

        if (hashUrl != this.rootAdminJs.elementCanvas.src) {
            this.onCanvasLoaded = () => this.handleBlocks();
            this.rootAdminJs.elementCanvas.src = hashUrl ;
            return;
        }

        let adminList = [];

        this.containerList.innerHTML = '';
        const canvas = this.rootAdminJs.elementCanvas.contentWindow.document;

        this.handleInitialComments(canvas, adminList);

        canvas.querySelectorAll('[data-admin-init]').forEach((item) => {
            adminList.push({
                'element': item,
                'init': JSON.parse('{' + item.getAttribute('data-admin-init') + '}')
            });
        });

        const objects = await this.loadHandleBlocks(adminList);

        // @todo need to create sequence of rendering
        Object.keys(objects).forEach((moduleName) => {
            const sequence = objects[moduleName].config.sequence;
            if (!sequence) {
                return;
            }

            objects[sequence].renderSequenceList.push(objects[moduleName]);
            delete objects[moduleName];
        });

        Object.keys(objects).forEach((moduleName) => {
            objects[moduleName].render(this.containerList);
        })
    }

    async loadHandleBlocks(moduleConfigs) {
        let modules = [];
        let moduleNames = [];
        let moduleImport = [];
        let moduleList = {};
        let moduleConfigsNamed = {};
        let moduleConfigsModule = {};
        const token = this.rootAdminJs.getToken();

        moduleConfigs.forEach((item) => {
            item.module = Object.keys(item.init)[0];
            item.config = item.init[item.module];
            if (typeof moduleConfigsNamed[item.module] === 'undefined') {
                moduleConfigsNamed[item.module] = [];
            }
            moduleConfigsNamed[item.module].push(item);

            if (!modules.includes(item.module)) {
                modules.push(item.module);
            }
        });

        await fetch('/resource/base/json/modules.json', {
            method: "GET",
            headers: {
                "API-Token": token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            Object.keys(json.modules).forEach((moduleName) => {
                const moduleConfig = json.modules[moduleName];
                if (!moduleConfig.permanent && !modules.includes(moduleName)) {
                    return;
                }

                moduleConfigsModule[moduleName] = moduleConfig;
                moduleNames.push(moduleName);
                moduleImport.push(import(`/resource/base/js/${moduleConfig.js}.js`));
            });
        }).catch((error) => {
            this.hideAllElements();
            this.actionLogout();
        });

        (await Promise.all(moduleImport)).forEach((moduleItem, key) => {
            const className = Object.keys(moduleItem)[0];
            const config = moduleConfigsModule[moduleNames[key]];
            config.elements = moduleConfigsNamed[moduleNames[key]];
            moduleList[moduleNames[key]] = new (moduleItem[className])(this.rootAdminJs, config);
        });

        return moduleList;
    }

    init() {
        this.syntax = new Syntax(this.node);

        this.buttonLogout = this.node.querySelector('[data-button=logOut]');
        this.buttonLogout.addEventListener('click', this.actionLogout.bind(this));

        this.buttonLanguage = this.node.querySelector('[data-button=language]');
        this.buttonLanguage.addEventListener('click', this.actionLanguage.bind(this));

        this.buttonBlockList = this.node.querySelector('[data-button=blockList]');
        this.buttonBlockList.addEventListener('click', this.toggleMenu.bind(this));

        this.containerLanguage = this.node.querySelector('[data-container=containerLanguage]');
        this.containerList = this.node.querySelector('[data-container=containerList]');
        this.containerPages = this.node.querySelector('[data-conatiner=containerPages]');

        this.languageBlock = new AdminLanguage(this.rootAdminJs, {
            'container': this.containerLanguage
        });
        this.languageBlock.render(this.containerLanguage);

        window.addEventListener("hashchange", (event) => this.handleBlocks());

        this.rootAdminJs.elementCanvas.addEventListener('load', () => this.onCanvasLoaded())

        this.syntax.update();
    }

    async actionLanguage() {
        if (this.languageBlock.isShown()) {
            this.languageBlock.hide();
            return;
        }

        this.languageBlock.show();
    }

    actionLogout() {
        localStorage.removeItem('adminToken');
        this.hideAllElements();
        this.hide();
        this.rootAdminJs.showLogin();
    }
}
