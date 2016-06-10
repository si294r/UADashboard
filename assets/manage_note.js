Ext.require([
    'Ext.window.Window',
    'Ext.data.*',
    'Ext.grid.*'
]);

Ext.define('Notes', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    fields: ['id', 'event_note', 'tanggal'],
    validators: []
});

Ext.onReady(function () {

    var store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        autoSync: true,
        model: 'Notes',
        proxy: {
            type: 'rest',
            url: document.app_url + 'home/manage_notes',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    },
            writer: {
                type: 'json',
                writeAllFields: true
            }
        },
        listeners: {
            write: function (store, operation) {
                var record = operation.getRecords()[0],
                        name = Ext.String.capitalize(operation.action),
                        verb;


                if (name == 'Destroy') {
                    verb = 'Destroyed';
                } else {
                    verb = name + 'd';
                }
//                            Ext.example.msg(name, Ext.String.format("{0} user: {1}", verb, record.getId()));

            }
        }
    });

    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        listeners: {
            cancelEdit: function (rowEditing, context) {
                // Canceling editing of a locally added, unsaved record: remove it
                if (context.record.phantom) {
                    store.remove(context.record);
                }
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        plugins: [rowEditing],
        frame: true,
        store: store,
        columns: [{
                text: 'Id',
                width: 40,
                sortable: true,
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'Event Note',
                flex: 1,
                sortable: true,
                dataIndex: 'event_note',
                field: {
                    xtype: 'textfield'
                }
            }, {
                header: 'Date',
                width: 120,
                sortable: true,
                dataIndex: 'tanggal',
                field: {
                    xtype: 'datefield', format: 'Y-m-d'
                }
            }],
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        text: 'Add',
                        iconCls: 'icon-add',
                        handler: function () {
                            // empty record
                            var rec = new Notes();
                            store.insert(0, rec);
                            rowEditing.startEdit(rec, 0);
                        }
                    }, '-', {
                        itemId: 'delete',
                        text: 'Delete',
                        iconCls: 'icon-delete',
                        disabled: true,
                        handler: function () {
                            var selection = grid.getView().getSelectionModel().getSelection()[0];
                            if (selection) {
                                store.remove(selection);
                            }
                        }
                    }]
            }]
    });
    grid.getSelectionModel().on('selectionchange', function (selModel, selections) {
        grid.down('#delete').setDisabled(selections.length === 0);
    });

    var winManageNote = Ext.create('Ext.Window', {
        title: 'Manage Note',
        width: 600,
        height: 300,
        plain: true,
        modal: true,
        layout: 'fit',
        closeAction: 'hide',
        items: [grid],
        listeners: {
            hide: {
                fn: function() {
                    reload_chart_by_selection();
                }
            }
        }
    });
    
    $('#btnManageNote').click(function() {
        winManageNote.show();
        grid.getStore().reload();
    });
    
});
