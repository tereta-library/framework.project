export default class Template {
    template = ''; // public
    node = null; // private

    getNode() {
        return this.node;
    }

    async render(element) {
        if (!this.template) {
            throw new Error('Template is not defined');
        }

        const self = this;
        const path = this.template;
        const url = '/resource/base/html/panel/' + path + '.html';
        const documentUrl = new URL(url, import.meta.url).href

        let documentHtml = null;
        await fetch(documentUrl)
            .then(response => response.text())
            .then(html => {
                documentHtml = html;
            })
            .catch(error => {
                console.error('Error the html file loading:', error);
            });

        const insertElement = document.createElement('div');
        insertElement.innerHTML = documentHtml;

        let nodeRoot = insertElement;
        let nodeLength = 0;
        let nodeChild = null;

        insertElement.childNodes.forEach(item => {
            if (item.nodeName === '#text') return;
            if (item.nodeName === 'SCRIPT' && item.getAttribute('src') === '/@vite/client') return;
            nodeLength++;
            nodeChild = item;
        });

        if (nodeLength === 1) {
            nodeRoot = nodeChild;
        }

        element.appendChild(nodeRoot);
        nodeRoot.handler = self;
        self.node = nodeRoot;

        this.init();

        return this;
    }

    init() {}
}
