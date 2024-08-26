import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';

export class AdminListingForm extends AdminTemplateForm {
    template = 'block/page/listing';

    init() {
        this.syntax = (new Syntax(this.node, {
            listing: [],
            config: {
                page: 1,
                totalPages: 1
            },
            changePage: this.changePage.bind(this),
            successMessage: '',
            showSuccessMessage: false,
            isSave: false,
            onClose: this.onClose.bind(this),
        }));

        (async() => {
            const data = await this.getListing(1);
            const config = data.config;

            this.syntax.set('config', config);
            this.syntax.set('listing', data.listing);
            this.syntax.update();
        })();

        this.syntax.update();
    }

    async changePage(event) {
        const page = event.target.value;
        const data = await this.getListing(page);

        this.syntax.set('listing', data.listing);
        this.syntax.update();
    }

    async getListing(page = 1) {
        const formData = new FormData();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/json/page/getListing', false);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', this.rootAdminJs.getToken());
        formData.append('page', page);
        xhr.send(formData);

        let result = JSON.parse(xhr.response);
        result.listing.forEach((item) => {
            item.openPage = this.openPage.bind(this)
        });

        return result;
    }

    openPage(event, element) {
        event.preventDefault();
        console.info(element.getAttribute('href'));
    }
}
