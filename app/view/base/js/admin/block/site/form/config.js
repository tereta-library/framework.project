import Syntax from '../../../syntax.js';

export class AdminSiteFormConfig {
    canvasNode = null;
    parent     = null;
    configStructure = [];

    constructor(parent, node) {
        this.parent = parent;
        this.canvasNode = node;
    }

    show(siteData) {
        let configStructure = {};

        siteData.configs.forEach((item) => {
            if (item.config.namespace !== 'config') return;
            this.appendConfigStructure(configStructure, item.config.identifier, item.config.label);
        });

        this.configStructure = this.encodeConfigStructure(configStructure);
    }

    encodeConfigStructure(configStructure) {
        let config = [];
        for (let key in configStructure) {
            if (typeof configStructure[key] === 'object') {
                config.push({key: key, value: this.encodeConfigStructure(configStructure[key])});
            } else {
                config.push({key: key, value: configStructure[key]});
            }
        }
        return config;
    }

    appendConfigStructure(configStructure, identifier, label) {
        const labelPointer = label.shift();
        if (!configStructure[labelPointer]) {
            configStructure[labelPointer] = {};
        }
        if (label.length == 1) {
            configStructure[labelPointer][label[0]] = identifier;
            return;
        }

        this.appendConfigStructure(configStructure[labelPointer], identifier, label);
    }
}