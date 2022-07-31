<div id="module<?php echo $module; ?>" class="module_owl_product_list">
  <? if ($heading_title) { ?>
  <div class="maintitle">
    <div class="line-through"></div>
    <div class="plaintext"><?php echo $heading_title; ?></div>
  </div>
  <? } ?>
  <div id="canvas_carousel<?=$module?>" style="height:<?=$setting['height']?>px">
    <div id="carousel<?=$module?>" class="owl-carousel">
      <?php foreach ($products as $product) { ?>
        <div class="item product-thumb text-center">
          <div class="image">
            <a href="<?php echo $product['href']; ?>">
              <img data-src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>" class="img-responsive owl-lazy" />
            </a>
            <? if (isset($product['sale'])) {?>
              <span class="badge sale"><?=$product['sale']?></span>
            <? } ?>
          </div>
          <div class="caption">
            <h4><a class="product_name" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
            <? if (isset($product['short']) and $product['short']) { ?>
            <div class="short-description" itemprop="description"><?=$product['short']?></div>
            <? } ?>
            <? if (isset($product['sku']) and $product['sku']) { ?>
              <meta itemprop="sku" content="<?=$product['sku']?>" />
            <? } ?>
            <? if (isset($product['attributes']) and $product['attributes']) { ?>
            <div class="attributes">
            <? foreach ($product['attributes'] as $attribute) { ?>
              <div class="attribute">
                <span class="name"><?=$attribute['name']?></span>:
                <span class="value"><?=$attribute['text']?></span>
              </div>
            <? } ?>
            </div>
            <? } ?>
            <?php if ($product['price']) { ?>
            <p class="price">
              <?php if (!$product['special']) { ?>
              <?php echo $product['price']; ?>
              <?php } else { ?>
              <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
              <?php } ?>
              <?php if ($product['tax']) { ?>
              <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
              <?php } ?>
            </p>
            <?php } ?>
            <? if (isset($ProductRating) and $ProductRating) { ?>
            <? if ($product['rating']) { ?>
            <div class="review" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
              <meta itemprop="ratingValue" content="<?=$product['rating']?>" />
              <span class="stars">
                <a href="<?=$product['href']?>#reviews">
                <span class="rating">
                  <?php for ($i = 1; $i <= 5; $i++) { ?>
                  <?php if ($product['rating'] < $i) { ?>
                  <svg class="star"><use xlink:href="#svg-star"></svg>
                  <?php } else { ?>
                  <svg class="star full"><use xlink:href="#svg-star"></svg>
                  <?php } ?>
                  <?php } ?>
                </span>
                </a>
              </span>
              <span class="text">
                <a title="Оставить отзыв" class="blue" href="<?=$product['href']?>#reviews"><span itemprop="reviewCount"><?=$product['reviews_num']?></span><?=$product['reviews']?></a>
              </span>
            </div>
            <? } else { ?>
            <div class="review">
              <span class="stars">
                <a href="<?=$product['href']?>#reviews">
                <span class="rating">
                  <?php for ($i = 1; $i <= 5; $i++) { ?>
                  <svg class="star"><use xlink:href="#svg-star"></svg>
                  <?php } ?>
                </span>
                </a>
              </span>
              <span class="text">
                <a class="blue" href="<?=$product['href']?>#reviews">отзывов пока нет</a>
              </span>
            </div>
            <? }} ?>
          </div>
          <div class="quickview" onclick="showQuickview(<?=$product['product_id']?>)">Быстрый просмотр</div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
  LazyLoadObject($('#canvas_carousel<?=$module?>'), function(){
    $('#carousel<?=$module?>').owlCarousel({
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
