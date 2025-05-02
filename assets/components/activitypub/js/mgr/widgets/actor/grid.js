activitypub.grid.Actor = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        url: activitypub.config.connectorUrl,
        baseParams: {
            action: 'MatDave\\ActivityPub\\Processors\\Actor\\GetList',
            sort: 'username',
            dir: 'asc'
        },
        fields: [
            'id',
            'type',
            'user',
            'username',
            'fullname'
        ],
        autosave: true,
        autoHeight: true,
        paging: true,
        remoteSort: true,
        autoExpandColumn: 'username',
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                sortable: true,
                hidden: true
            },
            {
                header: _('activitypub.field.type'),
                dataIndex: 'type',
                sortable: true,
                editor: {
                    xtype: 'activitypub-combo-actor_type'
                }
            },
            {
                header: _('username'),
                dataIndex: 'username',
                sortable: true
            },
            {
                header: _('activitypub.field.fullname'),
                dataIndex: 'fullname',
                sortable: true
            },
        ]
    });
    activitypub.grid.Actor.superclass.constructor.call(this, config);
}

Ext.extend(activitypub.grid.Actor, MODx.grid.Grid, {});

Ext.reg('activitypub-grid-actor', activitypub.grid.Actor);