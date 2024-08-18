import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

export class AdminMenu extends AdminTemplate {
    template = 'block/page';

    constructor(rootNode, config) {
        debugger;
        super(rootNode, config);
    }

    init() {
        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.addEventListener('click', this.buttonClick.bind(this));
    }

    async buttonClick() {
        const config = this.config.elements[0];
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        const url = (new URL(window.location.protocol + '//' + window.location.hostname + '/api/admin/page'));
        url.searchParams.append('identifier', config.init.page);

        await fetch(url, {
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
        this.form = await this.showForm('admin/page/form', formData, this.form);
    }
}
