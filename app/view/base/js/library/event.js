export default class Event {
    constructor() {
        this.events = {};
        this.singleEvents = {};
    }

    register(event, callback, single = false) {
        if (single && typeof this.singleEvents[event] == 'undefined') {
            this.singleEvents[event] = [];
        }

        if (single && this.singleEvents[event].includes(single)) {
            return;
        } else if (single) {
            this.singleEvents[event].push(event);
        }

        if (!this.events[event]) {
            this.events[event] = [];
        }

        this.events[event].push(callback);
    }

    dispatch(event, ...args) {
        if (this.events[event]) {
            this.events[event].forEach(callback => {
                callback(...args);
            });
        }
    }
}
