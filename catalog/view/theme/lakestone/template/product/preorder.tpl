<div class="oneclick_order">
  <div class="product_purchase modal-default">
    <div class="image">
      <img src="<?=$images[0]['image']?>" alt="<?=$name?>" title="<?=$name?>" class="img-responsive">
    </div>
    <hr>
    <div class="title"><?=$name?></div>
    <div class="customers_form">
      <input name="product_id" type="hidden" value="<?=$product_id?>">
      <div class="form-group">
        <input name="username" class="form-control" placeholder="Ваше имя">
      </div>
      <div class="form-group">
        <input name="phone" class="form-control" placeholder="Номер телефон">
      </div>
      <div class="buttons">
        <button onClick="oneclick_send(this)" class="btn btn-red">Оформить предзаказ</button>
        <button class="btn btn-default" data-dismiss="modal" type="button">Отмена</button>
      </div>
    </div>
    <p class="disclaimer text-justify">Нажимая на кнопку <q>Оформить предзаказ</q>, я даю согласие на <a class="blue" target="_blank" href="/personal">обработку персональных данных</a></p>
  </div>
</div>
<script>
var oneclick_send = function(t) {
  try {
    var w = $(t).parents('.oneclick_order')
    var n = w.find('input[name="username"]')
    var p = w.find('input[name="phone"]')
    var np = n.parent()
    var pp = p.parent()
    var product_id = w.find('input[name="product_id"]').val()
    var ok = true
    var cf = $('.customers_form')
    np.removeClass('has-error')
    pp.removeClass('has-error')
    if ( np.length == 0 || pp.length == 0 )
      throw new Error('object not found')
  } catch(e) {
    ErrorLog(new MyError('an error by find objects at click@oneclick_send', e))
  }

  if ( typeof n.val != 'function' || ! n.val() ) {
    np.addClass('has-error')
    ok = false
  }

  if ( typeof p.val != 'function' || ! p.val() ) {
    pp.addClass('has-error')
    ok = false
  }

  if ( ok ) {
    console.log(t);
    $(t).prop('disabled', true)
    .text('отправляем ваш заказ...')
    var res = $.ajax({
      type: 'post',
      url: '/index.php?route=checkout/preorder',
      data: {
        'product_id': product_id,
        'FullName':n.val(),
        'Phone':p.val(),
      },
      cache: false,
      success: function(d) {
        console.log(d)
        try {
          DataLayerPush_preorder(d.order_id)
        } catch (e) {
          ErrorLog(new MyError('an error in preorder_send', e))
        }
        cf.empty()
        cf.append('<div class="text-success">' + d['text'] + '</div>')
      },
      error: function(d) {
        console.log(d)
        cf.empty()
        cf.append('<div class="text-danger">' + d['error'] + '</div>')
      },
      complete: function() {
        cf.append('<div class="buttons b1"><button class="btn btn-default" type="link" data-dismiss="modal"><span aria-hidden="true">Закрыть</span></button></div>')
      }
    })
  } else {
    var m = $('#error-message')
    m.find('.title').text('Ошибка при формировании заказа')
    m.find('.text').html('Пожалуйста, заполните все обязательные поля. Незаполненные поля выделены красным.')
    $(window).scrollTop( np.position()['top'])
    m.modal('show')
  }
}
var DataLayerPush_preorder = function(order_id) {
  (dataLayer = window.dataLayer || []).push({
    'goods_id': '<?=$product_id?>',
    'goods_price': '<?=(int)$price?>',
    'event': 'pixel-mg-event',
    'pixel-mg-event-category': 'Enhanced Ecommerce',
    'pixel-mg-event-action': 'Purchase',
    'pixel-mg-event-non-interaction': 'False',
    "ecommerce": {
      'currencyCode': 'RUB',
      "purchase": {
        "actionField": {
          'id': order_id,
          'revenue': "<?=(int)$price?>",
        },
        "products": [{
          "id": "<?=$product_id?>",
          "name" : "<?=$name?>",
          "price": "<?=(int)$price?>",
          "brand": "Lakestone",
          "category": "<?=$category?>",
          "quantity": 1
        }]
      }
    }
  });
}

</script>
