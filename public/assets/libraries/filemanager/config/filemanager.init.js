$('.fm-container').richFilemanager({
    // options for the plugin initialization step and callback functions, see:
    // https://github.com/servocoder/RichFilemanager/wiki/Configuration-options#plugin-parameters
    callbacks: {
        afterSelectItem: function (resourceObject, url) {
            window.close();
        }
    }
});