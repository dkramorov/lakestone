<div class="product-quickview">
  <div class="images">
    <div class="image"><img src="<?=$images[0]['image']?>" class="img-responsive" data-popup="<?=$images[0]['popup']?>"></div>
    <div class="thumbs _owl-carousel">
      <? foreach ($images as $image) { ?>
        <img class="img-responsive" src="<?=$image['thumb']?>" data-big="<?=$image['image']?>" data-popup="<?=$image['popup']?>">
      <? } ?>
    </div>
  </div>
  <div class="info">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></span><span class="sr-only">Закрыть</span></button>
    <div class="content">
      <div class="body">
        <div class="title"><a class="black" href="<?=$href?>"><?=$name?></a></div>
        <? if ($attributes) { ?>
        <div class="attributes">
          <? foreach ($attributes as $attribute) { ?>
            <div class="attribute">
              <span><?=$attribute['name']?></span>: <span><?=$attribute['value']?></span>
            </div>
          <? } ?>
        </div>
        <? } ?>
        <? if ($price) { ?>
        <div class="price">
        <? if (!$special) { ?>
        <? echo $price; ?>
        <? } else { ?>
        <span class="price-new"><? echo $special; ?></span> <span class="price-old"><? echo $price; ?></span>
        <? } ?>
        <? if ($tax) { ?>
        <span class="price-tax"><? echo $text_tax; ?> <? echo $tax; ?></span>
        <? } ?>
        </div>
        <? } ?>
        <hr>
        <? if ($related) {?>
        <div class="color-various">
          <div class="text">
            Цвет изделия:
          </div>
          <div class="related">
            <? foreach($related as $product) { ?>
              <img src="<?=$product['image'][0]['thumb']?>" title="<?=$product['name']?>" data-product_id="<?=$product['product_id']?>"
              <? if ($product['product_id'] == $product_id) { ?>
                class="active"
              <? } ?>
              data-href="<?=$product['href']?>">
            <? } ?>
          </div>
        </div>
        <hr>
        <? } ?>
        <div class="buttons">
          <? if ($quantity > 0) { ?>
          <button type="button" class="btn btn-red" onClick="cart.add(<?=$product_id?>)">Добавить в корзину</button>
          <button type="button" class="btn blue" onClick="cart.oneclick(<?=$product_id?>)">Купить в один клик</button>
          <? } else { ?>
          <button type="button" class="btn btn-red" onClick="cart.preorder(<?=$product_id?>)">Предзаказ</button>
          <? } ?>
        </div>
        <div class="description">
          <div class="addon">Описание:</div>
          <div class="text">
            <div class="short"><?=$short?></div>
            <div class="full"><?=$full?></div>
          </div>
        </div>
      </div>
      <div class="footer">
        <a id="BtnProductFull" type="button" class="blue" onClick="fullDescription()">Полное описание товара</a>
        <a id="BtnGoProduct" type="button" class="btn btn-blue" href="<?=$href?>">Больше информации о товаре</a>
      </div>
    </div>
  </div>
</div>
<script>
<?=$javascript?>
$(document).ready(function() {
  var $q = $('.modal .product-quickview')
  setupThumbs()
  showBig($('.images .thumbs img', $q).first())
  $('.images .image', $q).on('mousemove', imgZoom)
  $('.related img', $q).on('click', function(e) {
    if ($(this).hasClass('active')) return
    showQuickview($(this).attr('data-product_id'))
  })
})
</script>
