import AdminTemplateForm from '../templateForm.js';
import Slider from '../../../library/slider.js';
import {AdminSiteFormTheme} from './form/theme.js';
import {AdminSiteFormConfig} from './form/config.js';

export class AdminSiteForm extends AdminTemplateForm {
    template            = 'block/site/form';
    elementLogoUploadZone = null;
    elementLogoUploadFile = null;
    elementIconUploadZone = null;
    elementIconUploadFile = null;
    themeSectionSlider    = null;
    formData         = new FormData();
    extendedVariables      = {};

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

        const themeElement = this.node.querySelector('#themeSection');
        const themeItemTemplate = themeElement.innerHTML;
        themeElement.innerHTML = '';

        this.themeSectionSlider = new Slider(themeElement);
        this.themeSection = new AdminSiteFormTheme(this, this.themeSectionSlider, themeItemTemplate);
        this.configSection = new AdminSiteFormConfig(this, this.node.querySelector('#configSection'));

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
            .set('copyright', '')
            .set('themeSection', this.themeSection)
            .set('configSection', this.configSection);

        this.themeSection.render();
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
            if (this.status !== 200) {
                return;
            }

            const jsonResponse = JSON.parse(this.responseText);
            self.extendedVariables = {};

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

            syntax.update();

            self.rootAdminJs.elementCanvas.contentWindow.location.reload();

            setTimeout(() => {
                syntax.set('showSuccessMessage', false);
                syntax.set('showErrorMessage', false);
                syntax.update();
            }, 5000);
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

        this.syntax.get('themeSection').save(this.formData)
        this.syntax.get('configSection').save(this.formData)

        xhr.send(this.formData);
    }

    /**
     * Show the form
     *
     * @param initialItems
     */
    async show(initialItems) {
        const token = this.rootAdminJs.getToken();
        let siteData = {};
        let loadAdditionalConfigs = ['view.customCss'];

        if (!initialItems) {
            initialItems = [];
        }

        initialItems.forEach((item) => {
            if (item.config.type !== 'config') return;
            loadAdditionalConfigs.push(item.config.identifier);
        });

        await fetch('/api/json/site/configuration/get/' + loadAdditionalConfigs.join(':'), {
            method: "GET",
            headers: {
                "Cache-Control": "no-cache",
                "API-Token": token,
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then((response) => response.json()).then((json) => {
            siteData = json;
        });

        if (siteData.error && siteData.errorCode == 401) {
            this.rootAdminJs.elementPanel.actionLogout();
            return;
        }

        let additionalConfig = {};
        Object.keys(initialItems).forEach((key) => {
            const item = initialItems[key];
            const identifier = item.config.identifier;
            if (item.config.type !== 'config') return;
            additionalConfig[identifier] = siteData.additionalConfig[identifier];

            if (item.config['namespace']) {
                additionalConfig[identifier]['namespace'] = item.config.namespace;
            }
            if (item.config['label']) {
                additionalConfig[identifier]['label'] = item.config.label;
            }
        });

        this.syntax
            .set('logoImage', siteData.logoImage)
            .set('iconImage', siteData.iconImage)
            .set('address', siteData.address)
            .set('copyright', siteData.copyright)
            .set('email', siteData.email)
            .set('name', siteData.name)
            .set('phone', siteData.phone)
            .set('tagline', siteData.tagline)
            .set('themeSection', this.themeSection)
            .set('configSection', this.configSection)
            .update();

        this.themeSection.setSiteData(siteData);
        this.configSection.setSiteData(additionalConfig);
        this.syntax.update();

        super.show();
    }
}
