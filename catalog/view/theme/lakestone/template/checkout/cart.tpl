<?php echo $header; ?>
<style media="screen"></style>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <hr>
  <div class="row">
    <?php echo $column_left; ?><?php if ($column_left && $column_right) { ?><?php $class = 'col-sm-6'; ?><?php } elseif ($column_left || $column_right) { ?><?php $class = 'col-sm-9'; ?><?php } else { ?><?php $class = 'col-sm-12'; ?><?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?><h1><?php echo $heading_title; ?></h1>
      <div class="cart_table">
        <div class="title">Наименование<span class="ww">&nbsp;и цена</span></div>
        <div class="title center">Количество<span class="ww">&nbsp;товара</span></div>
        <div class="title center">Стоимость</div>
        <div class="title">&nbsp;</div>
        <? $showGiftSet = true; ?><? foreach ($products as $product) { ?><? if($product['product_id'] == GIFT_SET_ID) $showGiftSet = false; ?>
        <div class="image flex" data-cart_id="<?=$product['cart_id']?>">
          <? if ($product['thumb']) { ?>
          <div class="image">
            <a href="<?=$product['href']?>"><img src="<?=$product['thumb']?>" alt="<?=$product['name']?>" title="<?=$product['name']?>" class="img-responsive"/></a>
          </div>
          <? } ?>
        </div>
        <div class="product flex" data-cart_id="<?=$product['cart_id']?>">
          <div class="text">
            <div class="name"><?=$product['name']?></div>
            <div class="numbers">
              <span class="price"><?=$product['price']?></span> x
              <span class="quantity"><?=$product['quantity']?></span> шт.
            </div>
          </div>
        </div>
        <div class="quantity center" data-cart_id="<?=$product['cart_id']?>">
          <span class="operate operate-dec">-</span>
          <input name="quantity" value="<?=$product['quantity']?>" autocomplete="off">
          <span class="operate operate-inc">+</span>
        </div>
        <div class="total center" data-cart_id="<?=$product['cart_id']?>">
          <div class="price">
            <?=$product['total']?>
          </div>
        </div>
        <div class="operation" data-cart_id="<?=$product['cart_id']?>">
          <div class="operate">
            <span onClick="removeProduct(<?=$product['cart_id']?>)" title="удалить" class="blue-icon status-not"></span>
          </div>
        </div>
        <? } ?>
      </div>
      <? if($showGiftSet and !empty($gift_info)): ?>
      <div class="gift-set">
        <div class="gs-price">Добавьте к заказу подарочный набор<br> <b>всего за <?=$gift_info['price']?></b></div>
        <div class="gs-photos">
          <div>
            <div><img src="/image/gift1.png" alt=""></div>
            <div><img src="/image/gift2.png" alt=""></div>
            <div><img src="/image/gift3.png" alt=""></div>
            <div><img src="/image/gift4.png" alt=""></div>
          </div>
          <div>
            <div>Имиджевая коробка</div>
            <div>Фирменный пакет</div>
            <div>Стильный каталог</div>
            <div>Дисконтная карта</div>
          </div>
        </div>
        <div class="gs-arr"></div>
        <div class="gs-btns">
          <span class="add-to-order" data-gift-id="<?=GIFT_SET_ID?>">Добавить к заказу</span>
          <span class="no-need">Мне не нужно</span>
        </div>
      </div>
      <? endif; ?>
      <div class="order_form">
        <div class="contacts">
          <div class="heading">Ваши контакты:</div>
          <div class="form">
            <div class="form-group<? echo isset($errors['FullName']) ? ' has-error' : ''?>">
              <label class="sr-only">ФИО *</label>
              <input required type="text" name="FullName" class="form-control" placeholder="ФИО *" value="<?=$FullName?>"/>
            </div>
            <div class="form-group<? echo isset($errors['Phone']) ? ' has-error' : ''?>">
              <label class="sr-only">Телефон *</label>
              <input required type="tel" name="Phone" class="form-control" placeholder="Телефон *" value="<?=$Phone?>"/>
            </div>
            <div class="form-group">
              <label class="sr-only">e-Mail</label>
              <input type="email" name="EMail" class="form-control" placeholder="Контактный e-Mail" value="<?=$EMail?>"/>
            </div>
            <div class="form-group">
              <label class="sr-only">Комментарий к заказу</label>
              <textarea name="Comment" class="form-control" placeholder="Комментарий к заказу"><?=$Comment?></textarea>
            </div>
          </div>
        </div>
        <div class="conditions">
          <div class="cond_payment">
            <div class="heading">Способы оплаты заказа:</div>
            <div class="blocks">
              <div class="block link" data-type="cache">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52.99 47.69">
                  <path d="M37.76,31.11a3.31,3.31,0,1,0-3.31-3.31A3.32,3.32,0,0,0,37.76,31.11Zm0-2.65a.66.66,0,1,1,.66-.66A.66.66,0,0,1,37.76,28.46Z"/>
                  <path d="M48.36,7.95H45V1.32A1.34,1.34,0,0,0,43.72,0H5.3A5.31,5.31,0,0,0,0,5.3V43.06a4.64,4.64,0,0,0,4.64,4.64H48.36A4.64,4.64,0,0,0,53,43.06V12.59A4.64,4.64,0,0,0,48.36,7.95ZM5.3,2.65h37.1v5.3H5.3A2.62,2.62,0,0,1,2.65,5.3,2.62,2.62,0,0,1,5.3,2.65ZM2.65,9.88a5.22,5.22,0,0,0,2.65.72H48.36a2,2,0,0,1,2,2v6.62H32.46a3.32,3.32,0,0,0-3.31,3.31v10.6a3.32,3.32,0,0,0,3.31,3.31H50.34v6.62a2,2,0,0,1-2,2H4.64a2,2,0,0,1-2-2ZM31.8,22.52a.65.65,0,0,1,.66-.66H50.34V33.78H32.46a.65.65,0,0,1-.66-.66Z"/>
                </svg>
                <div class="text">Наличный расчет при получении</div>
              </div>
              <div class="block link card" data-type="card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 59.92 42.85">
                  <path d="M53.63,42.85H6.3A6.3,6.3,0,0,1,0,36.56V6.29A6.3,6.3,0,0,1,6.3,0H53.63a6.3,6.3,0,0,1,6.29,6.29V36.56A6.3,6.3,0,0,1,53.63,42.85ZM6.3,3A3.3,3.3,0,0,0,3,6.29V36.56A3.3,3.3,0,0,0,6.3,39.85H53.63a3.3,3.3,0,0,0,3.29-3.29V6.29A3.3,3.3,0,0,0,53.63,3Z"/>
                  <rect y="9.35" width="59.84" height="3.19"/>
                </svg>
                <div class="text">Банковской картой при получении</div>
              </div>
            </div>
          </div>
          <hr>
          <div class="cond_delivery">
            <div class="heading">Способы получения заказа в
              <span class="locality link" data-toggle="modal" data-target="#SelectLocality"><span class="text city"><?=$Locality?></span><svg class="svg-angle"><use xlink:href="#svg-angle"></use></svg></span>
            </div>
            <div class="subtitle">Выберите подходящий вариант:</div>
            <div class="deliverySelector"><?=$DeliveryMethods?></div>
            <div class="deliveryOption">
              <div class="pickpoint">
                <div class="form-group">
                  <div class="address">
                    <div class="hidden addPickpoint link" data-toggle="modal" data-target="#order_placing">
                      <div class="form-group">
                        <span class="plus red">+</span><span class="text">Выберите пункт самовывоза</span>
                      </div>
                    </div>
                    <div class="hidden changePickpoint">
                      <div class="subtitle">Пункт выдачи заказа:</div>
                      <div class="form-group">
                        <div class="addressPickpoint dotred"><?=$DPoint['Address'] ?? ''?></div>
                      </div>
                      <div class="link" data-toggle="modal" data-target="#order_placing" onClick="if (typeof $ds !== 'undefined') $ds.click()">
                        <span class="text">Выбрать другой адрес</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="hidden courier">
                <div class="subtitle">Укажите адрес доставки<sup class="red">*</sup>:</div>
                <div class="form-group">
                  <input class="form-control" type="text" name="address" placeholder="Введите адрес" value="<?=$CourierAddress?>">
                </div>
              </div>
              <div class="hidden post">
                <div class="subtitle">Укажите свой адрес, включая индекс<sup class="red">*</sup>:</div>
                <div class="form-group">
                  <input class="form-control" type="text" name="address" placeholder="Введите адрес" value="<?=$PostAddress?>">
                </div>
              </div>
              <div class="hidden showroom">
                <div class="subtitle">Адрес шоурума:</div>
                <div class="form-group">
                  <div class="address dotred">г. Москва, ул. Новодмитровская, дом 5А, стр. 2 (м.Дмитровская)</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="conclusion">
        <div class="left"></div>
        <div class="right">
          <? if ($coupon_on) { ?>
          <div class="coupon_info"></div>
          <div class="flex coupon">
            <div class="name"><input name="coupon_code" class="form-control" placeholder="Промокод (если есть)"></div>
            <div class="value">
              <button type="button" data-loading-text="Проверяем..." onclick="setCoupon(this)" class="btn btn-blue">Применить</button>
            </div>
          </div>
          <? } ?>
          <div class="flex2 delivery">
            <div class="name">Доставка:</div>
            <div class="value">
              <span class="text"><?=$deliveryComment?></span> <span class="price"><?=$deliveryCost?></span>
            </div>
          </div>
          <div class="flex2 amount">
            <div class="name">Товаров на сумму:</div>
            <div class="value"><span class="price"><?=$amount?></span></div>
          </div>
          <? if (isset($coupon)) { ?>
          <div class="flex2 coupon">
            <div class="name">Скидка по промокоду:</div>
            <div class="value"><span class="price"><?=$coupon?></span></div>
          </div>
          <? } ?>
          <hr>
          <div class="flex2 total">
            <div class="name">Итого к оплате:</div>
            <div class="value">
              <? if (isset($pre_total)) { ?><span class="price red"><?=$total?></span>
              <span class="price old"><?=$pre_total?></span><? } else { ?><span class="price"><?=$total?></span><? } ?>
            </div>
          </div>
          <hr>
          <button class="btn btn-red" type="button" onClick="sendOrder(this)">Оформить заказ</button>
          <div class="order_disclaimer link">Нажимая на кнопку "Оформить заказ", вы принимаете условия
            <a class="blue" target="_blank" href="/publichnaya-oferta">Публичной оферты</a>.
          </div>
        </div>
      </div>
      <?php //echo $content_bottom; ?>
    </div>
  </div>
  <?php echo $column_right; ?>
</div></div>
<div id="content_bottom" class="container-fluid">
  <div class="container">
    <?php echo $content_bottom; ?>
  </div>
</div>


<script>
    var DeliveryMethod = '<?=$deliveryCode?>'
    var PaymentMethod = '<?=$paymentCode?>'
    $(document).ready(function () {
        $('.cond_payment .blocks .block[data-type]').on('click', changePayment)
        $('#order_placing').on('setPoint', changePickpoint)
        $('#input_locality').on('change_locality', changeLocality)
        $('.cart_table .quantity .operate').on('click', operateProduct)
        $('.cart_table .quantity input[name="quantity"]').on('change', operateProduct)
        // if (Locality === 'г. Москва')
        //   $('.deliverySelector .dropdown-menu li[data-type="showroom"]').removeClass('hidden')
        $('.order_form input, .order_form textarea').on('change', function () {
            var d = {
                'var': $(this).attr('name'),
                'val': $(this).val()
            }
            $.post('index.php?route=checkout/cart/saveField', d)
        })
        $('.cond_payment .blocks .block[data-type="' + PaymentMethod + '"]').trigger('click', 1)
        // $('.deliverySelector li[data-type="' + DeliveryMethod + '"]').trigger('click', 0)
        setupDeliveryMethods(0)
        setupPickpoint()
        $('input[type="tel"]').inputmask({
            "mask": "+7(999) 999-9999"
        })
    }).on('click', '.gift-set .gs-btns .no-need,.gift-set .gs-btns .add-to-order', function () {
        if ($(this).hasClass('add-to-order')) {
            let $giftID = $(this).data('gift-id');
            let $cartID = parseInt($('.cart_table div[data-cart_id]').last().data('cart_id'));

            $cartID++;

            if ($giftID) {
                $.post('/index.php?route=checkout/cart/add', {'product_id': $giftID, 'quantity': 1}, function ($j) {
                    if ($j.success) {
                        $('#cart .badge.cart').html($j.count);
                        $('#cart .amount, .conclusion .amount .value .price,  .conclusion .total .value .price').html($j.amount);

                        if ($j.package) {
                            $('.cart_table').append('<div class="image flex" data-cart_id="' + $cartID + '"><div class="image"><a href="' + $j.prod_link + '"><img src="' + $j.package + '" alt="' + $j.name + '" title="' + $j.name + '" class="img-responsive" style="width: 79px; height: 79px"></a></div></div>');
                        } else {
                            $('.cart_table').append('<div class="image flex" data-cart_id="' + $cartID + '"></div>');
                        }

                        $('.cart_table').append('<div class="product flex" data-cart_id="' + $cartID + '"><div class="text"><div class="name">' + $j.name + '</div><div class="numbers"><span class="price">' + $j.price + ' <strong class="currency_suffix">руб.</strong></span> x <span class="quantity">1</span> шт.</div>\</div></div><div class="quantity center" data-cart_id="' + $cartID + '"><span class="operate operate-dec">-</span><input name="quantity" value="1" autocomplete="off"><span class="operate operate-inc">+</span></div><div class="total center" data-cart_id="' + $cartID + '"><div class="price">' + $j.price + ' <strong class="currency_suffix">руб.</strong></div></div><div class="operation" data-cart_id="' + $cartID + '"><div class="operate"><span onclick="removeProduct(' + $cartID + ')" title="удалить" class="blue-icon status-not"></span></div></div>');
                    }
                }).fail(function ($err) {
                    console.log($err);
                })
            }
        }

        $('.gift-set').css({'height': '0', 'opacity': '0', 'margin': '0', 'overflow': 'hidden'});
        setTimeout(function () {
            $('.gift-set').remove();
        }, 500);
    })
    var changePayment = function (e, d) {
        var $t = $(e.currentTarget)
        var $bb = $('.cond_payment .blocks .block')
        var pm = $t.attr('data-type')
        if (!d && PaymentMethod === pm) return
        PaymentMethod = pm
        $bb.removeClass('active')
        $t.addClass('active')
        if (d !== 0)
            $.get('index.php?route=checkout/cart/setPayment&method=' + pm, loadTotals)
    }
    var changeDelivery = function (e, d) {
        var $t = $(e.currentTarget)
        var $b = $('.deliverySelector button.dropdown-toggle')
        var dm = $t.attr('data-type')
        $('.text', $b).text($t.text())
        if (!d && DeliveryMethod === dm) return
        console.log('ss');
        DeliveryMethod = dm
        $('.deliveryOption > div').addClass('hidden')
        $('.deliveryOption > div.' + dm).removeClass('hidden')
        if (d !== 0)
            $.get('index.php?route=checkout/cart/setShipping&method=' + dm, loadTotals)
    }
    var changePickpoint = function (e, d) {
        var $d = $('.deliveryOption .pickpoint .address')
        $('.addPickpoint', $d).addClass('hidden')
        $('.addressPickpoint').text(d.addr)
        $('#order_placing').modal('hide')
        setupPickpoint()
    }
    var changeLocality = function (e, d) {
        $('.cond_delivery .locality .city').text(d)
        // if (d === 'г. Москва')
        //   $('.deliverySelector .dropdown-menu li[data-type="showroom"]').removeClass('hidden')
        // else
        //   $('.deliverySelector .dropdown-menu li[data-type="showroom"]').addClass('hidden')
        // $('.deliverySelector .dropdown-menu li[data-type="pickpoint"]').click()
        $('.deliverySelector').load('index.php?route=checkout/cart/loadDeliveryMethods', function () {
            setupDeliveryMethods()
            setupPickpoint()
        })
    }
    var setupDeliveryMethods = function () {
        var $s = $('.deliverySelector .dropdown-menu')
        $('li[data-type]', $s).on('click', changeDelivery)
        if ($('li[data-type="' + DeliveryMethod + '"]', $s).length > 0)
            $('li[data-type="' + DeliveryMethod + '"]', $s).trigger('click', 1)
        else
            $('li', $s).first().trigger('click', 1)
    }
    var setupPickpoint = function () {
        var $d = $('.deliveryOption .pickpoint .address')
        $('.changePickpoint, .addPickpoint', $d).addClass('hidden')
        if (DPoint) $('.changePickpoint', $d).removeClass('hidden')
        else $('.addPickpoint', $d).removeClass('hidden')
    }
    var loadTotals = function () {
        var $c = $('.conclusion')
        $.get('index.php?route=checkout/cart/loadTotals', function (d) {
            $('.delivery .text', $c).html(d['deliveryComment'])
            $('.delivery .price', $c).html(d['deliveryCost'])
            $('.amount .price', $c).html(d['amount'])
            if (d['pre_total']) {
                $('.total .price.red', $c).html(d['total'])
                $('.total .price.old', $c).html(d['pre_total'])
            } else {
                $('.total .price', $c).html(d['total'])
            }
        })
    }
    const setCoupon = function (t) {
        let $P = $(t).parents('.right');
        let $ci = $('.coupon_info', $P);
        let $p = $(t).parents('.coupon');
        let $i = $('input[name="coupon_code"]', $p); //coupon_code
        if ($i.val()) {
            $(t).button('loading');
            $.get('index.php?route=checkout/cart/setCoupon', {'code': $i.val()}, function (d) {
                $(t).button('reset');
                if (d.status == 'OK') {
                    //loadTotals();
                    $ci.text(null)
                    location.reload();
                } else {
                    $i.val(null)
                    $ci.text(d.error)
                }
            })
        }
    }
    var removeProduct = function (id) {
        $('.cart_table *[data-cart_id="' + id + '"]').remove()
        cart.remove(id)
        loadTotals()
    }
    var operateProduct = function (e) {
        var $t = $(e.currentTarget)
        var $p = $t.parents('.quantity')
        var id = $p.attr('data-cart_id')
        var $ct = $('.cart_table')
        var $i = $('input', $p)
        var vn
        var v = parseInt($i.val())
        if (e.type === 'change') nv = v
        else if ($t.hasClass('operate-inc')) nv = v + 1
        else nv = v - 1
        if (nv < 0) return
        $i.val(nv)
        cart.update(id, nv, null, function (d) {
            $('.total[data-cart_id=' + id + '] .price', $ct).html(d['products'][id]['total'])
            $('.product[data-cart_id=' + id + '] .numbers .quantity', $ct).text(nv)
            loadTotals()
        })
    }
    var showError = function (t, m) {
        var $e = $('#error-message')
        $('.modal-header .title').text(t)
        $('.modal-body .text').text(m)
        $e.modal('show')
    }
    var sendOrder = function (target) {
        var err = false
        var data = {
            'Locality': Locality
        }
        var $f = $('.order_form .contacts')
        var $do = $('.deliveryOption')
        var $phone = $('.form input[name="Phone"]')
        $('.has-error').removeClass('has-error')
        $('input[required]', $f).each(function () {
            if (!$(this).val()) {
                err = true
                $(this).parent().addClass('has-error')
            }
        })
        if (!$phone.inputmask('isComplete')) {
            err = true
            $phone.parent().addClass('has-error')
        }
        if (err) {
            showError('Заполните форму', 'Пожалуйста, заполните свои контактные данные в форме заказа')
            return
        }
        $('input, textarea', $f).each(function () {
            var n = $(this).attr('name')
            data[n] = $(this).val()
        })
        data['DeliveryMethod'] = DeliveryMethod
        data['PaymentMethod'] = PaymentMethod
        switch (DeliveryMethod) {
            case 'pickpoint':
                if (!DPoint) {
                    $('.pickpoint .addPickpoint .form-group', $do).addClass('has-error')
                    showError('Выберите пункт выдачи заказов', 'Пожалуйста, выберите наиболее удобный пункт выдачи заказов в форме настройки способа получения заказа')
                    return
                } else {
                    data['address'] = $('.addressPickpoint', $do).text()
                    data['pickpoint'] = DPoint
                }
                break
            case 'post':
                if (!$('.post input', $do).val()) {
                    $('.post .form-group', $do).addClass('has-error')
                    showError('Укажите полный адрес доставки', 'Пожалуйста, укажите наиболее полный адрес для доставки заказа в форме настройки способа получения заказа')
                    return
                } else {
                    data['address'] = $('.post input', $do).val()
                }
                break
            case 'courier':
                if (!$('.courier input', $do).val()) {
                    $('.courier .form-group', $do).addClass('has-error')
                    showError('Укажите полный адрес доставки', 'Пожалуйста, укажите наиболее полный адрес для доставки заказа в форме настройки способа получения заказа')
                    return
                } else {
                    data['address'] = $('.courier input', $do).val()
                }
                break
            case 'showroom':
                break
            default:
                showError('Неизвестный метод доставки', 'Похоже, у нас на сайте что-то пошло не так. Пожалуйста, позвоните менеджеру по бесплатному телефону <?=$telephone?>, расскажите о ситуации и сделайте заказ по телефону')
                return
        }
        $(target)
            .prop('disabled', true)
            .addClass('disabled');
        $.post('index.php?route=checkout/order', data, function (d) {
            $(target)
                .prop('disabled', false)
                .removeClass('disabled');
            if (d.success) {
                location = d.success.redirect
            } else {
                showError('Внимание! Заказ не создан!', 'К сожалению, у нас на сайте что-то пошло не так. Пожалуйста, позвоните менеджеру по бесплатному телефону <?=$telephone?>, расскажите о ситуации и сделайте заказ по телефону')
                ErrorLog('Make order error: ' + d.error)
            }
        })
    }
</script>

<!--<?/*
     <div class="content_wrap checkout-cart">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-center"><?php echo $column_image; ?></td>
                <td class="text-left"><?php echo $column_name; ?></td>
                <td class="text-left"><?php echo $column_price; ?></td>
                <td class="text-left"><?php echo $column_quantity; ?></td>
                <td class="text-left"><?php echo $column_total; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product) { ?>
              <tr data-cart_id="<?=$product['cart_id']?>">
                <td class="text-center"><?php if ($product['thumb']) { ?>
                  <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
                  <?php } ?></td>
                <td class="text-left"><a class="black" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                  <?php if (!$product['stock']) { ?>
                  <span class="text-danger">***</span>
                  <?php } ?>
                  <?php if ($product['option']) { ?>
                  <?php foreach ($product['option'] as $option) { ?>
                  <br />
                  <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                  <?php } ?>
                  <?php } ?>
                  <?php if ($product['reward']) { ?>
                  <br />
                  <small><?php echo $product['reward']; ?></small>
                  <?php } ?>
                  <?php if ($product['recurring']) { ?>
                  <br />
                  <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
                  <?php } ?></td>
                <td class="text-left"><?php echo $product['price']; ?></td>
                <td class="text-left"><div class="input-group btn-block" style="max-width: 200px;">
                    <input type="text" name="quantity[<?php echo $product['cart_id']; ?>]" value="<?php echo $product['quantity']; ?>" size="1" class="form-control" />
                    <span class="input-group-btn">
                    <button name="button_refresh" onClick="cartRefresh(this)" type="button" title="<?php echo $button_update; ?>" class="btn "><svg class="svg-refresh"><use xlink:href="#svg-refresh"></svg></button>
                    <button name="button_remove" id="button_remove" type="button" title="<?php echo $button_remove; ?>" class="btn " onclick="cart.remove('<?=$product['cart_id']?>', '/cart');ec_remove('<?php echo $product['product_id'] ?>', '<?php echo $product['name'] ?>');"><svg class="svg-remove"><use xlink:href="#svg-remove"></svg></button></span></div></td>
                <td class="text-left orange"><?php echo $product['total']; ?></td>
              </tr>
              <?php } ?>
              <?php foreach ($vouchers as $voucher) { ?>
              <tr>
                <td></td>
                <td class="text-left"><?php echo $voucher['description']; ?></td>
                <td class="text-left"></td>
                <td class="text-left"><div class="input-group btn-block" style="max-width: 200px;">
                    <input type="text" name="" value="1" size="1" disabled="disabled" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger" onclick="voucher.remove('<?php echo $voucher['key']; ?>');"><i class="fa fa-times-circle"></i></button>
                    </span></div></td>
                <td class="text-right"><?php echo $voucher['amount']; ?></td>
                <td class="text-right"><?php echo $voucher['amount']; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php if ($modules) { ?>
      <h2><?php echo $text_next; ?></h2>
      <p><?php echo $text_next_choice; ?></p>
      <div class="panel-group" id="accordion">
        <?php foreach ($modules as $module) { ?>
        <?php echo $module; ?>
        <?php } ?>
      </div>
      <?php } ?>
      <br />
      <div class="row order-form">
        <div class="col-sm-4">
          <div class="head">ОПЛАТА</div>
          <hr>
          <div class="form-group">
            <label><input type="radio" name="PayWay" value="1" <? echo ($PayWay == 1 or empty($PayWay) )?'checked="checked"':''?>> Наличными курьеру</label>
          </div>
          <div class="form-group">
            <label><input type="radio" name="PayWay" value="2" <? echo ($PayWay == 2)?'checked="checked"':''?>> Банковской картой курьеру</label>
          </div>
          <hr>
          <div class="">
            <p><b>Бесплатная доставка по всей России и Казахстану. Оплата при получении заказа.</b></p>
          </div>
          <div class="">
            <p><b>Заполните все поля и нажмите "Оформить заказ".</b></p>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="head delivery">ДОСТАВКА
                  <?php if (!empty($DPoint['CityName'])) { ?>
                          <a class="tr2orange grey" title="Посмотреть и выбрать Пункт Выдачи Заказа" role="button" data-toggle="modal" data-target="#SelectLocality">в <?php echo $DPoint['CityName']; ?> <span class="glyphicon glyphicon-triangle-bottom"></span></a>
                  <?php } ?>
          </div>
          <hr>
          <div class="form-group<? echo isset($errors['FullName']) ? ' has-error' : ''?>">
            <label class="sr-only">ФИО *</label>
            <input required type="text" name="FullName" class="form-control" placeholder="ФИО *" value="<? echo $FullName; ?>"/>
          </div>
          <div class="form-group<? echo isset($errors['Phone']) ? ' has-error' : ''?>">
            <label class="sr-only">Телефон *</label>
            <input required type="text" name="Phone" class="form-control" placeholder="Телефон *" value="<? echo $Phone; ?>"/>
          </div>
          <div class="delivery_method">
            <ul class="nav nav-tabs<?php if (empty($DPoint['CityName'])) echo ' hidden'; ?>" role="tablist">
              <li role="presentation"<?php if (empty($DPoint['CityName']))  echo ' class="active"'; ?>>
                 <a class="black tr2orange" href="#tab_devivery_address" aria-controls="tab_delivery_addres" role="tab" data-toggle="tab">
                   <span class="hidden-sm">Адрес доставки</span><span class="visible-sm"><span title="Адрес доставки" class="glyphicon glyphicon-home"></span></span>
                 </a>
              </li>
              <li role="presentation"<?php if (!empty($DPoint['CityName'])) echo ' class="active"'; ?>>
                 <a class="black tr2orange" href="#tab_dpoint_address" aria-controls="tab_dpoint_addres" role="tab" data-toggle="tab">
                   <span class="hidden-sm">Адрес пункта выдачи</span><span class="visible-sm"><span title="Адрес пункта выдачи" class="glyphicon glyphicon-share"><span></span>
                 </a>
              </li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane<?php if (empty($DPoint['CityName'])) echo ' active'; ?>" id="tab_devivery_address">
                 <div class="form-group<? echo isset($errors['Address']) ? ' has-error' : ''?>">
                   <label class="sr-only">Адрес доставки *</label>
                   <input<?php if (empty($DPoint['CityName'])) echo ' required'; ?> type="text" name="Address" class="form-control" placeholder="Адрес доставки *" value="<? echo $Address; ?>"/>
                 </div>
              </div>
              <div role="tabpanel" class="tab-pane<?php if (!empty($DPoint['CityName'])) echo ' active'; ?>" id="tab_dpoint_address">
                 <div class="form-group<? echo isset($errors['DPointAddress']) ? ' has-error' : ''?>">
                   <label class="sr-only">Адрес самовывоза *</label>
                   <div class="input-group">
                     <input<?php if (!empty($DPoint['CityName'])) echo ' required'; ?> type="text" readonly name="DPointAddress" class="form-control" placeholder="Пункт самовывоза не выбран!" value="<? if (!empty($DPoint['Address'])) echo $DPoint['Address']; ?>"/>
                   <div class="input-group-addon">
                     <a title="Выбрать точку самовывоза" class="tr2orange grey" onClick="if (typeof $ds !== 'undefined') $ds.click()" role="button" data-toggle="modal" data-target="#order_placing"><span class="glyphicon glyphicon-screenshot"></span></a>
                   </div>
                   </div>
                 </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="sr-only">e-Mail</label>
            <input type="email" name="EMail" class="form-control" placeholder="Контактный e-Mail" value="<? echo $EMail; ?>"/>
          </div>
          <div class="form-group">
            <label class="sr-only">Комментарий к заказу</label>
            <textarea name="Comment" class="form-control" placeholder="Комментарий к заказу"><? echo $Comment; ?></textarea>
          </div>
        </div>
        <div class="col-sm-4 total">
          <div class="head">ИТОГ</div>
          <hr>
          <div class="row">
            <div class="col-xs-6">Товаров</div>
            <div class="col-xs-6 text-right"><?php echo $full_count; ?></div>
          </div>
          <hr class="grey">
          <div class="row">
            <div class="col-xs-6">Сумма</div>
            <div class="col-xs-6 text-right orange"><?php echo $full_price; ?></div>
          </div>
          <div class="row">
            <div class="col-xs-12">
            <div class="form-group">
              <label class="sr-only">Оформить заказ</label>
              <button name="order" class="btn btn-black form-control">Оформить заказ</button>
              <p class="disclaimer text-justify">Нажимая на кнопку <q>Оформить заказ</q>, я даю согласие на <a target="_blank" href="/personal">обработку персональных данных</a>.</p>
            </div>
            </div>
          </div>
        </div>
      </div>
      </form>
      <?php echo $content_bottom; ?>
     </div>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>
<script type="text/javascript"><!--
<?php if ( !empty($error_message) ) { ?>
WindowLoad.push(function() {
        var m = $('#error-message')
        m.find('.title').text('Ошибка при формировании заказа')
        m.find('.text').html('<?=$error_message?>')
        $(window).scrollTop( $('.order-form').position()['top'])
        m.modal('show')
})
<?php } ?>
var cartRefresh = function(t) {
  var $tr = $(t).parents('tr')
  var cid = $tr.attr('data-cart_id')
  var q = $tr.find('input[name^=quantity]').val()
  console.log(cid, q)
  cart.update(cid, q, '/cart')
}
var ec_remove = function(id, name) {
        try {
                dataLayer.push({
                    "ecommerce": {
                        "remove": {
                            "products": [
                                {
                                    "id": id,
                                    "name": name
                                }
                            ]
                        }
                    }
                })
        } catch(e) {
                ErrorLog(e)
        }
}
try {
        var pidl = [<?php foreach ($products as $product) {echo '"' . $product['product_id'] . '",';} ?>];
        var fp = <?php echo (int)$full_price ?>;
        (dataLayer = window.dataLayer || []).push({
                'goods_id': pidl,
                'goods_price': fp.toString(),
                'event': 'pixelInitiateCheckout',
                'pixel-mg-event-non-interaction': 'False'
        });
        var google_tag_params = {
                ecomm_prodid: pidl,
                ecomm_pagetype: "cart",
                ecomm_totalvalue: fp
        };
        var _tmr = _tmr || [];
        window._tmr.push({
                type: 'itemView',
                productid: pidl,
                pagetype: 'cart',
                totalvalue: fp.toString()
        })
} catch (e) {
        ErrorLog(new MyError('an error in external code', e))
}
var checkForm = function(e) {
        //
        return true
        //
        var res = true
        $('*[required]').each(function(i, f) {
                $(f).parent().removeClass('has-error')
                if (!$(f).val()) {
                        res = false
                        $(f).parent().addClass('has-error')
                }
        })
        if (!res) {
                e.preventDefault()
                return false
        }
}
DocumentReady.push(function(){
        $('a[href="#tab_devivery_address"]').on('show.bs.tab', function(e) {
                $('input[name="DPointAddress"]').prop('required', false).parent().removeClass('has-error')
                $('input[name="Address"]').prop('required', true)
        })
        $('a[href="#tab_dpoint_address"]').on('show.bs.tab', function(e) {
                $('input[name="DPointAddress"]').prop('required', true)
                $('input[name="Address"]').prop('required', false).parent().removeClass('has-error')
        })
        $('button[name=order]').on('click', function(e) {
                try {
                        if (!checkForm(e))
                                return
                        yaCounter40400645.reachGoal('button_zakaz')
                        ga('send', 'event', 'click', 'button_zakaz')
                } catch (e) {
                        ErrorLog(new MyError('an error in external code at click@order', e))
                }
        })
})
var ga_loader = function(t,p) {
        try {
                ga('send', 'event', 'thx_for_order', document.URL);
                yaCounter40400645.reachGoal('thx_for_order');

                var tr = {
                        'id': t['id'],
                        'affiliation': 'Gianfranco Bonaventura',
                        'revenue': t['to'],
                        'currency': 'RUB'
                };
                ga('ecommerce:addTransaction', tr);
                $(p).each(function(){
                        var a = {
                                'id': t['id'],
                                'name': this.name,
                                'sku': 'SKU' + this.product_id,
                                'category': 'all',
                                'price': this.price,
                                'quantity': this.quantity
                        };
                        ga('ecommerce:addItem', a);
                });
                ga('ecommerce:send');
        } catch(e) {
                ErrorLog(e)
        }
};
--></script>*/?>--><?php echo $footer; ?>
