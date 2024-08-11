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
        // const config = this.config[0];
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        await fetch('/api/admin/site', {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "Authorization": "Bearer " + token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        // Show admin form
        this.form = await this.showForm('admin/site/form', formData, this.form);
    }
}
