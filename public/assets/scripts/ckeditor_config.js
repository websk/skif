/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config

    config.language = 'ru';

    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Subscript,Superscript';

    // Set the most common block elements.
    config.format_tags = 'p;div;h1;h2;h3;h4;h5';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'link:advanced';

    // при нажатии enter добавляем br
    config.enterMode = CKEDITOR.ENTER_BR;
    config.shiftEnterMode = CKEDITOR.ENTER_P;
    config.protectedSource.push(/<script[\s\S]*?script>/g); /* script tags */

    config.allowedContent = 'h1 h2 h3 h4 h5 br b i u em ul ol li tbody thead hr pre;'
        + 'div(*)[*]{*};'
        + 'p[*];'
        + 'table(*);'
        + 'tr(*);'
        + 'th(*);'
        + 'td(*);'
        + 'a(*)[!href, id, name, target];'
        + 'img(*)[!src,alt,title,width,height]{*};';

    config.fillEmptyBlocks = false;
    config.entities = false;
    config.extraPlugins = 'find';

    config.find_highlight = {
        element: 'span',
        styles: {'background-color': '#ff0'}
    };

    config.linkDefaultProtocol = 'https://';
};

CKEDITOR.on('dialogDefinition', (ev) => {
    if (ev.data.name === 'link') {
        ev.data.definition.getContents('info').get('protocol').items = [
            ["http://", "http://"],
            ["https://", "https://"],
            ['Другой', ""]
        ];
    }
});