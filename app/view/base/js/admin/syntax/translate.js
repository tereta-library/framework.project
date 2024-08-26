import {SyntaxAbstract} from './abstract.js';

const syntaxTranslateSingleton = new class {
    translateMap = {}

    elements = [];

    constructor() {
        this.instance = [];
    }

    setTranslateMap(map) {
        this.translateMap = map;
    }

    addElement(element) {
        this.elements.push(element);
    }

    translateAttribute(element, attributeName) {
        let value = element.getAttribute(attributeName);

        if (typeof this.translateMap[value] === 'undefined') {
            element.innerHTML = value;
            return;
        }

        element.innerHTML = this.translateMap[value];
    }

    update() {
        this.elements.forEach((element) => {
            this.translateAttribute(element, 'data-translate');
        });
    }
}

export class SyntaxTranslate extends SyntaxAbstract {
    update() {
        this.handleAttributes('data-translate', this.node, (element, attributeName) => {
            syntaxTranslateSingleton.addElement(element);
            syntaxTranslateSingleton.translateAttribute(element, attributeName);
        });
    }

    setTranslateMap(map) {
        syntaxTranslateSingleton.setTranslateMap(map);
    }
}

export const singleton = syntaxTranslateSingleton;
