<?php echo $header; ?>
<div class="container">
  <ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $breadcrumb['href']; ?>"><span itemprop="name"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <? if (!$column_left) { ?>
  <hr>
  <? } ?>
</div>
<div class="<? echo ($column_left ? 'container-fluid' : '')?>">
  <div class="row">
    <div class="panel-grey">
      <div class="container">
        <div class="row"><?php echo $column_left; ?>
          <?php if ($column_left && $column_right) { ?>
            <?php $class = 'col-sm-6 col-md-7'; ?>
          <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-9 col-md-10'; ?>
          <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
          <?php } ?>
          <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
           <div id="googlemaps"></div>
           <div class="content_wrap">
            <div class="title"><h1><?php echo $heading_title; ?></h1></div>
            <div class="info">
              <p class="phone call_phone_3">Тел.: <a class="phone_link" rel="nofollow" href="tel:<?=$telephone_href?>"><?=$telephone?></a> (бесплатный звонок по РФ)</p>
              <p class="email" rel="nofollow">E-mail: <a class="email_link" rel="nofollow" href="mailto:<?=$admin_email?>"><?=$admin_email?></a></p>
              <hr>
              <h3 align="center">Шоу-рум</h3>
              <p><strong>Адрес:</strong> г. Москва, ул. Новодмитровская, дом 5А, стр. 2 (м. Дмитровская). Вход строго по паспорту или водительскому удостоверению.</p>
              <p><strong>Время работы:</strong> с 09:00 до 21:00, без выходных.</p>
              <p align="justify"><strong>Как добраться:</strong>
      выходите на ст. м. Дмитровская (из стеклянных дверей направо и ещё раз направо по лестнице), далее  прямо в сторону "Хлебозавода №9", проходите через территорию "Хлебозавода №9" и поворачиваете налево в сторону железнодорожного перехода, после перехода идёте прямо вдоль серого здания "Молодая гвардия", после здания поворачиваете налево и проходите 30 метров до входа в строение №2 (вывеска "ФИТНЕС КЛУБ С БАССЕЙНОМ"). На охране показываете паспорт или водительское удостоверение. Вам нужно 605 помещение на 6 этаже. </p><br>
      <div><img style="width: 100%" src="https://www.lakestone.ru/image/catalog/4.gif"></div>
            </div>
            <h3 align="center">Форма обратной связи</h3>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
              <fieldset>
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group required">
                      <label class="col-sm-4 control-label" for="input-name"><?php echo $entry_name; ?></label>
                      <div class="col-sm-8">
                        <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control" />
                        <?php if ($error_name) { ?>
                        <div class="text-danger"><?php echo $error_name; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="form-group required">
                      <label class="col-sm-4 control-label" for="input-email"><?php echo $entry_email; ?></label>
                      <div class="col-sm-8">
                        <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" />
                        <?php if ($error_email) { ?>
                        <div class="text-danger"><?php echo $error_email; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                  <?php echo $captcha; ?>
                  <div class="col-xs-12">
                    <div class="form-group required">
                      <label class="col-sm-2 control-label" for="input-enquiry"><?php echo $entry_enquiry; ?></label>
                      <div class="col-sm-10">
                        <textarea name="enquiry" rows="10" id="input-enquiry" class="form-control"><?php echo $enquiry; ?></textarea>
                        <?php if ($error_enquiry) { ?>
                        <div class="text-danger"><?php echo $error_enquiry; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              <p class="disclaimer text-right">Нажимая на кнопку <q><?php echo $button_submit; ?></q>, вы принимаете условия <a class="blue" target="_blank" href="/publichnaya-oferta">Публичной оферты</a></p>
              <div class="buttons">
                <div class="pull-right">
                  <input class="btn btn-primary" type="submit" value="<?php echo $button_submit; ?>" />
                </div>
              </div>
              </fieldset>
            </form>
            <?php echo $content_bottom; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<script>
  <?php
  $geocode   = explode(',', $geocode);
  $Latitude  = trim($geocode[0]);
  $Longitude = trim($geocode[1]);
  ?>
  var myLatLng = {lat: <?=$Latitude?>, lng: <?=$Longitude?>};

  function initMap() {
    // Create a map object and specify the DOM element for display.
    var map = new google.maps.Map(document.getElementById('googlemaps'), {
      center: myLatLng,
      scrollwheel: false,
      zoom: 15,
    controls: {
      panControl: true,
      zoomControl: true,
      mapTypeControl: true,
      scaleControl: true,
      streetViewControl: true,
      overviewMapControl: true
    },
    scrollwheel: false,
//    markers: mapMarkers,
    });
  // Create a marker and set its position.
    var marker = new google.maps.Marker({
      map: map,
      position: myLatLng,
      title: '<?=$store?>'
    });

  }
  LoadScript.push('https://maps.googleapis.com/maps/api/js?key=AIzaSyDckTkDvu1QDi1rcti-5P6vvDU_FHf2F0g&callback=initMap')
</script>
<?php echo $footer; ?>
