define([
    'uiComponent',
    'ko',
], function (Component, ko) {
    return Component.extend({
        defaults: {
            notifications: [],
        },
        initialize() {
            this._super();

            this.notifications.forEach(({ id, count }) => {
                const menu = document.querySelector(`#${id} > a`);
                if (menu) {
                    menu.dataset.notifications = count > 9 ? '9+' : count;
                }
            });
        },
    });
})
