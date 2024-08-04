import AdminTemplate from './template.js';
import Syntax from '../syntax.js';

export class AdminLogin extends AdminTemplate{
    template = 'block/login';
    syntax = null;
    logoutAction = null;

    show() {
        this.node.classList.add('show');
    }

    hide() {
        this.node.classList.remove('show');
        if (this.logoutAction) {
            this.logoutAction();
        }
    }

    fieldChange(ev) {
        const target = ev.target;

        if (target.value === '') {
            target.classList.remove('filled');
        } else {
            target.classList.add('filled');
        }
    }

    submit(ev, form) {
        ev.preventDefault();

        const emailInput = form.querySelector('input[name=email]');
        const passwordInput = form.querySelector('input[name=password]');

        const formData = {
            'email': emailInput.value,
            'password': passwordInput.value
        };

        fetch('/api/admin/token', {
            method: "POST",
            body: JSON.stringify(formData),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            if (!json.token) {
                this.syntax.set('errorMessage', json.message);
                this.syntax.set('isError', true);
                this.syntax.update();
                return;
            }

            this.syntax.set('isError', false);
            this.syntax.update();

            localStorage.setItem('adminToken', json.token);
            this.hide();
            this.rootAdminJs.show();
            console.log(json)
        });
    }

    init() {
        const self = this;
        this.node.querySelectorAll('[data-action="close"]').forEach(item => {
            item.addEventListener('click', this.hide.bind(self));
        });

        this.syntax = (new Syntax(this.node, {
            isError: false,
            errorMessage: null,
            fieldChange: this.fieldChange.bind(self),
            submit: this.submit.bind(self),
        }));
        this.syntax.update();
    }

    setLogoutAction(logoutAction) {
        this.logoutAction = logoutAction;
    }
}
