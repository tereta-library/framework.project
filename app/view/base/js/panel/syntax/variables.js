import {SyntaxAbstract} from './abstract.js';

export class SyntaxVariables extends SyntaxAbstract {
    update() {
        this.handleAttributes('data-variable', this.node, (element, attributeName) => {
            this.handleAttribute(element, attributeName);
        });
    }

    handleAttribute(element, attributeName) {
        let value = element.getAttribute(attributeName);
        const split = value.split(':');

        if (split.length === 1) {
            element.innerText = this.parseVariableType(value);
            return;
        }

        if (split.length === 2 && split[0] === 'text') {
            element.innerText = this.parseVariableType(split[1]);
            return;
        }

        if (split.length === 2 && split[0] === 'html') {
            element.innerHTML = this.parseVariableType(split[1]);
            return;
        }

        if (split.length === 2 && split[0] === 'value' && element.tagName === 'INPUT' && element.type === 'checkbox') {
            const variableValue = this.parseVariableType(split[1]);
            element.checked = variableValue == 'false' ? false : variableValue;
            return;
        } else if (split.length === 2 && split[0] === 'value') {
            const variableValue = this.parseVariableType(split[1])
            element.setAttribute('value', variableValue);
            element.value = variableValue;
            return;
        }

        if (split.length === 3 && (split[0] === 'attribute' || split[0] === 'attr')) {
            if (this.parseVariableType(split[2]) === false) {
                element[split[1]] = '';
                element.removeAttribute(split[1]);
                return;
            }
            element[split[1]] = this.parseVariableType(split[2]);
            element.setAttribute(split[1], this.parseVariableType(split[2]));
            return;
        }

        if (split.length === 2) {
            if (this.parseVariableType(split[1]) === false) {
                element[split[0]] = '';
                element.removeAttribute(split[0]);
                return;
            }
            element[split[0]] = this.parseVariableType(split[1]);
            element.setAttribute(split[0], this.parseVariableType(split[1]));
            return;
        }

        if (split.length === 3 && (split[0] === 'class')) {
            if (this.parseVariable(split[2])) {
                element.classList.add(split[1]);
            } else {
                element.classList.remove(split[1]);
            }
        }
    }

    parseVariableType(variable) {
        if (variable.substring(0, 9) === '(boolean)') {
            return !!this.parseVariable(variable.substring(9));
        }
        if (variable.substring(0, 1) === '!') {
            return !!this.parseVariable(variable);
        }

        let result = this.parseVariable(variable);
        if (!result) {
            return '';
        }

        return result;
    }

    parseVariable(variableOriginal) {
        let properties = null;
        let variable = variableOriginal;

        if (variable.substring(0, 1) === '!') {
            return !this.parseVariable(variable.substring(1));
        }

        const functionExpression = /(.*)\((.*)\)/;

        if (functionExpression.test(variable)) {
            const matched = variable.match(functionExpression);
            variable = matched[1];
            properties = matched[2] ? eval('{' + matched[2] + '}') : null;
        }

        const result = this.get(variable, this.variables);
        return typeof result === 'function' ? result(properties) : result;
    }
}
