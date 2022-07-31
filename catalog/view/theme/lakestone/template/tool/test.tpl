<?php echo $header; ?>
<div id="order_placing" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <i class="fa fa-map-marker" aria-hidden="true"></i><span class="city"><?php echo $Locality ?></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="address col-sm-3">
              <?php if ( ! empty($places) ) { ?>
                <ul>
                <?php foreach ($places as $ba) { echo '<li><a role="link">' . $ba['address'] . '</a></li>'; } ?>
                </ul>
              <?php } else { ?>
                В вашем населенном пункте, к сожалению, отсутствуют пункты выдачи
                заказов.  Но, не огорчайтесь, наш менеджер подберет для вас
                оптимальный вариант доставки.  Свяжитесь с нами по телефону
                <a type="phone" title="звонок по России бесплатный" href="tel:<?php echo str_replace(array(' ','-','(',')'), '', $phone) ?>"><?php echo $phone ?></a> (звонок по России бесплатный).
              <?php } ?>
            </div>
            <div class="col-sm-9">
              <div id="map"></div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="SelectLocality1" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <i class="fa fa-map-marker" aria-hidden="true"></i><span class="city"><?php echo $Locality ?></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div>Укажите ваш регион доставки. От выбора зависят условия доставки.</div>
        <div class="search form-group">
        <div class="dropdown">
          <div class="input-group">
          <input class="form-control input-sm" id="input_locality" type="text" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" placeholder="Начните писать название города">
          <span role="button" class="input-group-addon" onClick="setLocality()"><i class="fa fa-search"></i></span>
          </div>
        </div>
        </div>
        <div class="LikesLocality">
          <div class="row">
            <div class="col-sm-12"><div class="title">Популярные города:</div></div>
            <?php
              foreach ($LikesLocality as $locality) {
                echo '<div class="col-sm-4"><a role="button" onClick="select_locality(this)">' . $locality . '</a></div>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
setTimeout(function(){
  var h = $(window).height() - 100
  var m = $('#order_placing')
  m.find('.modal-dialog').width($(window).width()-100)
  m.find('.modal-content').height(h)
  var mb = h - 81
  //m.find('.modal-header').outerHeight()
  console.log(m.find('.modal-header').outerHeight())
  m.find('.address').height(mb)
  $('#map').height(mb)
  $('#order_placing').modal('show')
}, 1000)
setTimeout(function(){
  $('#SelectLocality1').modal('show')
}, 3000)
</script>

<script>
var YInit = function() {
  console.log('start YInit')
  ymaps.ready(map_init)
}
var map_init = function() {
  console.log('start map_init')
  var u = $('#order_placing div.address ul')
  BBC = new ymaps.GeoObjectCollection({}, {
       preset: 'islands#redIcon',
       draggable: false
  })
  <?php foreach ($places as $i => $place) { ?>
      var p = new ymaps.Placemark([<?php echo $place['GPS'] ?>], {
        balloonContent: '<b><?php echo $place['address'] ?></b><p><?php echo $place['guide'] ?></p>',
      })
      BBC.add(p)
      $(u.find('li')[<?php echo $i?>]).on('click', 'a', p, function(d){
        d.data.balloon.open()
      })
  <?php } ?>


  map = new ymaps.Map("map", {
    center: [55.76, 37.64], zoom: 10
  })
  <?php if (! empty($bounds) ) { ?>
    map.setBounds([<?php echo '['. $bounds[0][0] . ',' . $bounds[0][1] . '],['. $bounds[1][0] . ',' . $bounds[1][1] . ']'?>])
  <?php } else { ?>
    ymaps.geocode("<?php echo $City; ?>").then(function(r){
      map.setCenter(r.geoObjects.get(0).geometry.getCoordinates())
    })
    
  <?php } ?>

  map.geoObjects.add(BBC)

}
</script>
<script async onload="YInit()" src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<div class="container">
  <div class="row">
    <div id="content" class="col-sm-12">
      <div class="content_wrap"></div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
