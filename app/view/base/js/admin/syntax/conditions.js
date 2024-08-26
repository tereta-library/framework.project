import {SyntaxAbstract} from './abstract.js';

export class SyntaxConditions extends SyntaxAbstract
{
    update() {
        this.handleAttributes('data-if', this.node, (element, attributeName) => {
            this.handleAttribute(element, attributeName);
        });
    }

    handleConditionVariable(condition) {
        return this.getVariableValue(condition);
    }

    getVariableValue(variable) {
        if (variable.substring(0,1) === '"' && variable.substring(-1, 1) === '"') {
            return variable.substring(1, variable.length - 1);
        }

        if (variable.substring(0,1) === "'" && variable.substring(-1, 1) === "'") {
            return variable.substring(1, variable.length - 1);
        }

        if (typeof variable === "number") {
            return variable;
        }

        if (parseInt(variable) == variable) {
            return variable;
        }

        let object = this.variables;
        let index = 0;

        const split = variable.split('.');
        const length = split.length;

        while (index < length) {
            if (!object.hasOwnProperty(split[index])) {
                console.error(`The "${variable}" object was not found.`);
                return false;
            }

            object = object[split[index]];
            index++;
        }

        return object;
    }

    handleConditionPerform(condition, objectMask, operator) {
        const data = condition.match(objectMask);

        if (operator == 'equal' && this.getVariableValue(data[1]) == this.getVariableValue(data[2])) {
            return true;
        } else if (operator == 'greater' && this.getVariableValue(data[1]) > this.getVariableValue(data[2])) {
            return true;
        } else if (operator == 'less' && this.getVariableValue(data[1]) < this.getVariableValue(data[2])) {
            return true;
        } else {
            return false;
        }
    }

    handleCondition(condition) {
        condition = condition.trim();

        if (condition.substring(0,1) === '!') {
            condition = condition.substring(1);

            return !this.handleCondition(condition);
        }

        const variableMask = /^([a-zA-Z0-9_\.]+)$/g;
        if (variableMask.test(condition)) {
            return this.handleConditionVariable(condition);
        }

        const objectMaskEq = /^(['"a-zA-Z0-9_\.]+) += +(['"a-zA-Z0-9_\.]+)$/i;
        if (objectMaskEq.test(condition)) {
            return this.handleConditionPerform(condition, objectMaskEq, 'equal');
        }

        const objectMaskGt = /^(['"a-zA-Z0-9_\.]+) +> +(['"a-zA-Z0-9_\.]+)$/i;
        if (objectMaskGt.test(condition)) {
            return this.handleConditionPerform(condition, objectMaskGt, 'greater');
        }

        const objectMaskLt = /^(['"a-zA-Z0-9_\.]+) +< +(['"a-zA-Z0-9_\.]+)$/i;
        if (objectMaskLt.test(condition)) {
            return this.handleConditionPerform(condition, objectMaskLt, 'less');
        }

        console.error(`The "${condition}" condition is not supported.`);
    }

    handleAttribute(element, attributeName) {
        const condition = element.getAttribute(attributeName);
        const result = this.handleCondition(condition);
        if (result) {
            element.classList.remove('hide');
            element.classList.add('show');
        } else {
            element.classList.remove('show');
            element.classList.add('hide');
        }
    }
}
