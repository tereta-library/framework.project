export default class Slider {
    lock = false;

    constructor(element) {
        this.elementRoot = element;
    }

    render(element) {
        this.elementWrapper = document.createElement('div');
        this.elementPrevious = document.createElement('div');
        this.elementCurrent = document.createElement('div');
        this.elementNext = document.createElement('div');

        this.elementRoot.classList.add('sliderCanvas');
        this.elementWrapper.classList.add('wrapper');

        this.elementPrevious.classList.add('previous');
        this.elementCurrent.classList.add('current');
        this.elementNext.classList.add('next');

        if (typeof element == 'string') {
            this.elementCurrent.innerHTML = element;
        } else {
            this.elementCurrent.append(element);
        }

        this.elementWrapper.append(this.elementPrevious);
        this.elementWrapper.append(this.elementCurrent);
        this.elementWrapper.append(this.elementNext);
        this.elementRoot.append(this.elementWrapper);
    }

    next(element, callback = null) {
        if (this.lock) {
            return false;
        }
        this.lock = true;
        this.elementNext.innerHTML = '';

        if (typeof element == 'string') {
            this.elementNext.innerHTML = element;
        } else {
            this.elementNext.append(element);
        }

        this.elementRoot.classList.add('slotRight');
        this.elementRoot.classList.remove('slotLeft');

        const time = this.getCssTime(this.elementWrapper);
        setTimeout(() => this.currentPosition(element, time, callback), time);
        return true;
    }

    previous(element, callback = null) {
        if (this.lock) {
            return false;
        }

        this.lock = true;
        this.elementPrevious.innerHTML = '';

        if (typeof element == 'string') {
            this.elementPrevious.innerHTML = element;
        } else {
            this.elementPrevious.append(element);
        }

        this.elementRoot.classList.add('slotLeft');
        this.elementRoot.classList.remove('slotRight');

        const time = this.getCssTime(this.elementWrapper);
        setTimeout(() => this.currentPosition(element, time, callback), time);

        return true;
    }

    currentPosition(element, time, callback) {
        this.elementRoot.classList.add('replacement');
        this.elementRoot.classList.remove('slotLeft');
        this.elementRoot.classList.remove('slotRight');
        this.elementCurrent.innerHTML = '';
        this.elementCurrent.append(element);

        setTimeout(() => {
            this.elementRoot.classList.remove('replacement');
            this.lock = false;
            if (callback) {
                callback();
            }
        }, time);
    }


    getCssTime(element) {
        const transitionTime = window.getComputedStyle(element)['transition'];

        if (transitionTime.endsWith('s')) {
            return parseFloat(transitionTime.replace('s', '')) * 1000;
        }

        return 1000;
    }
}
