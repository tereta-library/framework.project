import AdminTemplateForm from '../templateForm.js';
import AdminSiteResellerForm from './form/reseller.js';
import AdminSiteThemeSelectorForm from './form/themeSelector.js';

export class AdminSiteForm extends AdminTemplateForm {
    template            = 'admin/site/form';
    elementLogoUploadZone = null;
    elementLogoUploadFile = null;
    elementIconUploadZone = null;
    elementIconUploadFile = null;
    themeSelector         = null;
    config                = null;
    formData                    = new FormData();

    init() {
        super.init();

        this.themeSelector = new AdminSiteThemeSelectorForm(this);
        this.themeSelector.render(this.node.querySelector('[data-window="themeWindow"]'));

        this.node.addEventListener('submit', this.save.bind(this));

        this.elementLogoUploadZone = this.node.querySelector('.uploaderLogo [data-container="uploadZone"]');
        this.elementLogoUploadFile = this.node.querySelector('.uploaderLogo [type="file"]');
        this.initUploadArea(this.elementLogoUploadZone, this.elementLogoUploadFile, 'logoImage');

        this.elementIconUploadZone = this.node.querySelector('.uploaderIcon [data-container="uploadZone"]');
        this.elementIconUploadFile = this.node.querySelector('.uploaderIcon [type="file"]');
        this.initUploadArea(this.elementIconUploadZone, this.elementIconUploadFile, 'iconImage');

        this.syntax.set('showSuccessMessage', false)
            .set('successMessage', '')
            .set('logoImage', '')
            .set('iconImage', '')
            .set('tagline', '')
            .set('address', '')
            .set('phone', '')
            .set('email', '')
            .set('name', '')
            .set('copyright', '')
            .set('themeSelector', this.themeSelector)
            .set('resellerClass', new AdminSiteResellerForm(this))
            .set('themeCss', null)
            .set('themeHeaderListing', [])
            .set('themeBodyListing', [])
            .set('themeFooterListing', [])
            .set('additional', []);
    }

    initUploadArea(elementFileUploadZone, elementFileUploadFile, variable) {
        elementFileUploadZone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            console.info('dragenter');
            elementFileUploadZone.classList.add('highlight');
        });

        elementFileUploadZone.addEventListener('dragover', (e) => e.preventDefault() );

        elementFileUploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            console.info('dragleave');
            elementFileUploadZone.classList.remove('highlight');
        });

        elementFileUploadZone.addEventListener('drop', (event) => this.dropFile(elementFileUploadZone, event, variable));
        elementFileUploadFile.addEventListener('change', (event) => this.uploadFile(event, variable));
    }

    uploadFile(event, variable) {
        const element = event.target;
        const files = element.files;

        for (const file of files) {
            this.applyFile(file, variable);
            break;
        }
    }

    dropFile(element, event, variable) {
        const files = event.dataTransfer.files;
        event.preventDefault();

        element.classList.remove('highlight');

        for(const file of files) {
            this.applyFile(file, variable);
            break;
        }
    }

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

    save(event) {
        this.formData = new FormData();

        const token = this.rootAdminJs.getToken();
        const syntax = this.syntax;
        const self = this;
        event.preventDefault();

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/admin/site', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('Authorization', "Bearer " + token);

        xhr.onload = function(xhr) {
            if (this.status === 200) {
                syntax.set('isSave', false);
                syntax.set('showSuccessMessage', true);
                syntax.set('successMessage', 'Site Configuration Saved');

                self.show(JSON.parse(this.responseText));

                syntax.update();

                setTimeout(() => {
                    syntax.set('showSuccessMessage', false);
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
        this.formData.append('theme[css]', this.syntax.get('themeCss').replace("\r\n", "\n"));
        this.formData.append('theme[header]', this.syntax.get('themeHeader'));
        this.formData.append('theme[body]', this.syntax.get('themeBody'));
        this.formData.append('theme[footer]', this.syntax.get('themeFooter'));

        this.getFormPaths(this.syntax.get('additional'), 'additional').forEach((item) => {
            this.formData.append(item[0], !item[1] ? '' : item[1]);
        });

        this.getFormPaths(syntax.get('resellerClass').getData(), 'additional').forEach((item) => {
            this.formData.append(item[0], item[1]);
        });

        xhr.send(this.formData);
    }

    getFormPaths(data, parentPath = '') {
        let result = [];

        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                const currentPath = parentPath ? `${parentPath}[${key}]` : key;

                // Проверяем, является ли текущее свойство объектом
                if (typeof data[key] === 'object' && data[key] !== null) {
                    result = result.concat(this.getFormPaths(data[key], currentPath));
                } else {
                    result.push([currentPath, data[key]]);
                }
            }
        }

        return result;
    }

    show(config) {
        this.config = config;

        this.syntax.get('resellerClass').setConfig(config.additional.reseller);

        this.syntax
            .set('logoImage', config.logoImage)
            .set('iconImage', config.iconImage)
            .set('address', config.address)
            .set('copyright', config.copyright)
            .set('email', config.email)
            .set('name', config.name)
            .set('phone', config.phone)
            .set('tagline', config.tagline)
            .set('themeCss', config.theme.css.replace("\r\n", "\n"))
            .set('themeHeader', config.theme.header)
            .set('themeBody', config.theme.body)
            .set('themeFooter', config.theme.footer)
            .set('themeHeaderListing', config.themeListing.header)
            .set('themeBodyListing', config.themeListing.body)
            .set('themeFooterListing', config.themeListing.footer)
            .set('additional', config.additional)
            .set('onClickTheme', async (event, element, parameter) => {
                this.themeSelector.open(element, parameter[1], this.syntax.get(parameter[0]));
            })
            .set('onChangeTheme', async (event, element, parameter) => {
                this.syntax.set('isSave', true);
                this.syntax.set(parameter[0], element.value);
                this.syntax.update();
            }).update();

        super.show();
    }
}
