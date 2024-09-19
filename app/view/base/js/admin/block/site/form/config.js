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

        Object.keys(siteData.configs).forEach((key) => {
            const item = siteData.configs[key];
            this.appendConfigStructure(configStructure, item.identifier, item.label, item.namespace, item.value);
        });

        this.configStructure = this.encodeConfigStructure(configStructure);
        debugger;
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

    appendConfigStructure(configStructure, identifier, label, namespace, value) {
        if (!configStructure[namespace]) {
            configStructure[namespace] = {};
        }

        configStructure[namespace][label] = {
            'key': identifier,
            'keyUp': this.onKeyup.bind(this, identifier),
            'onChange': this.onChange.bind(this, identifier),
            'value': value
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