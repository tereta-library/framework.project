import AdminTemplate from './template.js';
import Syntax from '../syntax.js';

export class AdminSite extends AdminTemplate {
    template = 'block/site';
    form = null;

    init() {
        this.syntax = new Syntax(this.node);
        this.syntax.update();
        this.node.addEventListener('click', this.buttonClick.bind(this));
    }

    async buttonClick() {
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        await fetch('/api/json/site/configuration', {
            method: "POST",
            headers: {
                "Cache-Control": "no-cache",
                "Authorization": "Bearer " + token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        if (formData.error) {
            alert(formData.error);
            this.rootAdminJs.elementPanel.actionLogout();
            return;
        }
debugger;
        // Show admin form
        this.form = await this.showForm('block/site/form', formData, this.form);
    }
}
