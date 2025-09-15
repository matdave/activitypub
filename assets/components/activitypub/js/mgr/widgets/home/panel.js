activitypub.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [
            {
                html: '<h2>' + _('activitypub.home.page_title') + '</h2>',
                border: false,
                cls: 'modx-page-header'
            },
            {
                xtype: 'modx-tabs',
                defaults: {
                    border: false,
                    autoHeight: true
                },
                border: true,
                activeItem: 0,
                hideMode: 'offsets',
                items: [
                    {
                        title: _('activitypub.manage.actor'),
                        layout: 'form',
                        items: [
                            {
                                html: _('activitypub.manage.actor_desc'),
                                cls: 'panel-desc'
                            },
                            {
                                xtype: 'activitypub-grid-actor',
                                cls: 'main-wrapper',
                                preventRender: true
                            }
                        ]
                    },
                    {
                        title: _('activitypub.manage.activity'),
                        layout: 'form',
                        items: [
                            {
                                html: _('activitypub.manage.activity_desc'),
                                cls: 'panel-desc'
                            },
                            {
                                xtype: 'activitypub-grid-activity',
                                cls: 'main-wrapper',
                                preventRender: true
                            }
                        ]
                    },
                ]
            }
        ]
    });
    activitypub.panel.Home.superclass.constructor.call(this, config);
}

Ext.extend(activitypub.panel.Home, MODx.Panel);
Ext.reg('activitypub-panel-home', activitypub.panel.Home);