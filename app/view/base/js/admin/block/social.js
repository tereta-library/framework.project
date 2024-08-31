import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

export class AdminMenu extends AdminTemplate {
    template = 'block/social';
    form = null;

    init() {
        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.addEventListener('click', this.buttonClick.bind(this));
    }

    async buttonClick() {
        // Load form data
        let formData = null;

        await fetch('/api/json/social/configuration', {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "API-Token": this.rootAdminJs.getToken(),
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        // Show admin form
        this.form = await this.showForm('block/social/form', formData, this.form);
    }
}
