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
                const menu = document.getElementById('menu-magento-marketplace-partners');
                if (menu) {
                    menu.dataset.notifications = count > 9 ? '9+' : count;
                }
            });
        },
    });
})
