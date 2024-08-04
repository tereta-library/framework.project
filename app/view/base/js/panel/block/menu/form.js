import AdminTemplateForm from '../templateForm.js';
import Syntax from '../../syntax.js';

export class AdminMenuForm extends AdminTemplateForm {
    template = 'admin/menu/form';
    uniqueId = 0;
    selectedMenuItem = null;
    menu = [];
    menuOriginal = [];
    syntax = null;

    show(config) {
        this.menuOriginal = config.menu;
        this.menu = JSON.parse(JSON.stringify(this.menuOriginal));
        //this.menu = JSON.parse('[]');

        const parentNode = {
            'label': 'Root',
            'subMenu': this.menu
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
        item.adminId       = 'unique_item_' + (this.uniqueId++);
        item.parent        = parentNode;
        item.parentListing = parentNode ? parentNode.subMenu : [];
        item.actionEdit    = this.actionEdit.bind(this, item);
        item.actionRemove  = this.actionRemove.bind(this, item)

        if (item.subMenu && item.subMenu.length > 0) {
            this.prepareMenu(item.subMenu, item);
        } else {
            item.subMenu = [];
        }
    }

    clearMenu(listing) {
        listing.forEach((item, index) => {
            delete item.adminId;
            delete item.parent;
            delete item.parentListing;
            delete item.actionEdit;
            delete item.actionRemove;
            delete item.opened;

            if (item.subMenu && item.subMenu.length > 0) {
                item.subMenu = this.clearMenu(item.subMenu);
            } else {
                delete item.subMenu;
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
        console.info('actionRemove');
        console.info(item);

        if (this.selectedMenuItem && item.adminId === this.selectedMenuItem.adminId) {
            this.selectedMenuItem = null;
            this.syntax.set('isSelected', false);
        }

        item.parentListing.forEach((listingItem, index) => {
            if (listingItem.adminId === item.adminId) {
                item.parentListing.splice(index, 1);
            }
        });

        this.syntax.set('isSave', true).update();
    }

    actionChange(event, element, parameter) {
        if (!this.selectedMenuItem) {
            return;
        }

        this.selectedMenuItem[parameter] = element.value;
        this.syntax.set('isSave', true).update();
    }

    actionAddSub() {
        let newItem = {
            'label': 'New item',
            'url'  : ''
        };

        this.prepareMenuItem(newItem, this.selectedMenuItem);
        this.selectedMenuItem.subMenu.push(newItem)
        this.syntax.set('isSave', true).update();
    }

    actionAddBefore() {
        let newItem = {
            'label': 'New item',
            'url'  : ''
        };

        this.prepareMenuItem(newItem, this.selectedMenuItem.parent);
        this.selectedMenuItem.parentListing.splice(this.selectedMenuItem.parentListing.indexOf(this.selectedMenuItem), 0, newItem);
        this.syntax.set('isSave', true).update();
    }

    actionAddAfter() {
        let newItem = {
            'label': 'New item',
            'url': ''
        };

        this.prepareMenuItem(newItem, this.selectedMenuItem.parent);

        this.selectedMenuItem.parentListing.splice(this.selectedMenuItem.parentListing.indexOf(this.selectedMenuItem) + 1, 0, newItem);

        this.syntax.set('isSave', true).update();
    }

    actionCreateFirst() {
        let newItem = {
            'label': 'New item',
            'url': ''
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
        xhr.open('POST', '/api/admin/menu', true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader("Content-Type",  "application/json;charset=UTF-8");
        xhr.setRequestHeader('Authorization', "Bearer " + token);
        xhr.onload = (xhr) => {
            if (xhr.target.status === 200) {
                this.syntax.set('successMessage', 'Menu saved')
                    .set('showSuccessMessage', true)
                    .set('isSave', false).update();

                setTimeout(() => {
                    syntax.set('showSuccessMessage', false);
                    syntax.update();
                }, 5000);
            }
        };

        this.menuOriginal = menu;
        xhr.send(JSON.stringify({
            'menu': this.menuOriginal
        }));
    }
}
