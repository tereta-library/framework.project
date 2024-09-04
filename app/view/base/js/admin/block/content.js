import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

export class AdminContent extends AdminTemplate {
    template = 'block/content';

    constructor(rootNode, config) {
        super(rootNode, config);
    }

    init() {
        const blockConfig = this.config.elements[0];

        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.addEventListener('click', this.buttonClick.bind(this, blockConfig));
    }

    async render(element) {
        super.render(element.querySelector('#adminMenuPageListing'));
    }

    async buttonClick(blockConfig) {
        // Load form data
        let formData = null;
        const identifier = blockConfig.init.content;

        await fetch(`/api/json/content/get/${identifier}`, {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "API-Token": this.rootAdminJs.getToken(),
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            formData = json;
        });

        if (formData.error && formData.errorCode == 401) {
            this.rootAdminJs.elementPanel.actionLogout();
            return;
        }

        // Show admin form
        this.form = await this.showForm('block/content/form', formData, this.form);
    }
}
