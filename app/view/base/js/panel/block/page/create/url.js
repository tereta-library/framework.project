import Syntax from '../../../syntax.js';
import Template from '../../../template.js';

export class AdminCrateFormUrl extends Template {
    template = 'admin/structure/create/url';
    syntax = null;

    init() {
        this.syntax = (new Syntax(this.node, {
            uri: '',
            onChange: this.onChange.bind(this)
        }));

        this.syntax.update();
    }

    setRootForm(rootForm) {
        this.rootForm = rootForm;
        return this;
    }

    getData() {
        return {
            uri: this.syntax.get('uri')
        };
    }

    onChange(event) {
        this.syntax.set('uri', event.target.value);
        this.syntax.update();

        this.rootForm.syntax.set('isSave', true).update();
    }
}
