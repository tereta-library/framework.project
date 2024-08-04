export default class Reseller
{
    constructor(parent) {
        this.parent = parent;
        this.config = {};
        this.domainValue = '';
        this.addDomainAction = this.addDomain.bind(this);
    }

    get domainList() {
        if (!this.config) {
            return [];
        }

        return this.config.domainList;
    }

    get enabled() {
        return this.config ? true : false;
    }

    setConfig(config) {
        let domainList = [];
        const configDomainList = JSON.parse(config.domainList ?? '[]');
        configDomainList.forEach((domain) => {
            domainList.push({
                item: domain
            })
        });

        this.config = config;
        this.config.domainList = domainList;

        this.config.domainList.forEach((domain) => {
            domain.removeDomain = this.removeDomain.bind(this, domain);
        });
    }

    addDomain() {
        const addDomain = {
            item: this.domainValue,
        }

        addDomain.removeDomain = this.removeDomain.bind(this, addDomain);

        this.domainValue = '';
        this.config.domainList.push(addDomain);
        this.parent.syntax.set('isSave', true);
        this.parent.syntax.update();
    }

    removeDomain(domain) {
        let removeKey = null;
        this.config.domainList.forEach((domainItem, removeKeyItem) => {
            if (domainItem.key === domain.key) {
                removeKey = removeKeyItem;
            }
        });

        delete this.config.domainList[removeKey];
        this.parent.syntax.set('isSave', true);
        this.parent.syntax.update();
    }

    getData() {
        let jsonData = [];
        this.config.domainList.forEach((domain) => {
            jsonData.push(domain.item);
        });

        return {
            'reseller': {
                'domainList': JSON.stringify(jsonData)
            }
        };
    }
}
