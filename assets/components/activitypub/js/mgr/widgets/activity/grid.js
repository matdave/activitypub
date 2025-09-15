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
            'public',
            'sensitive',
            'createdon',
            'actor_username',
            'resource_pagetitle',
            'resource_content',
            'resource_introtext',
        ],
        autosave: true,
        autoHeight: true,
        paging: true,
        remoteSort: true,
        autoExpandColumn: 'resource_content',
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
            },
            {
                header: _('activitypub.field.createdon'),
                dataIndex: 'createdon',
                sortable: true
            },
            {
                header: _('activitypub.field.pagetitle'),
                dataIndex: 'resource_pagetitle',
                sortable: true,
            },
            {
                header: _('activitypub.field.content'),
                dataIndex: 'resource_content',
                sortable: true,
                renderer: function (value, metaData, record) {
                    if (record.data.resource_introtext) {
                        return record.data.resource_introtext;
                    }
                    const content = document.createElement('div');
                    content.innerHTML = record.data.resource_content;
                    const text = content.textContent || content.innerText || '';
                    if (text > 100) {
                        return text.substr(0, 100) + '...';
                    }
                    return text;
                }
            },
            {
                header: _('activitypub.field.actor'),
                dataIndex: 'actor_username',
                sortable: true
            },
        ]
    });
    activitypub.grid.Activity.superclass.constructor.call(this, config);
}

Ext.extend(activitypub.grid.Activity, MODx.grid.Grid, {});

Ext.reg('activitypub-grid-activity', activitypub.grid.Activity);