import {SyntaxAbstract} from "./abstract.js";

export class SyntaxJson extends SyntaxAbstract{
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
