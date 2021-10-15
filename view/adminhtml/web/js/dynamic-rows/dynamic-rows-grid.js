define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
    'prototype'
], function (dynamicRowsGrid) {
    'use strict';

    return dynamicRowsGrid.extend({
        processingInsertData: function () {
            this._super();
            this._sort();
        },

        setToInsertData: function () {
            var dnd = this.dnd();

            if (dnd && dnd.enabled) {
                if (!this.update) {
                    var dataToInsert = this.elems().map(function (element) {
                        var obj = {};

                        obj[this.positionProvider] = element.position;
                        obj[this.identificationProperty] = element.data()[this.identificationProperty];

                        return obj;
                    }.bind(this));

                    this.source.set(this.dataProvider, dataToInsert);
                }
            } else {
                this._super();
            }
        },

        /**
         * Update data for send after sort
         *
         * @param {number|string}position
         * @param {object} elem
         * @return {*}
         */
        sort: function (position, elem) {
            var result = this._super(position, elem);

            this.setToInsertData();

            return result;
        }
    });
});
