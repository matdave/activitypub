activitypub.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [
            {
                xtype: 'activitypub-panel-home',
                renderTo: 'custom-ext-panel-div'
            }
        ]
    });
    activitypub.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(activitypub.page.Home, MODx.Component);
Ext.reg('activitypub-page-home', activitypub.page.Home);
