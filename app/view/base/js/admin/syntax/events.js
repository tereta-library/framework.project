import {SyntaxAbstract} from './abstract.js';

export class SyntaxEvents extends SyntaxAbstract{

    update() {
        this.handleAttributes('data-event', this.node, (element, attributeName) => {
            this.addEventListener(element, attributeName);
        });
    }

    addEventListener(element, attributeName) {
        const value = element.getAttribute(attributeName);
        const split = value.split(':');

        let functionEvent = null;
        let functionName = split[0];
        let functionProperties = [];

        if (split.length === 1) {
            functionName = split[0];
        }

        if (split.length === 2) {
            functionEvent = split[0];
            functionName = split[1];
        }

        const variableMask = /^(.*)\((.*)\)$/;
        if (variableMask.test(functionName)) {
            const funcProperties = functionName.match(variableMask);
            functionName = funcProperties[1];
            if (funcProperties[2]) {
                functionProperties = eval('[' + funcProperties[2] + ']');
            }
        }

        if (functionProperties.length === 1) {
            functionProperties = functionProperties[0];
        }

        // Check if "on" exists infunction name: onClick = click event with onClick function
        if (functionEvent === null) {
            functionEvent = this.transformFunctionName(functionName);
       }

        // Assign click event if not defined
        if (functionEvent === null) {
            functionEvent = 'click';
        }

        if (typeof element.bindedEvents === 'undefined') {
            element.bindedEvents = [];
        }

        if (typeof element.bindedEvents[functionEvent] !== 'undefined') {
            return;
        }

        element.bindedEvents[functionEvent] = true;

        element.addEventListener(functionEvent, this.bindProperties(element, functionName, functionProperties));
    }

    transformFunctionName(functionName) {
        if (functionName.substring(0, 2) === 'on') {
            return functionName.substring(2).toLowerCase();
        }

        return null;
    }

    bindProperties(element, functionName, functionProperties) {
        if (!functionName) {
            return;
        }

        const keys = functionName.split('.');
        let functionLink = this.variables;
        let functionLinkThis = null;
        let functionParent = null;

        for (let i = 0; i < keys.length; i++) {
            const k = keys[i];

            if (functionLink[k] === undefined) {
                throw new Error('Event handler is not defined: ' + functionName);
            }

            functionLink = functionLink[k];
            functionLinkThis = functionParent;
            functionParent = functionLink;
        }

        if (typeof functionLink != 'function') {
            throw new Error('Event handler is not a function: ' + functionName);
        }

        if (functionLinkThis !== null) {
            functionLink = functionLink.bind(functionLinkThis);
        }

        return (event) => {
            return functionLink(event, element, functionProperties)
        };
    }
}
