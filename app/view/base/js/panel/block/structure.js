import AdminTemplate from './template.js';
import Syntax from "../syntax.js";

export class AdminCreate extends AdminTemplate {
    template = 'block/structure';

    init() {
        this.syntax = (new Syntax(this.node));
        this.syntax.update();

        this.node.querySelector('*[data-button=create]').addEventListener('click', this.buttonCreateAction.bind(this));
        this.node.querySelector('*[data-button=listing]').addEventListener('click', this.buttonListingAction.bind(this));
        this.node.querySelector('*[data-button=files]').addEventListener('click', this.buttonFilesAction.bind(this));
    }

    async buttonFilesAction() {
        this.formFilesAction = await this.showForm('admin/structure/files', [], this.formFilesAction);

        this.node.querySelector('#admin_structure_checkbox').checked = false;
    }

    async buttonListingAction() {
        this.formListingAction = await this.showForm('admin/structure/listing', [], this.formListingAction);

        this.node.querySelector('#admin_structure_checkbox').checked = false;
    }

    async buttonCreateAction() {
        const token = this.rootAdminJs.getToken();

        // Load form data
        let receivedData = null;

        const url = (new URL(window.location.protocol + '//' + window.location.hostname + '/api/admin/structure'));

        await fetch(url, {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "Authorization": "Bearer " + token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            receivedData = json;
        });

        // Show admin form
        this.formCreateAction = await this.showForm('admin/structure/create', receivedData, this.formCreateAction);
        this.node.querySelector('#admin_structure_checkbox').checked = false;
    }
}
