import {SyntaxAbstract} from './abstract.js';

export class SyntaxLoops extends SyntaxAbstract{
    update() {
        let elements = [];

        this.getAllElements('data-foreach', true).forEach((element) => {
            if (this.checkParent(element, elements)) return;

            elements.push(element);
        });

        elements.forEach((element) => {
            if (element.foreachTemplate === undefined) {
                element.foreachTemplate = element.innerHTML.trim();
            }

            this.processForeach(element, element.foreachTemplate);
        });
    }

    checkParent(child, elements) {
        let result = false;

        elements.forEach((element) => {
            if (element == child) {
                return;
            }
            if (element.contains(child)) {
                result = true;
            }
        });

        return result;
    }

    processForeach(element, template) {
        const variable = element.getAttribute('data-foreach');
        const value = this.get(variable);
        element.innerHTML = '';

        if (!value) {
            return;
        }

        const keys = (typeof value == 'object' ? Object.keys(value) : value.keys());

        keys.forEach((key) => {
            const item = value[key];
            let insertElement = document.createElement(element.tagName);
            const syntaxClass = this.syntax.getSyntaxClass();
            insertElement.innerHTML = template.trim();

            insertElement.childNodes.forEach((childNode) => {
                if (childNode.nodeType == 3) {
                    element.append(childNode);
                    return;
                }

                const syntax = new syntaxClass(childNode, {
                    'key': key,
                    'item': item
                });
                syntax.update();

                element.append(childNode);
            });
        });
    }
}
