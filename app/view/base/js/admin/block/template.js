import Template from '../template.js';

export default class AdminTemplate extends Template {
    renderSequenceList = [];

    constructor(rootNode, config) {
        super();

        this.rootAdminJs = rootNode;
        this.config = config;
    }

    async showForm(path, config, form) {
        let itemForm = form;
        if (!itemForm) {
            itemForm = await this.rootAdminJs.getBlock(path);
            await itemForm.render(this.rootAdminJs.elementPanel.containerPages, config);
        }
        this.rootAdminJs.elementPanel.hideForms();
        this.rootAdminJs.elementPanel.hideAllElements();
        itemForm.show(config);
        this.rootAdminJs.elementPanel.showForms();
        return itemForm;
    }
    async render(element) {
        await super.render(element);
        this.renderSequence(element);
    }

    renderSequence(element) {
        this.renderSequenceList.forEach((item) => {
            item.render(element);
        });
    }
}
