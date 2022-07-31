<div id="instagram_heading_<?=$module?>" class="module_instagram">
  <div class="container">
    Подписывайтесь на наш инстаграм-канал: <a href="https://www.instagram.com/lakestone.ru/" tarfet="_blank">@lakestone.ru</a>
  </div>
</div>
<div id="canvas_instagram<?=$module?>" style="height:<?=$MinH?>px">
  <div id="instagram<?=$module?>" class="owl-carousel pre-footer">
    <?php foreach ($images as $image) { ?>
    <div class="item text-center">
      <a target="_blank" href="<?=$image['link']?>">
        <img data-src="<?php echo $image['src']; ?>"
             title="<?=$image['title']?>"
             alt="<?php echo $image['desc']; ?>"
             class="img-responsive owl-lazy"/>
      </a>
    </div>
    <?php } ?>
  </div>
</div>

<script type="text/javascript">

  var canvasResizer = function () {
    $('#canvas_instagram<?=$module?>').css('height', '');
  };

  var owlResizer = function (e) {
    var ih = $(e.element).height();
    var $owl = $('.owl-carousel.pre-footer');
    if (ih < $owl.height())
      $owl.height(ih)
  };

  var imgResizer = function (e) {
    $(e.element).css({'width':'','height':''})
  };

  $(function () {
    LazyLoadObject($('#canvas_instagram<?=$module?>'), function () {

      var owl = $('#instagram<?php echo $module; ?>');
      owl.on('resized.owl.carousel, initialized.owl.carousel', function (e) {
        var owlItemWidth = owl.find('.owl-item').css('width');
        var owlItemHeight = owlItemWidth;
        owl.find('.owl-item img').css({
          'width' : owlItemWidth,
          'height' : owlItemHeight,
        });
      });

      owl.owlCarousel({
        responsive: {
          0: {
            items: 2
          },
          480: {
            items: 3
          },
          768: {
            items: 4
          },
          991: {
            items: 5
          }
        },
        // autoWidth: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        nav: false,
        dots: false,
        loop: true,
        lazyLoad: true,
        // onLoadLazy: imgResizer,
        // onLoadedLazy: owlResizer,
        onInitialize: function () {
          $('#canvas_instagram<?=$module?>').css('height', '');
        },
        // navText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>']
      });
    });
  })
</script>
