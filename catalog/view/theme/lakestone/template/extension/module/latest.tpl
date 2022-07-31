<div class="latest">
<div class="top_link"><a class="black tr2orange" href="/collection">ПОСМОТРЕТЬ ВСЕ</a></div>
<span class="ersatz_head3 text-center"><?php echo $heading_title; ?></span>
<div id="carousel<?php echo $module; ?>" class="owl-carousel">
  <?php foreach ($products as $product) { ?>
    <div class="item product-thumb slider text-center">
      <div class="image" style="width:<?php echo $setting['width']; ?>px">
        <a href="<?php echo $product['href']; ?>">
          <img src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
          <img src="<?php echo $product['images'][1]; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
          <span class="slider-link">Подробнее</span>
        </a>
        <span class="slider-cart" onclick="cart.add('<?php echo $product['product_id']; ?>', 1, '<?php echo (int) $product['price']; ?>');"><i class="fa fa-shopping-cart"></i></span>
      </div>
      <div class="caption">
        <span class="ersatz_head4"><a class="black tr2orange" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></span>
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
      </div>
    </div>
  <?php } ?>
</div>
</div>
<script type="text/javascript"><!--
//StartUpCheck.push([['$(\'#carousel<?php echo $module; ?>\').owlCarousel'], function() {
StartUpCheck.push([['$.fn.owlCarousel'], function() {
  $('#carousel<?php echo $module; ?>').owlCarousel({
          items: 4,
          autoPlay: 6000,
          navigation: true,
          navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
          pagination: false
  })
}])
--></script>
