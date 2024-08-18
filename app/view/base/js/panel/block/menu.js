import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

/**
 * Admin menu class
 */
export class AdminMenu extends AdminTemplate {
    /**
     * Template path
     * @type {string}
     */
    template = 'block/menu';

    /**
     * Initialize the class
     */
    init() {
        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.addEventListener('click', this.buttonClick.bind(this));
    }

    /**
     * Button click event
     * @returns {Promise<void>}
     */
    async buttonClick() {
        const element = this.config.elements[0];
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        await fetch('/api/json/menu/configuration/' + element.config, {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "API-Token": token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
             formData = json;
        });

        // Show admin form
        this.form = await this.showForm('block/menu/form', formData, this.form);
    }
}
