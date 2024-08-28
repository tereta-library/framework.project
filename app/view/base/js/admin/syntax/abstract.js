export class SyntaxAbstract
{
    constructor(syntax, node, variables) {
        this.syntax = syntax;
        this.node = node;
        this.variables = variables;
    }

    get(attributeName) {
        const attributePointer = attributeName.split('.');
        return this.getVariable(attributePointer, this.variables);
    }

    getVariable(attributePointer, variable) {
        const currentPointer = attributePointer.shift();
        const keys = Object.keys(variable);

        if (keys.includes(currentPointer) === false) {
            console.error(`Variable '${currentPointer}' is not defined at the syntax class id #${this.syntax.id}.`)
            return null;
        }

        if (attributePointer.length > 0) {
            return this.getVariable(attributePointer, variable[currentPointer]);
        }

        return variable[currentPointer];
    }

    handleAttributes(attributeName, element, callback, useMask = true) {
        const elements = this.getAllElements(attributeName, useMask);

        elements.forEach((element) => {
            this.getAttributes(attributeName, element, useMask).forEach((attributeName) => {
                callback(element, attributeName);
            });
        });
    }

    getAttributes(attributeName, element, useMask = true) {
        const attributeMask = RegExp(`^${attributeName}-(.*)$`, 'i');
        let result = [];

        Object.keys(element.attributes).forEach((key) => {
            const itemAttributeName = element.attributes[key].name;

            if (useMask && attributeMask.test(itemAttributeName)) {
                result.push(itemAttributeName);
            }

            if (itemAttributeName === attributeName) {
                result.push(itemAttributeName);
            }
        });

        return result;
    }

    checkSyntax(element) {
        if (element.syntax === undefined && element.parentNode !== null) {
            return this.checkSyntax(element.parentNode);
        } else if (element.syntax === undefined) {
            return false;
        }

        return (element.syntax.id === this.syntax.id);
    }

    getAllElements(attributeName, useMask = true) {
        let result = [];

        //const allElements = this.node.getElementsByTagName('*');
        const allElements = this.node.querySelectorAll(`*[${attributeName}]`);
        const attributeMask = RegExp(`${attributeName}-(.*)`, 'g');

        this.performElement(result, this.node, attributeName, useMask ? attributeMask : null);

        Object.keys(allElements).forEach((key) => {
            const element = allElements[key];
            this.performElement(result, element, attributeName, attributeMask);
        });

        return result;
    }

    performElement(result, element, attributeName, attributeMask = null) {

        if (this.checkSyntax(element) === false) {
            return;
        }

        Object.keys(element.attributes).forEach((attributeKey) => {
            const attribute = element.attributes[attributeKey];
            if (attributeMask && attributeMask.test(attribute.name)) {
                result.push(element);
            }

            if (attribute.name === attributeName) {
                result.push(element);
            }
        });
    }
}
