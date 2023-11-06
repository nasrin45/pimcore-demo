pimcore.registerNS("pimcore.plugin.CustomBundle");

pimcore.plugin.CustomBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("CustomBundle ready!");
    }
});

var CustomBundlePlugin = new pimcore.plugin.CustomBundle();
