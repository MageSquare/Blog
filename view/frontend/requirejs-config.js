var config = {
    map: {
        '*': {
            magesquare_appendaround: 'MageSquare_Blog/js/vendor/appendaround/appendaround',
            msBlogAccord : 'MageSquare_Blog/js/msBlogAccord',
            msBlogViewStatistic: 'MageSquare_Blog/js/msBlogViewStatistic',
            msBlogTabs: 'MageSquare_Blog/js/tabs',
            msBlogViewsList: 'MageSquare_Blog/js/posts-lists-counter-update'
        }
    },
    paths: {
        slick: 'MageSquare_Base/vendor/slick/slick.min',
        catalogAddToCart: 'Magento_Catalog/js/catalog-add-to-cart'
    },
    shim: {
        slick: {
            deps: [ 'jquery' ]
        }
    }
};
