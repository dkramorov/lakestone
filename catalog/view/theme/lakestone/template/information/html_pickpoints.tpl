<div class="PickPoints">
  <h5>Адреса пунктов самовывоза LAKESTONE</h5>
  <div class="city-selector">
    <div class="pad">.</div>
    <div class="selector">
      <div class="head">Ваш город:</div>
      <div class="city">
        <span class="city_name"><?=$Locality?></span>
        <a class="blue" onClick="selectCity()">Изменить</a>
      </div>
    </div>
    <div class="description">
      У вас есть возможность бесплатно заказать товар на один из более чем 2000 пунктов выдачи заказов и забрать его там, предварительно осмотрев товар перед покупкой!
    </div>
  </div>
  <div class="map-navigator">
    <div class="flex">
      <div class="pick_point">
        <div class="pp_title">Пункты самовывоза</div>
        <div class="ListPoints"></div>
      </div>
      <div class="map-canvas">
        <div class="map"></div>
        <div class="cover">
          <div class="message"></div>
          <svg class="spinner"><use xlink:href="#svg-spinner"/></svg>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
  window.ListPoints = $('.map-navigator .pick_point .ListPoints')
  var $m = $('.PickPoints .map-canvas')
  $('svg.spinner', $m).css('top', $m.height()/2)

  $('#input_locality').on('change_locality', function(e, d) {
    if (Locality !== CurLocality) {
      CurLocality = Locality
      $('.city-selector .city_name').text(Locality)
      loadLocality()
    }
  })

  var $pp = $('#information-delivery .map-navigator .pick_point')
  $pp.find('.ListPoints').height(
    $pp.height() - $pp.find('.title').outerHeight()
  )
  CurLocality = Locality
})
var selectCity = function() {
  $('#SelectLocality').modal('show')
}
loadRMap(function() {
  if (typeof dmap === 'undefined') {
    dmap = RMap.initMap($('.map-navigator .map'))
  }
  dmap.callback = {
    infowindow_content: function(place) {
      var b
      if (DPoint === place.Provider + '.' + place.ID) {
        b = '<button data-provider="' + place.Provider  + '" data-id="' + place.ID  + '" onClick="setPoint(this)" disabled="yes" class="btn btn-primary setPlace">Выбран</button>'
      } else {
        b = '<button data-provider="' + place.Provider  + '" data-id="' + place.ID  + '" onClick="setPoint(this)" class="btn btn-primary setPlace">Выбрать этот пункт</button>'
      }
      return '<div><strong class="address">' + place.address + '</strong><p class="gmap_infowindow_guide">' + place.guide + '</p><div class="text-center">' + b + '</div></div>'
    },
    unblockMap: function () {
      var dom = dmap.dom
      var $p = $(dom).parents('.map-canvas')
      var $c = $p.find('.cover')
      if (!$c.hasClass('error')) {
        $(dom).css('opacity', 1)
        $c.css('display', 'none')
      }
    },
    errorMap: function () {
      messageMap('Не удалось загрузить карты', dmap)
    }
  }
  loadLocality()
})
var loadLocality = function() {
  $.getJSON('index.php?route=common/locality/getPlaces&locality=' + Locality, function (d) {
    if (typeof d.places === 'undefined' || !d.places) {
      messageMap('К сожалению, в этом населенном пункте нет точек выдачи заказа')
      return
    }
    ListPoints.empty()
    RMap.setBounds(dmap, d.bounds)
    RMap.setPlaces(dmap, d.places)
    d.places.forEach(function (place) {
      var d = $('<div class="item">' + place.address + '</div>')
      d.on('click', function () { place.marker.click() })
      ListPoints.append(d)
    })
  })
  .done(function() {
    SetupSimpleBar(ListPoints[0])
  })
  .fail(function(e) {
    ErrorLog('error ajax on html_delivery.tpl:')
    ErrorLog(e)
  })
}
</script>
