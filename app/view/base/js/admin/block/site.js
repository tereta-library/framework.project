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
        // Show admin form
        this.form = await this.showForm('block/site/form', this.config.elements, this.form);
    }
}
