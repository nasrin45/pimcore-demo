document.addEventListener(pimcore.events.postOpenObject, (e) => {
    console.log("postOpenObject event triggered.");
    if (e.detail.object.data.general.className === 'Product') {

        e.detail.object.toolbar.add({
            text: t('Preview'),
            iconCls: 'pimcore_icon_preview',
            scale: 'small',
            handler: function (obj) {
                const id = e.detail.object.id
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `/preview/product/` + id, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const newTab = window.open(' ', '_blank');
                        newTab.document.open();
                        newTab.document.write(xhr.responseText);
                        newTab.document.close();
                        newTab.location.href = '/preview/product/' + id;
                    }
                };

                xhr.send();

                // fetch('/preview/product/' + id, {
                //     method: 'GET',
                // })
                //     .then(response => {
                //         if (response.ok) {
                //             console.log("Response Ok");
                //             return    response.json();
                //         } else {
                //             throw new Error('Network response was not ok');
                //         }
                //     })
                //     .then(data => {
                //         if (data && data.id) {
                //             var id = data.id;
                //             window.open('/preview/product/' + id, '_blank');
                //         } else {
                //             console.error('Product ID not found in the response.');
                //         }
                //     })
                //     .catch(error => {
                //         console.error('Fetch request failed:', error);
                //     });
            }.bind(this, e.detail.object)
        });
        pimcore.layout.refresh();
    }
});


