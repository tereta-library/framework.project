import {SyntaxAbstract} from './abstract.js';

let staticTemplates = {};

export class SyntaxTemplates extends SyntaxAbstract {
    templates = {};

    constructor(syntax, node, variables) {
        super(syntax, node, variables);

        this.handleAttributes('data-template', this.node, (element, attributeName) => {
            this.fetchTemplate(element, attributeName);
        });
    }

    fetchTemplate(element, attributeName) {
        const templateName = element.getAttribute(attributeName);
        this.templates[templateName] = element.innerHTML;
        staticTemplates[templateName] = element.innerHTML;
        element.remove(element);
    }

    update() {
        this.handleAttributes('data-use', this.node, (element, attributeName) => {
            this.handleAttribute(element, attributeName);
        });
    }

    handleAttribute(element, attributeName) {
        const templateName = element.getAttribute(attributeName);
        let template = this.templates[templateName];
        if (!template) {
            template = staticTemplates[templateName];
        }

        if (!template) {
            return;
        }

        element.innerHTML = template;
    }
}
