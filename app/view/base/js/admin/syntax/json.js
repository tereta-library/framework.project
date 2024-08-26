import {SyntaxAbstract} from "./abstract.js";

/**
 * Additional functionality for JSON syntax
 */
export class SyntaxJson extends SyntaxAbstract{
    /**
     * Remove recursive objects from the given object
     *
     * @param obj
     * @param seenObjects
     * @returns {*|*[]|string}
     */
    clean(obj, seenObjects = new Set()) {
        if (obj && typeof obj === 'object') {
            if (seenObjects.has(obj)) {
                return '[Recursive object]';
            }

            seenObjects.add(obj);

            if (Array.isArray(obj)) {
                const newArr = [];
                obj.forEach(item => {
                    newArr.push(this.clean(item, seenObjects));
                });
                seenObjects.delete(obj);
                return newArr;
            } else {
                const newObj = {};
                for (let key in obj) {
                    newObj[key] = this.clean(obj[key], seenObjects);
                }
                seenObjects.delete(obj);
                return JSON.parse(JSON.stringify(newObj));
            }
        }

        return obj;
    }
}
