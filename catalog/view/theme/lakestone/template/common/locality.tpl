<div id="order_placing" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<div class="row">
      	  <div class="col-sm-3">
            <svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"></svg><span class="city"><?php echo $Locality ?></span>
            <button type="button" class="visible-xs close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></span></button>
          </div>
          <div class="col-sm-9">
            <span class="text"><?php echo $delivery_advanced; ?></span>
            <button type="button" class="hidden-xs close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></span></button>
          </div>
        </div>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-sm-3 col-xs-12">
	      <div class="title"><h3>Пункты выдачи заказов:</h3></div>
	      <div class="address">
              <?php if ( ! empty($places) ) { ?>
                <ul>
                <?php foreach ($places as $ba) { echo '<li><a>' . $ba['address'] . '</a></li>'; } ?>
                </ul>
              <?php } else { ?>
                В вашем населенном пункте, к сожалению, отсутствуют пункты выдачи
                заказов.  Но, не огорчайтесь, наш менеджер подберет для вас
                оптимальный вариант доставки.  Свяжитесь с нами по телефону
                <a type="phone" title="звонок по России бесплатный" href="tel:<?php echo str_replace(array(' ','-','(',')'), '', $phone) ?>"><?php echo $phone ?></a> (звонок по России бесплатный).
              <?php } ?>
              </div>
            </div>
            <div class="col-sm-9 col-xs-12">
              <div id="map"></div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="SelectLocality" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"></svg><span class="city"><?php echo $Locality ?></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></span></button>
      </div>
      <div class="modal-body">
        <div>Укажите ваш регион доставки. От выбора зависят условия доставки.</div>
        <div class="search form-group">
          <div class="dropdown">
           <div class="input-group">
           <input class="form-control input-sm" id="input_locality" type="text" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" placeholder="Начните писать название города">
           <?php /*<span role="button" class="input-group-addon" onClick="setLocality()"><i class="fa fa-search"></i></span>*/?>
           </div>
          </div>
        </div>
        <div class="LikesLocality">
          <div class="row">
            <div class="col-sm-12"><div class="title">Популярные города:</div></div>
            <?php
              foreach ($LikesLocality as $locality) {
                echo '<div class="col-sm-4"><a class="priority_' . $locality['Priority'] . '" role="button" onClick="select_locality(this)">' . $locality['Locality'] . '</a></div>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>

var order_placing_setup = function() {
  var w = $(window).width()
  var m = $('#order_placing')
  var b = 0.9
  if ( w <= 767 ) b = 0.98
  var h = $(window).innerHeight() * b
  m.find('.modal-dialog').width(w * b)
  m.find('.modal-content').height(h)
  var mh = m.find('.modal-header').outerHeight()
  var th = m.find('.modal-body .title').outerHeight()
  if ( mh == 0 ) mh = 51
  if ( th == 0 ) th = 23
  var mb = h - mh - 30
  if ( w <= 767 ) {
  	m.find('.modal-body .address').height(mb * 0.4 - th)
  	$('#map').height(mb * 0.6)
  } else {
  	m.find('.modal-body .address').height(mb - th)
  	$('#map').height(mb)
  }
}

DocumentReady.push(function(){
	$('#order_placing')
	   .on('show.bs.modal', function(){
		try {
			order_placing_setup()
		} catch(e) {
			ErrorLog(e)
		}
	   })
	   .on('shown.bs.modal', function(){
		checkStartUp(['map.getBounds'], function() {
			try {
				order_placing_setup()
				map_setup()
			} catch (e) {
				ErrorLog(e)
			}
                })
	   })
	<?php if ($open_locality) { ?>
		$('#order_placing').modal('show')
	<?php } ?>
})

var map_setup = function() {
  <?php if (! empty($bounds) ) { ?>
    var c = [<?php echo '['. $bounds[0][0] . ',' . $bounds[0][1] . '],['. $bounds[1][0] . ',' . $bounds[1][1] . ']'?>]
    map.setBounds(c)
    	.then(function() {
    		console.log('setBounds done: ', [<?php echo '['. $bounds[0][0] . ',' . $bounds[0][1] . '],['. $bounds[1][0] . ',' . $bounds[1][1] . ']'?>])
    	}, function(e) {
    		console.error('setBounds done', e)
    	})
    map.zoomRange.get().then(function (r) {
    	if ( map.getZoom() > r[1] )
        	map.setZoom(r[1])
    })
  <?php } else { ?>
    ymaps.geocode("<?php echo $City; ?>").then(function(r){
      map.setCenter(r.geoObjects.get(0).geometry.getCoordinates())
    })
  <?php } ?>
}

var YInit = function() {
  try {
    ymaps.ready(map_init)
  } catch (e) {
  	ErrorLog(e)
  }
}
var map_init = function() {
 try {
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


  <?php if (! empty($bounds) ) { ?>
  map = new ymaps.Map("map", {
    bounds: [<?php echo '['. $bounds[0][0] . ',' . $bounds[0][1] . '],['. $bounds[1][0] . ',' . $bounds[1][1] . ']'?>],
    controls: ['zoomControl', 'geolocationControl']
  })
  map.geoObjects.add(BBC)
  <?php } ?>
 } catch (e) {
 	ErrorLog(new MyError('yandex.map: map_init', e))
 }
}
var setLocality = function() {
	var t = $('#input_locality').val()
	if (t) {
		$.get('/index.php?route=common/locality/setLocality', {'name':t})
		$('#SelectLocality .city').text(t)
		$('#up-nav .city').text(t)
		$('#SelectLocality').modal('hide')
		var p
		if (typeof window.localities[t] == 'undefined')
			l = window.localities['default']
		else
			l = window.localities[t]
		$('#menu .phone a')
			.text(l['phone'])
			.attr('href','tel:' + l['phone'].replace(/[ ()-]/g,''))
		setTimeout(TopFix, 500)
	}
}
var select_locality = function(t) {
	$('#input_locality').val($(t).text()).focus()
	$('.dropdown-menu').parent().removeClass('open')
	////////
	$.get('/index.php?route=common/locality/setLocality', {'name':$(t).text()}, function(){
		location.reload()
	})

}
var dropdown_locality = function() {
	var t = $('#input_locality').val()
	$('#SelectLocality .dropdown-menu').remove()
	if (t.length == 0)
		return
	$.post(
		'/index.php?route=common/locality/search',
		{'name':t},
		function(d){
			var p = $('#input_locality').parent()
			if (d.result.length > 0) {
				var ul = $('<ul class="dropdown-menu" aria-labelledby="input_locality"></ul>')
				$(d.result).each(function(){
					var re = new RegExp(t,"i")
					var n = this.replace(re, '<span class="selected">$&</span>')
					var l = '<li><a role="button" onClick="select_locality(this)">' + n + '</a></li>'
					ul.append(l)
				})
				$('#input_locality').after(ul)
				if ( ! p.hasClass('open') )
					p.addClass('open')
			} else {
				p.removeClass('open')
			}
		}
	)
}
//LoadScript.push('https://api-maps.yandex.ru/2.1/?lang=ru_RU')
StartUp.push(function () {
	$.getScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU')
	.done(YInit)
})
</script>
<!--
<script async onload="YInit()" src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
-->
