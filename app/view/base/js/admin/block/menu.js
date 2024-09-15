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
        let subMenus = this.config.elements;
        subMenus.forEach((subMenu, subMenuNumber) => {
            subMenu['clickMenuItem'] = this.subMenuClick.bind(this, subMenuNumber);
        });

        this.syntax = (new Syntax(
            this.node, {
                'listing': subMenus
            }
        ));

        this.syntax.update();
    }

    /**
     * Button click event
     * @returns {Promise<void>}
     */
    async subMenuClick(menuNumber) {
        const element = this.config.elements[menuNumber];
        const token = this.rootAdminJs.getToken();

        // Load form data
        let formData = null;

        await fetch(`/api/json/menu/configuration/${element.config}`, {
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

        // Show admin form
        this.form = await this.showForm('block/menu/form', formData, this.form);
    }
}
