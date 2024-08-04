import {SyntaxAbstract} from './abstract.js';

const syntaxTranslateSingletone = new class {
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
            syntaxTranslateSingletone.addElement(element);
            syntaxTranslateSingletone.translateAttribute(element, attributeName);
        });
    }

    setTranslateMap(map) {
        syntaxTranslateSingletone.setTranslateMap(map);
    }
}

export const singletone = syntaxTranslateSingletone;
