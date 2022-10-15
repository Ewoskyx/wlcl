let header_height
$(window).on("load scroll", function () {
    header_height = $('#header').outerHeight()
    $('.header_padding').css('padding-top', header_height);

    $('.lazy').each(function () {
        if ($(this).isOnScreen()) {
            $(this).attr('src', $(this).data('src'))
        }
    });
    if ($('.counter-wrapper').length > 0) {
        if ($('.counter-wrapper').isOnScreen()) {
            document.querySelectorAll('.count-up').forEach(el => {
                animateValue(el, 0, el.dataset.value, el.dataset.isPlus, 800);
            })
        }
    }
});
if ($('#myRange').length > 0) {
    $('#myRange').on('input', function (e) {
        let input_val = e.target.value
        $('.range_value').html(input_val)

        $('.unit_count').html(Math.ceil(input_val * 0.60))
        $('.tree_count').html(Math.ceil((input_val * 0.60) / 15.7))
        var value = (this.value - this.min) / (this.max - this.min) * 100
        this.style.background = 'linear-gradient(to right, #fb6c31 0%, #fb6c31 ' + value + '%, #979a96 ' + value + '%, #979a96 100%)'
        if (value > 10) {
            $('.tree1').css('opacity', '1')
        } else {
            $('.tree1').css('opacity', '0')
        }
        if (value > 25) {
            $('.tree2').css('opacity', '1')
        } else {
            $('.tree2').css('opacity', '0')
        }
        if (value > 55) {
            $('.tree3').css('opacity', '1')
        } else {
            $('.tree3').css('opacity', '0')
        }
        if (value > 80) {
            $('.tree4').css('opacity', '1')
        } else {
            $('.tree4').css('opacity', '0')
        }
    });
};


$.fn.isOnScreen = function (e) {
    var win = $(window);
    var viewport = {
        top: win.scrollTop(),
        left: win.scrollLeft()
    };
    viewport.right = viewport.left + win.width();
    viewport.bottom = viewport.top + win.height();
    var bounds = this.offset();
    bounds.right = bounds.left + this.outerWidth();
    bounds.bottom = bounds.top + this.outerHeight();
    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
};

function animation(el, active_el) {
    el.removeClass(active_el.data('animation'))
    active_el.addClass(active_el.data('animation'))

}

if ($('.phone').length > 0) {
    $('.phone').mask('(000) 000 0000');
}
if ($('.industries .nav-item').length > 0) {
    let count = $('.industries .nav-item').length;
    $('.industries .nav-item').css("width", `calc(100% / ${count})`)
}


new Swiper('#slider .swiper-container', {
    loop: false,
    speed: 0,
    touchRatio: 0,
    autoplay: {
        delay: 50000000,
        disableOnInteraction: false,
    },
    on: {
        setTransition: function () {
            animation($('#slider .swiper-slide .text-wrapper'), $('#slider .swiper-slide-active .text-wrapper'))
            animation($('#slider .swiper-slide .image-wrapper'), $('#slider .swiper-slide-active .image-wrapper'))
        }
    }
});
new Swiper('#body .articles .swiper-container', {
    observer: true,
    observeParents: true,
    lazy: true,
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 30,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
        1500: {
            slidesPerView: 4,
            spaceBetween: 30,
        },
    }
});
new Swiper('#body .testimonials .swiper-container', {
    observer: true,
    observeParents: true,
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 30,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        }
    }
});