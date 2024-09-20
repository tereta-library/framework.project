import Syntax from '../../../syntax.js';

class AdminSiteFormConfigItem {
    key = '';
    keyUp = null;
    onChange = null;
    value = null;

    constructor(config) {
        this.key = config.key;
        this.keyUp = config.keyUp;
        this.onChange = config.onChange;
        this.value = config.value;
    }
}

export class AdminSiteFormConfig {
    canvasNode = null;
    parent     = null;
    configStructure = [];
    configValues = {};

    constructor(parent, node) {
        this.parent = parent;
        this.canvasNode = node;
    }

    setSiteData(configs) {
        let configStructure = {};

        Object.keys(configs).forEach((key) => {
            const item = configs[key];
            this.configValues[key] = this.appendConfigStructure(configStructure, item.identifier, item.label, item.namespace, item.value);
        });

        [this.configStructure, this.configValues] = this.encodeConfigStructure(configStructure);
    }

    encodeConfigStructure(configStructure, values = {}) {
        let config = {};
        for (let key in configStructure) {
            if (configStructure[key] instanceof AdminSiteFormConfigItem) {
                config[key] = configStructure[key];
                values[config[key].key] = config[key];
            } else {
                [config[key], values] = this.encodeConfigStructure(configStructure[key], values);
            }
        }
        return [config, values];
    }

    appendConfigStructure(configStructure, identifier, label, namespace, value) {
        if (!configStructure[namespace]) {
            configStructure[namespace] = {};
        }

        return configStructure[namespace][label] = new AdminSiteFormConfigItem({
            'key': identifier,
            'keyUp': this.onKeyup.bind(this, identifier),
            'onChange': this.onChange.bind(this, identifier),
            'value': value
        });
    }

    save(formData) {
        Object.keys(this.configValues).forEach((key) => {
            const value = this.configValues[key];
            formData.append(`additionalConfig[${value.key}]`, value.value);
        });
    }

    onChange(identifier, event) {
        this.configValues[identifier].value = event.target.value;
        this.parent.syntax.set('isSave', true).update();
    }

    onKeyup(identifier, event, element) {
        this.configValues[identifier].value = event.target.value;
    }
}