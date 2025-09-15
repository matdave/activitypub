var ActivityPub = function (config) {
    config = config || {};
    ActivityPub.superclass.constructor.call(this, config);
};
Ext.extend(ActivityPub, Ext.Component, {

    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
    go: function(action, id) {
        location.href = '?a=' + action + (id ? '&id=' + id : '') + '&namespace=activitypub';
    },

});
Ext.reg('activitypub', ActivityPub);
activitypub = new ActivityPub();
