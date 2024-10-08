import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';

export class AdminMenuForm extends AdminTemplateForm {
    template = 'block/menu/form';
    uniqueId = 0;
    selectedMenuItem = null;
    menu = [];
    menuOriginal = [];
    removeIds = [];
    syntax = null;

    show(config) {
        this.identifier = config.identifier;
        this.menuOriginal = config.menu;
        this.menu = JSON.parse(JSON.stringify(this.menuOriginal));

        const parentNode = {
            'label': 'Root',
            'menu': this.menu
        }
        this.prepareMenu(this.menu, parentNode);

        this.syntax = (new Syntax(this.node, {
            debug             : this.node,
            successMessage    : '',
            showSuccessMessage: false,
            isSave            : false,
            menu              : this.menu,
            isSelected        : false,
            onClose           : this.onClose.bind(this),
            actionChange      : this.actionChange.bind(this),
            saveForm          : this.saveForm.bind(this),
            getItemData       : this.getItemData.bind(this),
            actionAddSub      : this.actionAddSub.bind(this),
            actionAddBefore   : this.actionAddBefore.bind(this),
            actionAddAfter    : this.actionAddAfter.bind(this),
            actionCreateFirst : this.actionCreateFirst.bind(this)
        }));

        this.syntax.update();

        super.show();
    }

    prepareMenu(listing, parentNode) {
        listing.forEach((item, index) => {
            this.prepareMenuItem(item, parentNode);
        });
    }

    prepareMenuItem(item, parentNode) {
        item.opened        = false;
        item.clientId      = (this.uniqueId++);
        item.parent        = parentNode;
        item.parentListing = parentNode ? parentNode.menu : [];
        item.actionEdit    = this.actionEdit.bind(this, item);
        item.actionRemove  = this.actionRemove.bind(this, item)

        if (item.menu && item.menu.length > 0) {
            this.prepareMenu(item.menu, item);
        } else {
            item.menu = [];
        }
    }

    clearMenu(listing) {
        listing.forEach((item, index) => {
            delete item.parent;
            delete item.parentListing;
            delete item.actionEdit;
            delete item.actionRemove;
            delete item.opened;

            if (item.menu && item.menu.length > 0) {
                item.menu = this.clearMenu(item.menu);
            } else {
                delete item.menu;
            }
        });

        return listing;
    }

    getItemData(properties) {
        if (this.selectedMenuItem && this.selectedMenuItem[properties]) {
            return this.selectedMenuItem[properties];
        }

        return null;
    }

    actionEdit(item) {
        this.selectedMenuItem = item;
        if (item.opened) {
            item.opened = false;
        } else {
            item.opened = true;
        }
        this.syntax.set('isSelected', true).update();
    }

    actionRemove(item) {
        if (this.selectedMenuItem && item.clientId === this.selectedMenuItem.clientId) {
            this.selectedMenuItem = null;
            this.syntax.set('isSelected', false);
        }

        item.parentListing.forEach((listingItem, index) => {
            if (listingItem.clientId === item.clientId) {
                item.parentListing.splice(index, 1);
            }
        });

        if (item.id) {
            this.removeIds.push(item.id);
        }
        this.syntax.set('isSave', true).update();
    }

    actionChange(event, element, parameter) {
        if (!this.selectedMenuItem) {
            return;
        }

        this.selectedMenuItem.isEdited = true;
        this.selectedMenuItem[parameter] = element.value;
        this.syntax.set('isSave', true).update();
    }

    actionAddSub() {
        let newItem = {
            'label'   : 'New item',
            'link'    : '',
            'clientId': (this.uniqueId++)
        };

        this.prepareMenuItem(newItem, this.selectedMenuItem);
        this.selectedMenuItem.menu.push(newItem)
        this.syntax.set('isSave', true).update();
    }

    actionAddBefore() {
        let newItem = {
            'label': 'New item',
            'link'  : '',
            'clientId': (this.uniqueId++)
        };

        this.prepareMenuItem(newItem, this.selectedMenuItem.parent);
        this.selectedMenuItem.parentListing.splice(this.selectedMenuItem.parentListing.indexOf(this.selectedMenuItem), 0, newItem);
        this.syntax.set('isSave', true).update();
    }

    actionAddAfter() {
        let newItem = {
            'label': 'New item',
            'link': '',
            'clientId': (this.uniqueId++)
        };

        this.prepareMenuItem(newItem, this.selectedMenuItem.parent);
        this.selectedMenuItem.parentListing.splice(this.selectedMenuItem.parentListing.indexOf(this.selectedMenuItem) + 1, 0, newItem);
        this.syntax.set('isSave', true).update();
    }

    actionCreateFirst() {
        let newItem = {
            'label': 'New item',
            'link': '',
            'clientId': (this.uniqueId++)
        };

        this.prepareMenuItem(newItem, null);
        this.menu.push(newItem);
        this.syntax.set('isSave', true).update();
    }

    saveForm(event) {
        const token = this.rootAdminJs.getToken();
        const syntax = this.syntax;
        event.preventDefault();

        const menuClean = this.syntax.json.clean(this.menu);
        const menu = this.clearMenu(menuClean);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/api/json/menu/configuration/${this.identifier}`, true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type",  "application/json;charset=UTF-8");
        xhr.setRequestHeader('API-Token', token);
        xhr.onload = (xhr) => {
            if (xhr.target.status !== 200) {
                return;
            }

            this.syntax.set('successMessage', 'Menu saved')
                .set('showSuccessMessage', true)
                .set('isSave', false);

            this.applySavedData(JSON.parse(xhr.target.responseText));
            this.removeIds = [];

            this.rootAdminJs.elementCanvas.contentWindow.location.reload();

            setTimeout(() => {
                syntax.set('showSuccessMessage', false);
                syntax.update();
            }, 5000);
        };

        this.menuOriginal = menu;
        xhr.send(JSON.stringify({
            'removeIds': this.removeIds,
            'menu': this.menuOriginal
        }));

        this.unstateMenu(this.menu);
    }

    applySavedData(data) {
        const menuItems = this.fetchMenuListed(this.menu);
        let clientIdMap = {};
        menuItems.forEach((item) => {
            if (item.clientId) {
                clientIdMap[item.clientId] = item;
            }
        });

        data.forEach((item) => {
            if (clientIdMap[item.clientId]) {
                clientIdMap[item.clientId].id = item.id;
                clientIdMap[item.clientId].clientId = null;
            }
        });
    }

    fetchMenuListed(items, listing = []) {
        items.forEach((item) => {
            listing.push(item);
            if (item.menu && item.menu.length > 0) {
                this.fetchMenuListed(item.menu, listing);
            }
        });

        return listing;
    }

    unstateMenu(menu) {
        menu.forEach((item) => {
            delete item.isEdited;
            if (item.menu && item.menu.length > 0) {
                this.unstateMenu(item.menu);
            }
        });
    }
}
