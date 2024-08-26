import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';

export class AdminCreateForm extends AdminTemplateForm {
    template = 'block/page/create';
    formTypesContainer = null;
    nodeList = {};
    selectedData = null;

    show(config) {
        let types = config.types;
        types.unshift({
            'key': '',
            'title': ''
        });

        this.syntax.set('types', config.types);
        this.syntax.update();

        super.show();
    }

    init() {
        this.syntax = (new Syntax(this.node, {
            successMessage: '',
            showSuccessMessage: false,
            isSave: false,
            types: [],
            typeSelected: '',
            typeChange: this.typeChange.bind(this),
            onClose: this.onClose.bind(this),
            createPage: this.createPage.bind(this)
        }));

        this.syntax.update();

        this.formTypesContainer = this.node.querySelector('*[data-form-types=container]');
    }

    typeChange(event) {
        this.syntax.set('typeSelected', event.target.value);
        this.syntax.update();

        this.selectedData = null;
        this.syntax.get('types').forEach((type) => {
            if (type.key != event.target.value) {
                return;
            }

            this.selectedData = type;
        });

        Object.keys(this.nodeList).forEach((nodeItem) => {
            this.nodeList[nodeItem].getNode().classList.remove('show');
        });

        if (!this.selectedData || !this.selectedData.key) {
            this.selectedData = null;
            return;
        }

        if (typeof this.nodeList[this.selectedData.key] != 'undefined') {
            this.nodeList[this.selectedData.key].getNode().classList.add('show');
            this.selectedData.module = this.nodeList[this.selectedData.key];
            return;
        }

        this.rootAdminJs.getBlock(this.selectedData.form).then(async (itemForm) => {
            const nodeItem = await itemForm.render(this.formTypesContainer);
            nodeItem.setRootForm(this);
            nodeItem.getNode().classList.add('show');

            this.nodeList[this.selectedData.key] = nodeItem;
            this.selectedData.module = this.nodeList[this.selectedData.key];
        });
    }

    createPage(event, element, variable) {
        event.preventDefault();
debugger;
        const token = this.rootAdminJs.getToken();
        const xhr = new XMLHttpRequest();
        const url = (new URL(window.location.protocol + '//' + window.location.hostname + '/api/' + this.selectedData.controller));

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('Authorization', "Bearer " + token);

        xhr.onload = function(xhr) {
            if (this.status !== 200) {
                return;
            }

            const responseJson = JSON.parse(this.responseText);
            if (typeof responseJson.error != 'undefined') {
                alert(responseJson.error);
            }
            if (typeof responseJson.redirect != 'undefined') {
                window.location.href = responseJson.redirect;
            }
        };

        const formData = new FormData();
        formData.append('data', JSON.stringify(this.selectedData.module.getData()));
        xhr.send(formData);
    }
}
