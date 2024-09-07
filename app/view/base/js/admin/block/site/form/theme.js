export class AdminSiteFormTheme {
    slider = null;
    parent = null;
    locked = false;
    currentThemeNumber = 1;

    constructor(parent, slider) {
        this.slider = slider;
        this.parent = parent;
    }

    render() {
        this.themeLoad(
            this.currentThemeNumber,
            (content, number) => {
                this.slider.render(content);
                this.themeLoaded(number);
            }
        );
    }

    previous() {
        this.themeLoad(
            this.currentThemeNumber - 1,
            (content, number) => {
                this.slider.previous(content, this.themeLoaded.bind(this, number))
            }
        );
    }

    next() {
        this.themeLoad(
            this.currentThemeNumber + 1,
            (content, number) => {
                this.slider.next(content, this.themeLoaded.bind(this, number));
            }
        );
    }

    themeLoaded(themeNumber) {
        this.locked = false;
        this.parent.syntax.update();
        this.currentThemeNumber = themeNumber;
    }

    themeLoad(themeNumber, callback) {
        this.locked = true;
        this.parent.syntax.update();

        const token = this.parent.rootAdminJs.getToken();

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/json/site/configuration/theme/' + themeNumber, true);
        xhr.setRequestHeader('Cache-Control', 'no-cache');
        xhr.setRequestHeader('API-Token', token);

        xhr.onload = function(xhr) {
            if (this.status !== 200) {
                return;
            }

            const jsonResponse = JSON.parse(this.responseText);
            callback('Theme ' + jsonResponse.number, parseInt(jsonResponse.number));
        }

        xhr.send();
    }
}