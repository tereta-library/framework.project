import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';

export class AdminSocialForm extends AdminTemplateForm {
    template = 'block/social/form';

    init() {
        this.syntax = (new Syntax(this.node, {
            successMessage    : '',
            showSuccessMessage: false,
            isSave            : false,
            facebook          : false,
            instagram         : false,
            pinterest         : false,
            linkedin          : false,
            youtube           : false,
            onClose           : this.onClose.bind(this),
            changeSocial      : this.changeSocial.bind(this),
            clearSocial       : this.clearSocial.bind(this),
            saveForm          : this.saveForm.bind(this)
        }));
        this.syntax.update();
    }

    saveForm(event, element, variable) {
        const syntax = this.syntax;
        event.preventDefault();

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/json/social/configuration', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());
        xhr.onload = (xhr) => {
            if (xhr.target.status === 200) {
                this.syntax.set('successMessage', 'Social links saved')
                    .set('isSave', false)
                    .set('showSuccessMessage', true).update();

                setTimeout(() => {
                    syntax.set('showSuccessMessage', false);
                    syntax.update();
                }, 5000);
            }
        };

        xhr.send(JSON.stringify({
            facebook : this.syntax.get('facebook'),
            instagram: this.syntax.get('instagram'),
            pinterest: this.syntax.get('pinterest'),
            linkedin : this.syntax.get('linkedin'),
            youtube  : this.syntax.get('youtube')
        }));
    }

    clearSocial(event, element, variable) {
        this.syntax.set('isSave', true).set(variable, false).update();
    }

    changeSocial(event, element, variable) {
        let value = element.value;
        if (!value) {
            value = false;
        }

        this.syntax.set('isSave', true).set(variable, value).update();
    }

    show(config) {
        this.syntax.set('facebook' , config.facebook);
        this.syntax.set('instagram', config.instagram);
        this.syntax.set('pinterest', config.pinterest);
        this.syntax.set('linkedin' , config.linkedin);
        this.syntax.set('youtube'  , config.youtube);
        this.syntax.update();

        super.show();
    }
}
