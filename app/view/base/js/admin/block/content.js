import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

export class AdminContent extends AdminTemplate {
    template = 'block/content';

    constructor(rootNode, config) {
        super(rootNode, config);
    }

    init() {
        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.addEventListener('click', this.buttonClick.bind(this));
    }

    async render(element) {
        element = element.querySelector('#adminPageCreateListing');

        super.render(element);
    }

    async buttonClick() {
        // Load form data
        let formData = null;

        // api/json/site/configuration
        const url = (new URL(window.location.protocol + '//' + window.location.hostname + '/api/json/content/configuration'));
        // @todo: Commented because made as create but need to add also edit method
        //url.searchParams.append('identifier', elementConfig.identifier);

        await fetch(url, {
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
        this.form = await this.showForm('block/content/form', formData, this.form);
    }
}
