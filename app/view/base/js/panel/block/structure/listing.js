import AdminTemplateForm from '/resources/js/admin/templateForm.js';
import Syntax from '/resources/js/syntax.js';

export class AdminListingForm extends AdminTemplateForm {
    template = 'admin/structure/listing';

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

            const totalPages = data.totalPages;
            const config = data.config;

            this.syntax.set('config', config);
            this.syntax.set('listing', data.listing);
            this.syntax.update();

            console.info(data);
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
        xhr.open('POST', '/api/admin/structure/urlListing', false);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('Authorization', "Bearer " + this.rootAdminJs.getToken());
        formData.append('page', page);
        xhr.send(formData);

        return JSON.parse(xhr.response);
    }
}
