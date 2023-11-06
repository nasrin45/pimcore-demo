pimcore.registerNS("pimcore.plugin.Button");

pimcore.plugin.Button = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    retrieveData: function () {

        Ext.Ajax.request({
            url: '/retrieveData',
            method: 'GET',
            success: function (response) {
                const result = Ext.decode(response.responseText);
                if (result.success) {
                    const data = result.data;
                    Ext.getCmp('campusName').setValue(data.campusName);
                    Ext.getCmp('department').setValue(data.department);
                    Ext.getCmp('numberOfStudents').setValue(data.numberOfStudents);
                    Ext.getCmp('facultyName').setValue(data.facultyName);
                    Ext.getCmp('course').setValue(data.course);
                    Ext.getCmp('joiningDate').setValue(data.joiningDate);
                }

            },
        });
    },

    pimcoreReady: function (e) {
        var user = pimcore.globalmanager.get("user");
        var permissions = user.permissions;

        if (permissions.indexOf("objects") !== -1) {
            var navigationUl = Ext.get(Ext.query("#pimcore_navigation UL"));
            var customButton = Ext.DomHelper.createDom('<li id="pimcore_menu_custom-button" data-menu-tooltip="Custom Button" class="pimcore_menu_item icon-custom"></li>');
            navigationUl.appendChild(customButton);
            var customIconImage = document.createElement("img");
            customIconImage.src = "/bundles/pimcoreadmin/img/icon/control_add_blue.png"; // Replace with your custom icon path
            customButton.appendChild(customIconImage);
            pimcore.helpers.initMenuTooltips();


            const submenu = Ext.create('Ext.menu.Menu', {
                items: [
                    {
                        text: 'Add New',
                        handler: this.customPanel.bind(this)
                    }
                ]
            });

            customButton.onclick = function () {
                // Get the position of the custom button
                var customButtonPosition = Ext.get(customButton).getXY();

                // Show the submenu to the right of the custom button
                submenu.showAt([customButtonPosition[0] + customButton.offsetWidth, customButtonPosition[1]]);
            }
        }
    },
    customPanel: function () {
        const customTabPanel = Ext.create('Ext.panel.Panel', { // Use 'Ext.panel.Panel' for a container
            title: 'Custom Panel',
            id: 'customPanel',
            closable: true,
            layout: 'form',
            items: [
                {
                    bbar: [
                        '->',
                        {
                            xtype: 'button',
                            text: 'Save',
                            handler: this.saveData.bind(this)
                        },
                    ],
                    items: [
                        {
                            title: 'Add Campus Details',
                            xtype: 'fieldset',
                            collapsible: true,
                            collapsed: true,
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Campus Name',
                                    id: 'campusName'
                                },
                                {
                                    xtype: 'combo',
                                    fieldLabel: 'Select Department',
                                    id: 'department',
                                    multiSelect: true,
                                    store: ['CS', 'Chemistry', 'Physics', 'Finance', 'Travel And Tourism'],
                                    queryMode: 'local',
                                    editable: false
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Number of Students',
                                    id: 'numberOfStudents'
                                }
                            ]
                        },
                        {
                            title: 'Add Faculty Details',
                            xtype: 'fieldset',
                            collapsible: true,
                            collapsed: true,
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Faculty Name',
                                    id: 'facultyName'
                                },
                                {
                                    xtype: 'combo',
                                    fieldLabel: 'Select Course',
                                    id: 'course',
                                    store: ['CS', 'Chemistry', 'Physics'],
                                    queryMode: 'local',
                                    editable: false
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Joining Date',
                                    id: 'joiningDate'
                                },
                                {
                                    xtype: 'checkbox',
                                    fieldLabel: 'Permission',
                                    id: 'permission'
                                }
                            ]
                        },
                    ],
                    listeners: {
                        afterrender: function () {
                            // Load data when the panel is rendered
                            this.retrieveData();
                        }.bind(this)
                    }
                    },
            ],
        });

        const tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.add(customTabPanel);
        tabPanel.setActiveTab(customTabPanel);
    },
    saveData: function () {
        const campusName = Ext.getCmp('campusName').getValue();
        const department = Ext.getCmp('department').getValue();
        const numberOfStudents = Ext.getCmp('numberOfStudents').getValue();
        const facultyName = Ext.getCmp('facultyName').getValue();
        const course = Ext.getCmp('course').getValue();
        const joiningDate = Ext.getCmp('joiningDate').getValue();
        const permission = Ext.getCmp('permission').getValue();

        var isValid = true;

        if (campusName.trim() === "") {
            Ext.Msg.alert("Validation Error", "Campus Name is required");
            isValid = false;
        }

        if (department.length === 0) {
            Ext.Msg.alert("Validation Error", "Select at least one department");
            isValid = false;
        }

        if (numberOfStudents < 0) {
            Ext.Msg.alert("Validation Error", "Number of Students cannot be negative");
            isValid = false;
        }

        if (facultyName.trim() === "") {
            Ext.Msg.alert("Validation Error", "Faculty Name is required");
            isValid = false;
        }

        if (course === null) {
            Ext.Msg.alert("Validation Error", "Please select a course");
            isValid = false;
        }

        if (!joiningDate) {
            Ext.Msg.alert("Validation Error", "Joining Date is required");
            isValid = false;
        }

        if (isValid) {
            // Proceed with saving if all validations passed
            var formData = {
                campusName: campusName,
                department: department,
                numberOfStudents: numberOfStudents,
                facultyName: facultyName,
                course: course,
                joiningDate: joiningDate,
                permission: permission
            };

            Ext.Ajax.request({
                url: '/customButton', // Replace with the actual server-side script URL
                method: 'POST',
                params: {
                    formData: Ext.encode(formData)
                },
                success: function (response) {
                    Ext.Msg.alert('Success', 'Data saved successfully.');
                },
                failure: function (response) {
                    Ext.Msg.alert('Error', 'Failed to save data.');
                }
            });
        }
    }
});

var ButtonPlugin = new pimcore.plugin.Button();