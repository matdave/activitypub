activitypub.grid.Activity = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        url: activitypub.config.connectorUrl,
        baseParams: {
            action: 'MatDave\\ActivityPub\\Processors\\Activity\\GetList',
            sort: 'createdon',
            dir: 'desc'
        },
        fields: [
            'id',
            'actor',
            'resource',
            'type',
            'summary',
            'content',
            'public',
            'sensitive',
            'createdon',
            'actor_username',
            'resource_pagetitle'
        ],
        autosave: true,
        autoHeight: true,
        paging: true,
        remoteSort: true,
        autoExpandColumn: 'summary',
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
                    xtype: 'activitypub-combo-activity_type'
                }
            },
            {
                header: _('activitypub.field.createdon'),
                dataIndex: 'createdon',
                sortable: true
            },
            {
                header: _('activitypub.field.summary'),
                dataIndex: 'summary',
                sortable: true
            },
            {
                header: _('activitypub.field.actor'),
                dataIndex: 'actor_username',
                sortable: true
            },
            {
                header: _('activitypub.field.pagetitle'),
                dataIndex: 'resource_pagetitle',
                sortable: true,
                hidden: true
            },
        ]
    });
    activitypub.grid.Activity.superclass.constructor.call(this, config);
}

Ext.extend(activitypub.grid.Activity, MODx.grid.Grid, {});

Ext.reg('activitypub-grid-activity', activitypub.grid.Activity);