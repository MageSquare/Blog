define([
    'Magento_Ui/js/dynamic-rows/record'
], function (Record) {
    'use strict';

    return Record.extend({
        initialize: function () {
            this._super();

            if (this.position === undefined) {
                var elementData = this.data(),
                    parentElement = this.parentComponent();

                this.position = elementData['position'] || elementData[parentElement.positionProvider];
            }
        }
    });
});
