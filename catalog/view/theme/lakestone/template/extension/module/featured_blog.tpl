<div id="module<?php echo $module; ?>" class="module_featured_blog">
  <div class="row">
    <div class="col-sm-12">
      <div class="maintitle">
        <div class="line-through"></div>
        <div class="plaintext"><?php echo $heading_title; ?></div>
      </div>
    </div>
  </div>
  <div id="canvas_carousel<?=$module?>" style="height:<?=$setting['height']?>px">
    <div id="carousel<?php echo $module; ?>" class="owl-carousel">
      <?php foreach ($articles as $article) { ?>
        <div class="item article-thumb slider text-center">
          <a class="black" href="<?php echo $article['href']; ?>">
            <div class="image banner_darken">
                <img data-src="<?php echo $article['image']; ?>" alt="<?php echo $article['name']; ?>" title="<?php echo $article['name']; ?>" class="img-responsive owl-lazy" />
            </div>
            <div class="caption">
              <h4 class="article_name"><?php echo $article['name']; ?></h4>
              <div class="description"><?=$article['description']?></div>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
  LazyLoadObject($('#canvas_carousel<?=$module?>'), function(){
    $('#carousel<?php echo $module; ?>').owlCarousel({
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
        // 991: {
        //   items: 6
        // },
        // 1200: {
        //   items: 5
        // },
      },
      margin: 15,
      // autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      //animateOut: 'fadeOut',
      nav: true,
      dots: false,
      rewind: true,
      //slideTransition: 'fade',
      lazyLoad: true,
      onInitialize: function() {$('#canvas_carousel<?=$module?>').css('height', '')},
      navText: ['<svg class="owl_button"><use xlink:href="#svg-angle-left"/></svg>', '<svg class="owl_button"><use xlink:href="#svg-angle-right"/></svg>'],
    })
  })
})
--></script>
