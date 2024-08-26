import AdminTemplate from './template.js';
import Syntax from '../syntax.js';

export default class AdminTemplateForm extends AdminTemplate {
    actionChangeTimeout = [];

    init() {
        this.syntax = (new Syntax(this.node, {
            'isSave': false,
            'onClose': this.onClose.bind(this),
            'onChange': this.onChange.bind(this),
            'onKeyup': this.onKeyup.bind(this)
        }));
    }

    async buttonClick() {
        const itemForm = await this.rootAdminJs.getBlock(this.template);
        itemForm.render(this.rootAdminJs.elementPanel.containerPages);
    }

    show() {
        this.node.classList.add('show');
    }

    hide() {
        this.node.classList.remove('show');
    }

    onClose() {
        this.rootAdminJs.elementPanel.hideForms();
    }

    onKeyup(event, element, parameter) {
        this.onChange(event, element, parameter);
    }

    onChange(event, element, parameter) {
        if (element.type === 'checkbox') {
            this.syntax.set(parameter, element.checked);
            this.syntax.set('isSave', true).update();
        } else if (this.syntax.get(parameter) != element.value) {
            this.syntax.set(parameter, element.value);
            this.syntax.set('isSave', true).update();
        }
    }
}
