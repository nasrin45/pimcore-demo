pimcore.registerNS("pimcore.plugin.TrackingBundle");

pimcore.plugin.TrackingBundle = Class.create({
    gridPanel: null, // Add a property to store the grid panel instance

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // Your code can be added here
        var user = pimcore.globalmanager.get("user");
        var permissions = user.permissions;

        if (permissions.indexOf("objects") !== -1) {
            var navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            var newMenuItem = Ext.DomHelper.createDom('<li id="pimcore_menu_new-item" data-menu-tooltip="Admin Tracking" class="pimcore_menu_item icon-fork"></li>');
            navigationUl.appendChild(newMenuItem);
            var iconImage = document.createElement("img");
            iconImage.src = "/bundles/pimcoreadmin/img/icon/chart_curve.png";
            newMenuItem.appendChild(iconImage);
            pimcore.helpers.initMenuTooltips();

            newMenuItem.onclick = this.displayPanel.bind(this); // Ensure "this" refers to the class instance
        }
    },

    displayPanel: function () {

        var store = new Ext.data.Store({
            fields: ['id', 'admin_id', 'action', 'date'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: '/track',
                reader: {
                    type: 'json',
                    rootProperty: 'list',
                    totalProperty: 'total',
                }
            }
        })

        const tabPanel = Ext.getCmp('pimcore_panel_tabs');
        const existingTabPanel = Ext.getCmp('admin_tab_panel');

        if (existingTabPanel) {
            tabPanel.setActiveTab(existingTabPanel);
            return;
        }

        if (!this.gridPanel) {
            // Create a new grid with columns: id, admin id, action, and data
            this.gridPanel = Ext.create('Ext.grid.Panel', {
                title: 'Admin Tracking',
                id: 'admin_tab_panel', // Set an ID for the tab panel
                width: 1500,
                height: 400,
                closable: true,
                store: store,
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Displaying {0} - {1} of {2}',
                    emptyMsg: "No data to display",
                }),
                columns: [
                    { text: 'ID', dataIndex: 'id' },
                    { text: 'Admin ID', dataIndex: 'admin_id' },
                    { text: 'Action', dataIndex: 'action' },
                    { text: 'Date', dataIndex: 'date' }
                ],
                listeners: {
                    close: function () {
                        // Remove the reference when the tab is closed
                        this.gridPanel = null;
                    }.bind(this)
                }
            });

            tabPanel.add(this.gridPanel);
        }

        tabPanel.setActiveItem(this.gridPanel);
    }
});

var TrackingBundlePlugin = new pimcore.plugin.TrackingBundle();
