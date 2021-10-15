var config = {
    map: {
        '*': {
            msBlogSlider: "MageSquare_Blog/js/blog-slider"
        }
    },
    paths: {
        slick: 'MageSquare_Base/vendor/slick/slick.min'
    },
    shim: {
        msBlogSlider: {
            deps: [ 'MageSquare_Base/vendor/slick/slick.min' ]
        },
        slick: {
            deps: [ 'jquery' ]
        }
    }
};
