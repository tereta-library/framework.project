import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

export class AdminPage extends AdminTemplate {
    template = 'block/page';

    init() {
        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.querySelector('*[data-button=listing]').addEventListener('click', this.buttonListingAction.bind(this));
        this.node.querySelector('*[data-button=files]').addEventListener('click', this.buttonFilesAction.bind(this));
    }

    async buttonFilesAction() {
        this.formFilesAction = await this.showForm('block/page/files', [], this.formFilesAction);
        this.node.querySelector('#adminPageCheckbox').checked = false;
        
        if (!localStorage.getItem('adminToken')) {
            this.rootAdminJs.elementPanel.actionLogout();
        }
    }

    async buttonListingAction() {
        this.formListingAction = await this.showForm('block/page/listing', [], this.formListingAction);
        this.node.querySelector('#adminPageCheckbox').checked = false;

        if (!localStorage.getItem('adminToken')) {
            this.rootAdminJs.elementPanel.actionLogout();
        }
    }
}
