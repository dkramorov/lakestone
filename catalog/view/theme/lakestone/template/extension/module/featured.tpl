<style>
.module_featured {
  margin-top: 20px;
}
.module_featured .owl-carousel .owl-nav > button {
  top: 30%;
  box-shadow: 0 0 20px -10px black;
  background-color: white;
}
.module_featured .maintitle {
  text-align: center;
}
.module_featured .maintitle > div {
  display: inline-block;
}
.module_featured .maintitle .line-through {
  border-top: solid thin #ccc;
  width: 100%;
}
.module_featured .maintitle .plaintext {
  background-color: white;
  position: relative;
  top: -25px;
  padding: 0 50px;
  font-size: 25px;
  font-weight: 600;
  line-height: 40px;
  color: black;
}
.caption a.product_name {
  color: black;
  font-size: 15px;
  text-decoration: underline;
  -webkit-text-decoration-color: #ccc;
  text-decoration-color: #ccc;
}
.caption a.product_name:hover {
  text-decoration: none;
}
.caption .price-new,
.caption .price-old,
.caption .price {
  font-size: 17px;
  font-weight: 700;
}
</style>
<div id="module_<?php echo $module; ?>" class="module_featured">
  <div class="row">
    <div class="col-sm-12">
      <div class="maintitle">
        <div class="line-through"></div>
        <div class="plaintext"><?php echo $heading_title; ?></div>
      </div>
    </div>
  </div>
  <div id="carousel<?php echo $module; ?>" class="owl-carousel">
    <?php foreach ($products as $product) { ?>
      <div class="item product-thumb slider text-center">
        <div class="image" style="width:<?php echo $setting['width']; ?>px">
          <a href="<?php echo $product['href']; ?>">
            <img data-src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive owl-lazy" />
            <img data-src="<?php echo $product['images'][1]; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive owl-lazy" />
            <span class="slider-link">Подробнее</span>
          </a>
          <span class="slider-cart" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i></span>
        </div>
        <div class="caption">
          <h4><a class="product_name" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
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
  <!--
          <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button>
  -->
      </div>
    <?php } ?>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
  $('#carousel<?php echo $module; ?>').owlCarousel({
    items: 4,
    autoPlay: 5000,
    //animateOut: 'fadeOut',
    nav: true,
    dots: false,
    rewind: true,
    lazyLoad: true,
    //slideTransition: 'fade',
    navText: ['<svg class="owl_button"><path d="M10 0 l-10 10 l10 10" filter="url(#feShadow)"></svg>', '<svg class="owl_button"><path d="M0 0 l10 10 l-10 10" filter="url(#feShadow)"></svg>'],
  })
})
--></script>
