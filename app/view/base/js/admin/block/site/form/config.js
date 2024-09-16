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
            if (item.config.type !== 'config') return;
            this.appendConfigStructure(configStructure, item.config.identifier, item.config.label, item.config.namespace);
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

    appendConfigStructure(configStructure, identifier, label, namespace) {
        if (!configStructure[namespace]) {
            configStructure[namespace] = {};
        }

        configStructure[namespace][label] = identifier;
    }
}