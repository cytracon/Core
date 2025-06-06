var config = {
    map: {
        "*": {
            "mgz.owlcarousel": "Cytracon_Core/js/owl.carousel.min"
        }
    },
    shim: {
       "mgz.owlcarousel": {
            deps:['jquery']
        },
        "Cytracon_Core/js/owl.carousel.min": {
            deps:['jquery']
        },
        'Cytracon_Core/js/jquery-scrolltofixed-min': {
            deps: ['jquery']
        }
    }
};