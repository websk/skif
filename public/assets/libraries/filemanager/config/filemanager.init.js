$('.fm-container').richFilemanager({
    callbacks: {
        afterSelectItem: function () {
            window.close();
        },
    },
});
