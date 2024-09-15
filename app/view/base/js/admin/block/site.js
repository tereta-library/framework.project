import AdminTemplate from './template.js';
import Syntax from '../syntax.js';

export class AdminSite extends AdminTemplate {
    template = 'block/site';
    form = null;

    /**
     * Initialize the class
     */
    init() {
        this.syntax = new Syntax(this.node, {
            'openConfiguration': () => {
                this.openConfiguration();
            }
        });
        this.syntax.update();
    }

    /**
     * Button click event
     *
     * @returns {Promise<void>}
     */
    async openConfiguration() {
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        await fetch('/api/json/site/configuration', {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "API-Token": token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        if (formData.error && formData.errorCode == 401) {
            this.rootAdminJs.elementPanel.actionLogout();
            return;
        }

        formData.configs = this.config.elements;

        // Show admin form
        this.form = await this.showForm('block/site/form', formData, this.form);
    }
}
