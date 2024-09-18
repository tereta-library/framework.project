import Syntax from '../../../syntax.js';

export class AdminSiteFormConfig {
    canvasNode = null;
    parent     = null;
    configStructure = [];

    constructor(parent, node) {
        this.parent = parent;
        this.canvasNode = node;
    }

    setSiteData(siteData) {
        let configStructure = {};

        siteData.configs.forEach((item) => {
            if (item.config.type !== 'config') return;
            this.appendConfigStructure(configStructure, item.config.identifier, item.config.label, item.config.namespace);
        });

        this.configStructure = this.encodeConfigStructure(configStructure);
    }

    encodeConfigStructure(configStructure) {
        let config = {};
        for (let key in configStructure) {
            if (typeof configStructure[key] === 'object') {
                config[key] = this.encodeConfigStructure(configStructure[key]);
            } else {
                config[key] = configStructure[key];
            }
        }
        return config;
    }

    appendConfigStructure(configStructure, identifier, label, namespace) {
        if (!configStructure[namespace]) {
            configStructure[namespace] = {};
        }

        configStructure[namespace][label] = {
            'key': identifier,
            'keyUp': this.onKeyup.bind(this, identifier),
            'onChange': this.onChange.bind(this, identifier),
            'getValue': this.getValue.bind(this, identifier)
        };
    }

    getValue(identifier) {
        if (!this.parent.syntax.get('additionalConfig')) {
            return '';
        }

        return this.parent.syntax.get('additionalConfig')[identifier] ?? '';
    }

    onChange(identifier, event) {
        this.parent.syntax.set('isSave', true).update();
    }

    onKeyup(identifier, event, element) {
        if (!this.parent.syntax.get('additionalConfig')) {
            this.parent.syntax.set('additionalConfig', {});
        }
        this.parent.syntax.get('additionalConfig')[identifier] = element.value;
    }
}