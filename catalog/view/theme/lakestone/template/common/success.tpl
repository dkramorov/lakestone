<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <hr>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
     <div class="content_wrap">
      <h1><?php echo $heading_title; ?></h1>
      <?php
        if (
          isset($sc) and
          is_array($sc) and
          sizeof($sc) > 0
        ) { ?>
          <p>В ближайшее время наш менеджер свяжется с Вами!</p>
          <p>Вы оставили следующие контактные данные для связи -
            Телефон: <strong><?=$order_info['telephone']?></strong>
          <? if (!empty($order_info['email'])) { ?>
            E-mail: <strong><?=$order_info['email']?></strong>
          <? }?>
          <div class="warning">
            <ul class="ul-red">
              <li>Если Вы оформили заказ в ночное время, то мы перезвоним Вам на следующий день с 9:00 до 12:00 по местному времени</li>
              <li>Если Вам необходима помощь или консультация, свяжитесь с нами по телефону <a class="black" rel="nofollow" href="<?=$telephone_href?>"><strong><?=$telephone?></strong></a>. Звонок по России бесплатный</li>
            </ul>
          </div>
          <div class="order-info">
            <div class="conditions">
              <div class="heading">Информация по доставке:</div>
              <? foreach ($order_info['cond'] as $cond) { ?>
              <div class="item">
                <div class="text"><?=$cond['name']?></div>
                <div class="value"><?=$cond['text']?></div>
              </div>
              <? } ?>
            </div>
            <div class="products">
              <div class="heading">Информация по заказу:</div>
              <div class="goods">
                <? foreach ($products as $product) { ?>
                  <div class="name"><?=$product['name']?></div>
                  <div class="quantity"><?=$product['quantity']?></div>
                  <div class="total"><?=$product['total']?></div>
                <? } ?>
              </div>
              <hr>
              <div class="item">
                <div class="name"><?=$text_total_full?></div>
                <div class="total"><?=$total_full?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <? if ($Accessories) { ?>
    <div class="Accessories">
      <div class="heading">Хотите, чтобы изделие прослужило Вам не один год?</div>
      <div class="heading small">30% скидка на средства по уходу за кожей (цена указано без скидки)</div>
      <?=$Accessories?>
    </div>
    <? } ?>
    <?php echo $content_bottom; ?>
  </div>
  <div class="container-fluid grey">
    <div class="container">
      <div class="heading">Не забудьте добавить любой аксессуар со скидкой 30%</div>
      <div class="heading small red">Скидка действует только 24 часа</div>
      <div class="accessories">
        <div class="item">
          <a href="/index.php?route=product/search&search=ремень">
            <div class="image">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 61.5 27.33" width="62"><path d="M58.08,4.56H45.56V3.42A3.43,3.43,0,0,0,42.14,0H19.36a3.43,3.43,0,0,0-3.42,3.42V4.56H3.42A3.43,3.43,0,0,0,0,8V19.36a3.43,3.43,0,0,0,3.42,3.42H15.94v1.14a3.43,3.43,0,0,0,3.42,3.42H42.14a3.43,3.43,0,0,0,3.42-3.42V22.78H58.08a3.41,3.41,0,0,0,3.42-3.41h0V8a3.41,3.41,0,0,0-3.41-3.42ZM15.94,20.5H3.42a1.13,1.13,0,0,1-1.14-1.12s0,0,0,0V8A1.13,1.13,0,0,1,3.39,6.83H15.94v3.62a3.4,3.4,0,0,0,0,6.42Zm0-6.83a1.14,1.14,0,0,1,1.14-1.14h5.69a1.14,1.14,0,1,1,0,2.28H17.08A1.14,1.14,0,0,1,15.94,13.67Zm8-3.21V8H37.58V19.36H23.92V16.88a3.4,3.4,0,0,0,0-6.42ZM43.28,23.92a1.13,1.13,0,0,1-1.12,1.14H19.36a1.13,1.13,0,0,1-1.14-1.12s0,0,0,0V17.08h3.42V20.5a1.14,1.14,0,0,0,1.14,1.14H38.72a1.14,1.14,0,0,0,1.14-1.14V6.83a1.14,1.14,0,0,0-1.14-1.14H22.78a1.14,1.14,0,0,0-1.14,1.14v3.42H18.22V3.42a1.13,1.13,0,0,1,1.12-1.14h22.8a1.13,1.13,0,0,1,1.14,1.12s0,0,0,0Zm15.94-4.56a1.14,1.14,0,0,1-1.14,1.14H45.56V6.83H58.08A1.14,1.14,0,0,1,59.22,8Z"/></svg></div>
            <div class="text">Ремни</div>
          </a>
        </div>
        <div class="item">
          <a href="/portmone">
            <div class="image"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52.99 47.69" width="53"><path d="M37.76,31.11a3.31,3.31,0,1,0-3.31-3.31A3.32,3.32,0,0,0,37.76,31.11Zm0-2.65a.66.66,0,1,1,.66-.66A.66.66,0,0,1,37.76,28.46Z"/><path d="M48.36,7.95H45V1.32A1.34,1.34,0,0,0,43.72,0H5.3A5.31,5.31,0,0,0,0,5.3V43.06a4.64,4.64,0,0,0,4.64,4.64H48.36A4.64,4.64,0,0,0,53,43.06V12.59A4.64,4.64,0,0,0,48.36,7.95ZM5.3,2.65h37.1v5.3H5.3A2.62,2.62,0,0,1,2.65,5.3,2.62,2.62,0,0,1,5.3,2.65ZM2.65,9.88a5.22,5.22,0,0,0,2.65.72H48.36a2,2,0,0,1,2,2v6.62H32.46a3.32,3.32,0,0,0-3.31,3.31v10.6a3.32,3.32,0,0,0,3.31,3.31H50.34v6.62a2,2,0,0,1-2,2H4.64a2,2,0,0,1-2-2ZM31.8,22.52a.65.65,0,0,1,.66-.66H50.34V33.78H32.46a.65.65,0,0,1-.66-.66Z"/></svg></div>
            <div class="text">Портмоне</div>
          </a>
        </div>
        <div class="item">
          <a href="/kardholdery">
            <div class="image"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 59.92 42.85" width="60"><path d="M53.63,42.85H6.3A6.3,6.3,0,0,1,0,36.56V6.29A6.3,6.3,0,0,1,6.3,0H53.63a6.3,6.3,0,0,1,6.29,6.29V36.56A6.3,6.3,0,0,1,53.63,42.85ZM6.3,3A3.3,3.3,0,0,0,3,6.29V36.56A3.3,3.3,0,0,0,6.3,39.85H53.63a3.3,3.3,0,0,0,3.29-3.29V6.29A3.3,3.3,0,0,0,53.63,3Z"/><rect y="9.35" width="59.84" height="3.19"/></svg></div>
            <div class="text">Кардхолдеры</div>
          </a>
        </div>
        <div class="item">
          <a href="/oblozhki-dlya-pasporta">
            <div class="image"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43 59" width="43"><path d="M39.46,3a.54.54,0,0,1,.54.54V55.46a.54.54,0,0,1-.54.54H3.54A.54.54,0,0,1,3,55.46V3.54A.54.54,0,0,1,3.54,3H39.46m0-3H3.54A3.54,3.54,0,0,0,0,3.54V55.46A3.54,3.54,0,0,0,3.54,59H39.46A3.54,3.54,0,0,0,43,55.46V3.54A3.54,3.54,0,0,0,39.46,0Z"/><path d="M21.5,36.48a13,13,0,0,0,13-13v-.37A13,13,0,0,0,22.66,10.56a7.85,7.85,0,0,0-1.16-.06,6.7,6.7,0,0,0-1.1.06h-.06A13,13,0,0,0,8.5,23.09v.39A13,13,0,0,0,21.5,36.48Zm4.1-2.78a15,15,0,0,0,4.14-8.85c.91-.11,1.81-.24,2.71-.39A11,11,0,0,1,25.6,33.7Zm6.9-11.26c-.88.15-1.76.28-2.63.39a15.06,15.06,0,0,0-3.58-9.24A11,11,0,0,1,32.5,22.44Zm-10-9.62a13.15,13.15,0,0,1,5.32,10.24c-1.77.17-3.55.26-5.32.29Zm0,12.53c1.73,0,3.47-.11,5.2-.27a13.18,13.18,0,0,1-5.2,8.67Zm-2,8.4a13.18,13.18,0,0,1-5.2-8.67c1.73.16,3.47.24,5.2.27Zm0-20.93V23.35c-1.77,0-3.55-.12-5.32-.29A13.15,13.15,0,0,1,20.5,12.82Zm-3.74.77a15.06,15.06,0,0,0-3.58,9.24c-.87-.11-1.75-.24-2.63-.39A11,11,0,0,1,16.76,13.59Zm-3.5,11.3A15,15,0,0,0,17.4,33.7a11,11,0,0,1-6.85-9.2C11.45,24.65,12.35,24.78,13.26,24.89Z"/></svg></div>
            <div class="text">Обложки для паспорта</div>
          </a>
        </div>
        <div class="item">
          <a href="/oblozhki-dlya-avtodokumentov">
            <div class="image"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 59.75" width="36"><path d="M27.75,0H8.13A8.5,8.5,0,0,0,1,4.08l.08.05A7.14,7.14,0,0,0,0,8.18V39.32c0,4.3,2.53,9.18,5.76,11.1l14.32,8.51a5.35,5.35,0,0,0,2.72.82,3.82,3.82,0,0,0,1.91-.5c1.55-.88,2.44-2.82,2.44-5.32V47.48h.6A8.26,8.26,0,0,0,36,39.23V8.07A8.17,8.17,0,0,0,27.75,0ZM24.58,53.92c0,1.53-.43,2.69-1.14,3.09a2.13,2.13,0,0,1-2-.3L7.07,48.21c-2.44-1.45-4.5-5.52-4.5-8.89V8.18c0-1.53.43-2.69,1.14-3.09a1.3,1.3,0,0,1,.65-.16,2.9,2.9,0,0,1,1.4.46l.42.25,14,8.3,0,0,.22.13c2.31,1.59,4.21,5.47,4.21,8.7Zm8.85-14.69a5.69,5.69,0,0,1-5.68,5.68H27.2V22.73c0-4.26-2.3-8.8-5.41-10.79l-.39-.25L7.07,3.18l0,0,0,0a5.64,5.64,0,0,0-.51-.3,5.41,5.41,0,0,1,1.66-.26H27.75a5.67,5.67,0,0,1,5.68,5.5Z"/></svg></div>
            <div class="text">Обложки для автодокументов</div>
          </a>
        </div>
        <div class="item">
          <a href="/klyuchnicy">
            <div class="image"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 65.99 29.92" width="66"><path d="M56.56,11.18A3,3,0,0,0,50.91,9.6H12.84v2.75H50.68a3.06,3.06,0,0,0,2.17,1.82l-1.49,4.72a2,2,0,0,0,1.93,2.63h.42a2,2,0,0,0,1.93-2.63l-1.48-4.73A3.05,3.05,0,0,0,56.56,11.18Zm-3.05-.12a.12.12,0,1,1-.12.12A.12.12,0,0,1,53.51,11.07Z"/><path d="M54.69,0H11.3A11.3,11.3,0,0,0,0,11.3V27.12a2.8,2.8,0,0,0,2.8,2.8H63.2a2.8,2.8,0,0,0,2.8-2.8V11.3A11.3,11.3,0,0,0,54.69,0Zm8.37,27H2.94V11.3A8.38,8.38,0,0,1,11.3,2.94H54.69a8.38,8.38,0,0,1,8.37,8.37Z"/></svg></div>
            <div class="text">Ключницы</div>
          </a>
        </div>
      </div>
      <div class="information">
        <div class="heading">Что нужно, чтобы получить скидку?</div>
        <p>Если мы Вам еще не перезвонили, то просто позвоните по этому бесплатному номеру <a class="black" rel="nofollow" href="<?=$telephone_href?>"><?=$telephone?></a> и попросите менеджера добавить в Ваш заказ выбранные товары.</p>
      </div>
    </div>
  </div>
  <script><!--
  var sc_products = new Array();
  try {
    var pidl = [<?php foreach ( $sc as $p ) {echo '"' . $p['product_id'] . '",';}?>];
    /*(dataLayer = window.dataLayer || []).push({
    'goods_id': pidl,
    'goods_price': '<?php echo (int) $total; ?>',
    'event': 'pixelPurchase',
    'pixel-mg-event-non-interaction': 'False'
  });*/

    var google_tag_params = {
    ecomm_prodid: pidl,
    ecomm_pagetype: "purchase",
    ecomm_totalvalue: '<?php echo (int) $total ?>'
    };

    var _tmr = _tmr || [];
    _tmr.push({
      type: 'itemView',
      productid: pidl,
      pagetype: 'purchase',
      totalvalue: '<?php echo $total ?>'
    })

      dataLayer.push({'order_id': '<?=$order_id?>'});

    dataLayer.push({
      "ecommerce": {
          "purchase": {
              "actionField": {
                "id" : "<?php echo (int) $sc[0]['order_id'] ?>",
                "revenue": <?=(int)$total?>,
              },
              "products": [
                <?php foreach ( $sc as $p ) {
                  printf('{"brand":"Lakestone","name":"%s","id":"%s","price":"%s","quantity":"%s"},',
                    htmlspecialchars($p['name']),
                    htmlspecialchars($p['product_id']),
                    htmlspecialchars($p['price']),
                    htmlspecialchars($p['quantity'])
                  );
                }; ?>
              ]
          }
      },
      'goods_id': pidl,
      'goods_price': '<?php echo (int) $total; ?>',
      'event': 'pixel-mg-event',
      'pixel-mg-event-category': 'Enhanced Ecommerce',
      'pixel-mg-event-action': 'Purchase',
      'pixel-mg-event-non-interaction': 'False',
  })
  } catch (e) {
    ErrorLog(new MyError('an error in external code', e))
  }
  --></script>

<? } else { ?>

      <?php echo $text_message; ?>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php echo $content_bottom; ?>
     </div>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>
        <? } ?>
<?php echo $footer; ?>
