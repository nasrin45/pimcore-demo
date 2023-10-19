document.addEventListener(pimcore.events.postOpenObject, (e) => {
    console.log("postOpenObject event triggered.");
    // console.log("Object ID:", e.detail.object.data.general.id);
    if (e.detail.object.data.general.className === 'Product') {

        e.detail.object.toolbar.add({
            text: t('Preview'),
            iconCls: 'pimcore_icon_preview',
            scale: 'small',
            handler: function (obj) {

                window.open('/preview/product', '_blank');
            }.bind(this, e.detail.object)
        });
        pimcore.layout.refresh();
    }
});