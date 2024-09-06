export default class SlideSelector {
    constructor(element) {
        this.elementRoot = element;
        this.sliders = [];
    }

    register(slider) {
        this.sliders.push(slider);
    }

    render() {
        this.elementWrapper = document.createElement('div');
        this.elementPrevious = document.createElement('div');
        this.elementCurrent = document.createElement('div');
        this.elementNext = document.createElement('div');

        this.elementRoot.classList.add('slideSelectorCanvas');
        this.elementWrapper.classList.add('wrapper');

        this.elementPrevious.classList.add('previous');
        this.elementPrevious.innerHTML = 'Previous';
        this.elementCurrent.classList.add('current');
        this.elementCurrent.innerHTML = 'Current';
        this.elementNext.classList.add('next');
        this.elementNext.innerHTML = 'Next';

        this.elementWrapper.append(this.elementPrevious);
        this.elementWrapper.append(this.elementCurrent);
        this.elementWrapper.append(this.elementNext);
        this.elementRoot.append(this.elementWrapper);
    }

    next() {

    }

    previous() {

    }
}