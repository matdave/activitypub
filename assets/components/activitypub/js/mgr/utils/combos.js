activitypub.combo.ActorType = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        fields: ['value'],
        displayField: "value",
        valueField: "value",
        mode: "remote",
        triggerAction: "all",
        emptyText: _("activitypub.combo.empty.directive"),
        editable: true,
        selectOnFocus: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true,
        url: activitypub.config.connectorUrl,
        pageSize: 10,
        baseParams: {
            action: "MatDave\\ActivityPub\\Processors\\Combo\\ActorType",
        }
    });
    activitypub.combo.ActorType.superclass.constructor.call(this, config);
};
Ext.extend(activitypub.combo.ActorType, MODx.combo.ComboBox);
Ext.reg("activitypub-combo-actor_type", activitypub.combo.ActorType);

activitypub.combo.ActivityType = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        fields: ['value'],
        displayField: "value",
        valueField: "value",
        mode: "remote",
        triggerAction: "all",
        emptyText: _("activitypub.combo.empty.directive"),
        editable: true,
        selectOnFocus: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true,
        url: activitypub.config.connectorUrl,
        pageSize: 10,
        baseParams: {
            action: "MatDave\\ActivityPub\\Processors\\Combo\\ActivityType",
        }
    });
    activitypub.combo.ActivityType.superclass.constructor.call(this, config);
};
Ext.extend(activitypub.combo.ActivityType, MODx.combo.ComboBox);
Ext.reg("activitypub-combo-activity_type", activitypub.combo.ActivityType);

activitypub.combo.ObjectType = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        fields: ['value'],
        displayField: "value",
        valueField: "value",
        mode: "remote",
        triggerAction: "all",
        emptyText: _("activitypub.combo.empty.directive"),
        editable: true,
        selectOnFocus: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true,
        url: activitypub.config.connectorUrl,
        pageSize: 10,
        baseParams: {
            action: "MatDave\\ActivityPub\\Processors\\Combo\\ObjectType",
        }
    });
    activitypub.combo.ObjectType.superclass.constructor.call(this, config);
};
Ext.extend(activitypub.combo.ObjectType, MODx.combo.ComboBox);
Ext.reg("activitypub-combo-object_type", activitypub.combo.ObjectType);