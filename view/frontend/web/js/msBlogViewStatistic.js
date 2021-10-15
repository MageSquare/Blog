define([
    'jquery',
    'mage/url',
    'mage/cookies',
    'pageCache',
    'mage/translate',
], function ($, urlBuilder) {
    'use strict';

    $.widget('magesquare_blog.msBlogViewStatistic', {
        options: {
            postId: null,
            baseUrl: window.BASE_URL,
            backendUrl: 'msblog/index/view'
        },

        _create: function () {
            urlBuilder.setBaseUrl(this.options.baseUrl);
            this.element.formKey();
            this.updateViewsCount()
        },

        /**
         *
         * @param {number} viewsCount
         */
        updateViewsCounterValue: function (viewsCount) {
            if (!isNaN(viewsCount)) {
                this.element.html($.mage.__('%1 view(s)').replace('%1', viewsCount));
            }
        },

        updateViewsCount: function () {
            $.ajax({
                method: 'POST',
                url: urlBuilder.build(this.options.backendUrl),
                data: {
                    form_key: $.mage.cookies.get('form_key'),
                    post_id: this.options.postId
                },
                success: function (result) {
                    this.updateViewsCounterValue(Number(result['views_count']));
                }.bind(this)
            });
        }
    });

    return $.magesquare_blog.msBlogViewStatistic;
});
