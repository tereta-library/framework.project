export default class Block
{
    constructor(root = null) {
        this.root = root;
    }

    async getBlock(path) {
        const url = './' + path + '.js';

        let result = null;

        await import(url).then(module => {
            const moduleName = Object.keys(module)[0];
            result = new (module[moduleName])(this.root);
        }).catch(error => {
            throw new Error('Error loading module (' + url + '): ' + error);
        })

        return result;
    }
}
