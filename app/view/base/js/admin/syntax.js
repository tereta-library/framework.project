import {SyntaxConditions} from './syntax/conditions.js';
import {SyntaxVariables} from './syntax/variables.js';
import {SyntaxEvents} from './syntax/events.js';
import {SyntaxLoops} from './syntax/loops.js';
import {SyntaxTemplates} from './syntax/templates.js';
import {SyntaxJson} from './syntax/json.js';
import {SyntaxTranslate} from './syntax/translate.js';

let uniqueId = 0;

export default class Syntax
{
    syntaxTranslate  = null;
    syntaxConditions = null;
    syntaxVariables  = null;
    syntaxEvents     = null;
    syntaxLoops      = null;
    syntaxTemplates  = null;
    syntaxJson       = null;

    constructor(node, variables = {}) {
        uniqueId++;
        this.id = uniqueId;
        this.node = node;
        this.variables = variables;

        node.syntax = this;

        this.syntaxTranslate  = new SyntaxTranslate(this, node, this.variables);
        this.syntaxConditions = new SyntaxConditions(this, node, this.variables);
        this.syntaxVariables  = new SyntaxVariables(this, node, this.variables);
        this.syntaxEvents     = new SyntaxEvents(this, node, this.variables);
        this.syntaxLoops      = new SyntaxLoops(this, node, this.variables);
        this.syntaxTemplates  = new SyntaxTemplates(this, node, this.variables);
        this.syntaxJson       = new SyntaxJson();
    }

    get json() {
        return this.syntaxJson;
    }

    getSyntaxClass() {
        return Syntax;
    }

    set(key, value) {
        const keys = key.split('.');
        let current = this.variables;

        for (let i = 0; i < keys.length - 1; i++) {
            const k = keys[i];

            if (current[k] === undefined) {
                current[k] = isNaN(Number(keys[i + 1])) ? {} : [];
            }

            current = current[k];
        }

        current[keys[keys.length - 1]] = value;
        return this;
    }

    get(key) {
        const keys = key.split('.');
        let result = this.variables;

        for (let k of keys) {
            if (result == null) {
                return undefined;
            }
            result = result[k];
        }

        return result;
    }

    update(sections = []) {
        if (sections.length === 0) {
            sections = ['templates', 'loops', 'conditions', 'variables', 'events', 'observer', 'translate'];
        }

        if (this.syntaxTranslate && sections.includes('translate')) {
            this.syntaxTranslate.update();
        }

        if (this.syntaxTemplates && sections.includes('templates')) {
            this.syntaxTemplates.update();
        }

        if (this.syntaxLoops && sections.includes('loops')) {
            this.syntaxLoops.update();
        }

        if (this.syntaxConditions && sections.includes('conditions')) {
            this.syntaxConditions.update();
        }

        if (this.syntaxVariables && sections.includes('variables')) {
            this.syntaxVariables.update();
        }

        if (this.syntaxEvents && sections.includes('events')) {
            this.syntaxEvents.update();
        }

        return this;
    }
}
