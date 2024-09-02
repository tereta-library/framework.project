import AdminTemplateForm from '../templateForm.js';

export class AdminSiteForm extends AdminTemplateForm {
    template            = 'block/site/form';
    elementLogoUploadZone = null;
    elementLogoUploadFile = null;
    elementIconUploadZone = null;
    elementIconUploadFile = null;
    config                = null;
    formData         = new FormData();

    /**
     *
     */
    init() {
        super.init();

        this.node.addEventListener('submit', this.save.bind(this));

        this.elementLogoUploadZone = this.node.querySelector('.uploaderLogo [data-container="uploadZone"]');
        this.elementLogoUploadFile = this.node.querySelector('.uploaderLogo [type="file"]');
        this.initUploadArea(this.elementLogoUploadZone, this.elementLogoUploadFile, 'logoImage');

        this.elementIconUploadZone = this.node.querySelector('.uploaderIcon [data-container="uploadZone"]');
        this.elementIconUploadFile = this.node.querySelector('.uploaderIcon [type="file"]');
        this.initUploadArea(this.elementIconUploadZone, this.elementIconUploadFile, 'iconImage');

        this.syntax
            .set('showSuccessMessage', false)
            .set('successMessage', '')
            .set('showErrorMessage', false)
            .set('errorMessage', '')
            .set('logoImage', '')
            .set('iconImage', '')
            .set('tagline', '')
            .set('address', '')
            .set('phone', '')
            .set('email', '')
            .set('name', '')
            .set('copyright', '');
    }

    /**
     * Initialize the upload area
     *
     * @param elementFileUploadZone
     * @param elementFileUploadFile
     * @param variable
     */
    initUploadArea(elementFileUploadZone, elementFileUploadFile, variable) {
        elementFileUploadZone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            elementFileUploadZone.classList.add('highlight');
        });

        elementFileUploadZone.addEventListener('dragover', (e) => e.preventDefault() );

        elementFileUploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            elementFileUploadZone.classList.remove('highlight');
        });

        elementFileUploadZone.addEventListener('drop', (event) => this.dropFile(elementFileUploadZone, event, variable));
        elementFileUploadFile.addEventListener('change', (event) => this.uploadFile(event, variable));
    }

    /**
     * Upload file event
     *
     * @param event
     * @param variable
     */
    uploadFile(event, variable) {
        const element = event.target;
        const files = element.files;

        for (const file of files) {
            this.applyFile(file, variable);
            break;
        }

        this.syntax.set('isSave', true);
        this.syntax.update();
    }

    /**
     * Drop file event
     *
     * @param element
     * @param event
     * @param variable
     */
    dropFile(element, event, variable) {
        const files = event.dataTransfer.files;
        event.preventDefault();

        element.classList.remove('highlight');

        for(const file of files) {
            this.applyFile(file, variable);
            break;
        }

        this.syntax.set('isSave', true);
        this.syntax.update();
    }

    /**
     * Apply the file to the form
     * @param file
     * @param variable
     */
    applyFile(file, variable) {
        this.formData.append(variable, file);
        this.syntax.set('isSave', true);

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = () => {
                this.syntax.set(variable, reader.result);
                this.syntax.update();
            };

            this.syntax.set(variable + 'File', file);
            reader.readAsDataURL(file);
        }
    }

    /**
     * Save the form
     * @param event
     */
    save(event) {
        this.formData = new FormData();

        const token = this.rootAdminJs.getToken();
        const syntax = this.syntax;
        const self = this;
        event.preventDefault();

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/json/site/configuration', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', token);

        xhr.onload = function(xhr) {
            if (this.status === 200) {
                const jsonResponse = JSON.parse(this.responseText);

                if (jsonResponse.error) {
                    syntax.set('isSave', false);
                    syntax.set('showSuccessMessage', false);
                    syntax.set('showErrorMessage', true);
                    syntax.set('errorMessage', 'Error: ' + jsonResponse.error);
                } else {
                    syntax.set('isSave', false);
                    syntax.set('showSuccessMessage', true);
                    syntax.set('successMessage', 'Site Configuration Saved');
                }

                self.show(jsonResponse);
                syntax.update();

                setTimeout(() => {
                    syntax.set('showSuccessMessage', false);
                    syntax.set('showErrorMessage', false);
                    syntax.update();
                }, 5000);
            }
        };

        this.formData.append('copyright', this.syntax.get('copyright'));
        this.formData.append('tagline', this.syntax.get('tagline'));
        this.formData.append('address', this.syntax.get('address'));
        this.formData.append('phone', this.syntax.get('phone'));
        this.formData.append('email', this.syntax.get('email'));
        this.formData.append('name', this.syntax.get('name'));
        if (this.syntax.get('iconImageFile')) {
            this.formData.append('iconImage', this.syntax.get('iconImageFile'));
        }

        if (this.syntax.get('logoImageFile')) {
            this.formData.append('logoImage', this.syntax.get('logoImageFile'));
        }

        xhr.send(this.formData);
    }

    /**
     * Show the form
     *
     * @param config
     */
    show(config) {
        this.config = config;

        this.syntax
            .set('logoImage', config.logoImage)
            .set('iconImage', config.iconImage)
            .set('address', config.address)
            .set('copyright', config.copyright)
            .set('email', config.email)
            .set('name', config.name)
            .set('phone', config.phone)
            .set('tagline', config.tagline)
            .set('onChangeTheme', async (event, element, parameter) => {
                this.syntax.set('isSave', true);
                this.syntax.set(parameter[0], element.value);
                this.syntax.update();
            }).update();

        super.show();
    }
}
