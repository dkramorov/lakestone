<?php if ($screens) { ?>
<div class="module_owl_product_list">
  <div id="carousel-whatsapp_screens" class="owl-carousel">
    <?php foreach ($screens as $screen): ?>
    <div><img data-src="<?php echo $screen['thumb']; ?>" alt="" class="owl-lazy"></div>
    <?php endforeach; ?>
  </div>
</div>
<script>
    $('#carousel-whatsapp_screens').owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 2
            },
            768: {
                items: 3
            },
            991: {
                items: 4,
                dots: true,
            },
            // 1200: {
            //   items: 5
            // },
        },
        margin: 10,
        // autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        //animateOut: 'fadeOut',
        nav: true,
        dots: false,
        rewind: true,
        loop: true,
        lazyLoad: true,
        lazyLoadEager: 1,
        //slideTransition: 'fade',
        /*
        onInitialize: function () {
          $('#canvas_carousel_featured').css('height', '')
        },
         */
        navText: ['<svg class="owl_button"><use xlink:href="#svg-angle-left"/></svg>', '<svg class="owl_button"><use xlink:href="#svg-angle-right"/></svg>'],
    })
</script><?php } else { ?><p><?php echo $text_no_reviews; ?></p><?php } ?>