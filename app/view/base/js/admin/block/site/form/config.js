import Syntax from '../../../syntax.js';

class AdminSiteFormConfigNamespace {
    name = null;
    fields = [];
    isActive = false;
    setActive = function() {};

    constructor(name, fields, setActiveAction) {
        this.name = name;
        this.fields = fields;
        this.setActive = setActiveAction;
    }
}

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
            this.configValues[key] = this.appendConfigStructure(configStructure, key, item.label, item.namespace, item.value);
        });

        let configStructureSource = [];
        [configStructureSource, this.configValues] = this.encodeConfigStructure(configStructure);

        this.configStructure = [];
        Object.keys(configStructureSource).forEach((key) => {
            this.configStructure.push(
                new AdminSiteFormConfigNamespace(key, configStructureSource[key], this.setNamespaceActive.bind(this, key))
            );
        });

        // Set the first namespace active
        let foundActive = false;
        this.configStructure.forEach((namespace) => {
            foundActive = namespace.isActive || foundActive;
        });

        if (!foundActive && this.configStructure.length > 0) {
            this.configStructure[0].isActive = true;
        }
    }

    setNamespaceActive(key) {
        this.configStructure.forEach((namespace) => {
            namespace.isActive = namespace.name === key;
        });
        this.parent.syntax.update();
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