var toggleMMenu = function (t) {
  $('#categories2m').toggle()
}
var toggleSMenu = function (t) {
  var $p
  if ($(t).hasClass('item'))
    $p = $(t)
  else
    $p = $(t).parents('.item')
  var $s = $p.next()
  if (!$s.hasClass('sub_cat')) {
    console.error('unexpected item: ', $s)
    console.error('target: ', t)
    return
  }
  $p.find('.operate svg').toggle()
  $s.toggle()
}
var menu_watch = function (e) {
  var close = function () {
    $('.cat_ext').fadeOut(100);
    $('#categories2 .menu .item').removeClass('active');
    $(document).off('mousemove', menu_watch);
    $('#categories2').removeClass('ext-opened');
  }
  if ($(e.target).parents('#categories2').length == 0) {
    close();
    // } else if ($(e.target).is('nav.container')) {
    // close();
  } else {
    // console.log(e.target);
  }
};
var loadRMap = function (f) {
  if (typeof RMap !== 'object') {
    if (typeof f === 'function')
      window.RMapInit = function () {
        f()
      }
    $.getScript('catalog/view/javascript/locality.min.js')
    // $('head').prepend('<link rel="stylesheet" href="catalog/view/theme/lakestone/stylesheet/simplebar.min.css">')
    // $.getScript('catalog/view/theme/lakestone/js/simplebar.js')
  } else {
    if (typeof f === 'function')
      f()
  }
}
var SetupSimpleBar = function (t) {
  checkStartUp(['SimpleBar'], function () {
    var ListPointsSB = new SimpleBar(t, {autoHide: false})
    //ListPointsSB.recalculate()
  })
}
var dropdown_locality = function () {
  var $ = jQuery211
  var t = $('#input_locality').val()
  let $URL = $('<a href="' + location.href + '"></a>');
  $('#SelectLocality .dropdown-menu').remove()
  if (t.length == 0)
    return
  $.post(
      '/index.php?route=common/locality/search',
      {'name': t},
      function (d) {
        var p = $('#input_locality').parent()
        if (Object.keys(d.result).length > 0) {
          var ul = $('<ul class="dropdown-menu" aria-labelledby="input_locality"></ul>')
          Object.keys(d.result).forEach(function (loc) {
            let cur = d['result'][loc];
            var re = new RegExp(t, "i")
            var n = loc.replace(re, '<span class="selected">$&</span>')
            var l = $('<a data-locality="' + loc + '">' + n + '</a>');
            if (typeof cur['SubDomain'] !== 'undefined' && cur['SubDomain'].length > 3) {
              $URL[0].hostname = cur['SubDomain'];
              l.attr('href', $URL[0]);
            } else {
              //role="button" onClick="select_locality(loc)"
              l.attr('role', 'button');
              l.attr('onclick', 'setRemoteLocality(this)');
              l.attr('data-sub_domain', cur['RemoteDomain']);
              $URL[0].hostname = cur['RemoteDomain'];
              l.attr('href', $URL[0]);
            }
            ul.append($('<li></li>').append(l));
          })
          $('#input_locality').after(ul)
          if (!p.hasClass('open'))
            p.addClass('open')
        } else {
          p.removeClass('open')
        }
      }
  )
}
const setRemoteLocality = function(t) {
  window.event.preventDefault();
  let a = $('#SelectLocality .content');
  let rd = $(t).attr('data-sub_domain');
  let loc = $(t).attr('data-locality');
  let url = $(t).attr('href');
  onSpinner(a);
  $.ajax({
    url: '//' + rd + '/index.php?route=common/locality/setRemoteLocality&loc=' + loc,
    xhrFields: {
      withCredentials: true
    },
    success: function(d) {
      location = url;
      // offSpinner(a);
    }
  });
  return false;
}
var setLocality = function (loc) {
  var $ = jQuery211
  onSpinner(t)
  var t = (typeof loc === 'undefined' ? $('#input_locality').val() : loc);
  if (t) {
    $.get('/index.php?route=common/locality/setLocality', {'name': t}, function () {
      setup_delivery(t)
      DPoint = ''
      $('#input_locality').trigger('change_locality', [t])
    })
  }
}
const select_locality = function (tr) {
  var $ = jQuery211
  var d = $(tr).attr('data-sub_domain')
  var t = $(tr).text()
  onSpinner(tr)
  if (d) {
    var l = location.href.split('/')
    location.href = l[0] + '//' + d + '/' + l[3]
    return
  }
  // $('#input_locality').val($(t).text()).focus()
  $('.dropdown-menu').parent().removeClass('open')
  // $.get('/index.php?route=common/locality/setLocality', {'name':t}, function () {setup_delivery(t)})
  $.get('/index.php?route=common/locality/setLocality', {'name': t}, function () {
    setup_delivery(t)
    DPoint = ''
    $('#input_locality').trigger('change_locality', [t])
  })
}
var setup_delivery = function (t) {
  $('#SelectLocality .city').text(t)
  window.Locality = t
  $('#SelectLocality').modal('hide')
  $('#menu2m .delivery-setup').load('/index.php?route=common/locality/getSetupDeliveryM')
  $('#up-nav2 .delivery-setup').load('/index.php?route=common/locality/getSetupDelivery', function () {
    offSpinner($('#SelectLocality .content'))
  })
}
var setup_order_placing = function () {
  var $ = jQuery211
  var w = $(window).width()
  var m = $('#order_placing')
  var b = 0.9
  if (w <= 767)
    b = 0.98
  var h = $(window).innerHeight() * b
  m.find('.modal-dialog').width(w * b).css('top', 20)
  m.find('.modal-content').height(h)
  var mh = m.find('.modal-header').outerHeight()
  var th = m.find('.modal-body .title').outerHeight()
  if (mh == 0)
    mh = 51
  if (th == 0)
    th = 23
  var mb = h - mh - 30
  // console.log(mb, mh, h);
  if (w <= 767) {
    m.find('.modal-body .addresses').height(mb * 0.4 - th)
    m.find('.map-canvas').height(mb * 0.6)
  } else {
    m.find('.modal-body .addresses').height(mb - th)
    m.find('.map-canvas').height(mb)
    m.find('svg.spinner').css('top', mb / 2)
  }
}
var presetup_modal_window = function () {
  var m_id = $(this).attr('id')
  switch (m_id) {
    case 'order_placing':
      // setup_order_placing()
      break;
  }
}
var setupModalPosition = function (m) {
  var pad = 40
  var wh = $(window).height() - pad
  var $md = $('.modal-dialog', m)
  var mh = $md.outerHeight()
  var top = (wh - mh) / 2
  if (wh < mh) {
    // $('.modal-content', m).css({'overflow':'auto', 'height': wh + 'px'})
    top = 0
  }
  $md.css('top', top + 'px')
}
var setup_modal_window = function () {
  var m_id = $(this).attr('id')
  switch (m_id) {
    case 'SelectLocality':
      $('#input_locality').focus().val('')
      break;
    case 'order_placing':
      setup_order_placing()
      loadRMap(OP_Init)
      break;
  }
  // setupModalPosition(this)
}
var reset_modal_window = function () {
  // $('.modal-dialog', this).css({
  //   'top': null,
  //   'height': null,
  //   'width': null,
  // })
  $('.modal-dialog', this).removeAttr('style');
}
var setPoint = function (t) {
  DPoint = $(t).attr('data-provider') + '.' + $(t).attr('data-id')
  var addr = $(t).parents('.pickpoint_info').find('.address').text()
  var P = {'dpoint': DPoint, 'addr': addr}
  $.getJSON('index.php?route=common/locality/setPlace', P)
  $('#order_placing').trigger('setPoint', P)
  $(t).prop('disabled', true)
          .text('выбран')
}
var messageMap = function (msg, rmap) {
  var dom = rmap.dom
  var $p = $(dom).parents('.map-canvas')
  var $c = $p.find('.cover')
  $(dom).css('opacity', 0.2)
  $c.addClass('error')
  $c.find('.spinner').css('display', 'none')
  $c.css('display', 'block')
  $c.find('.message').text(msg)
}
/*
 #order_placing
 */
var OP_Init = function () {
  var w = window
  var m = $('#order_placing')
  if (typeof w.OPMap === 'undefined') {
    w.OPMap = RMap.initMap(m.find('.map'))
    w.OPMap.Locality = w.Locality
  } else if (w.OPMap.Locality == w.Locality) {
    return
  }
  m.find('.modal-header .city').text(w.Locality)
  OPMap.callback = {
    infowindow_content: function (place) {
      var b
      if (DPoint === place.Provider + '.' + place.ID) {
        b = '<button data-provider="' + place.Provider + '" data-id="' + place.ID + '" onClick="setPoint(this)" disabled="yes" class="btn btn-primary setPlace">Выбран</button>'
      } else {
        b = '<button data-provider="' + place.Provider + '" data-id="' + place.ID + '" onClick="setPoint(this)" class="btn btn-primary setPlace">Выбрать этот пункт</button>'
      }
      return '<div class="pickpoint_info"><strong class="address">' + place.address + '</strong><p class="gmap_infowindow_guide">' + place.guide + '</p><div class="text-center">' + b + '</div></div>'
    },
    unblockMap: function () {
      var dom = OPMap.dom
      var $p = $(dom).parents('.map-canvas')
      var $c = $p.find('.cover')
      if (OPMap.ds) {
        OPMap.ds.click();
        OPMap.ds = false
      } else
        OPMap.ds = true
      // if (!$c.hasClass('error')) {
      $(dom).css('opacity', 1)
      $c.css('display', 'none')
      // }
    },
    errorMap: function () {
      messageMap('Не удалось загрузить карты', OPMap)
      delete OPMap
    }
  }
  var $nav = m.find('.modal-body .addresses')
  // var $addr = $('<li><a></a></li>')
  $nav.empty()
  $.getJSON('index.php?route=common/locality/getPlaces&locality=' + w.Locality, function (d) {
    if (typeof d.places === 'undefined' || !d.places) {
      messageMap('К сожалению, в этом населенном пункте нет точек выдачи заказа', OPMap)
      $nav.load('index.php?route=common/locality/getEmptyPickPoint')
      return
    }
    RMap.setBounds(OPMap, d.bounds)
    RMap.setPlaces(OPMap, d.places)
    d.places.forEach(function (place) {
      var d = $('<div class="item" data-pickpoint_id="' + place.Provider + '.' + place.ID + '"><a>' + place.address + '</a></div>')
      d.on('click', function () {
        place.marker.click()
      })
      $nav.append(d)
    })
    // SetupSimpleBar($nav[0])
    if (typeof DPoint !== 'undefined') {
      if (typeof $ds === 'undefined') {
        $ds = $nav.find('div[data-pickpoint_id="' + DPoint + '"]')
        if ($ds.length > 0) {
          if (OPMap.ds)
            $ds.click()
          else
            OPMap.ds = $ds
        }
      }
    }
  }).fail(function (e) {
    ErrorLog('error ajax: ', e)
  })
}
var showQuickview = function (pid) {
  var $m = $('.modal.ajax')
  var $mmc = $('.modal-content', $m)
  var $mc = $('.ajax-content', $mmc)
  onSpinner($mc)
  var ww = $(window).width()
  var wh = $(window).height()
  var mw = ww * 0.6
  if (ww < 900)
    mw = ww * 0.9
  // $mc.empty()
  if (!$m.hasClass('in')) {
    $m.find('.modal-dialog').width(mw).css('margin', 'auto')
    $mmc.height(wh * 0.8)
    $m.modal('show')
  }
  // $mc.load('/index.php?route=product/product/quickview', {product_id: pid}, function() {
  //   $mmc.css('height', '')
  // 	offSpinner($mc)
  // })
  $.post('/index.php?route=product/product/quickview', {product_id: pid}, function (d) {
    $mc.empty().append(d)
    $mmc.css('height', '')
    offSpinner($mc)
  })
}
var closeVideo = function () {
  var $m = $('.modal.default')
  $('.modal-content .content', $m).empty()
  $m.off('hidden.bs.modal', window.closeVideo)
}
var showVideo = function (vid, prov) {
  prov = (typeof prov === 'undefined' ? 'youtube' : prov)
  var $m = $('.modal.default')
  var $mmc = $('.modal-content', $m)
  var $mc = $('.content', $mmc)
  // onSpinner($mc)
  var ww = $(window).width()
  var wh = $(window).height() - 40
  var mw, mh
  if (ww > wh) {
    mh = wh * 0.8
    mw = mh * 1.77
    if (mw > ww * 0.8) {
      mw = ww * 0.8
      mh = mw * 0.56
    }
  } else {
    mw = ww * 0.8
    mh = mw * 0.56
  }
  if (!$m.hasClass('in')) {
    $m.find('.modal-dialog').width(mw).css('margin', 'auto')
    $m.modal('show')
  }
  var d = $('<div style="margin-top:38px"><div class="media"><iframe src="https://www.youtube.com/embed/' + vid + '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div></div>')
  $mc.empty().append(d)
  $m.on('hidden.bs.modal', window.closeVideo)
  offSpinner($mc)
}
var LazyLoadObject = function (obj, func) {
  if (typeof IntersectionObserver === 'undefined') {
    // console.log('bypass for ', obj)
    func.call({target: obj})
    return
  }
  var o = $(obj).get()[0];
  if (typeof this.funcs === 'undefined') {
    this.funcs = []
  }
  this.funcs.push({
    'target': o,
    'func': func
  })
  this.obj_load = function (entries) {
    // console.log(entries[0]);
    entries.forEach(function (entry) {
      // if (entry.intersectionRatio <= 0) return;
      if (!entry.isIntersecting)
        return;
      funcs.forEach(function (t, i) {
        if (t.target == entry.target) {
          t.func()
          observer.unobserve(entry.target)
          delete funcs[i]
        }
      })
    })
  }
  this.config = {
    // rootMargin: '100px 0px 100px 0px'
  }
  if (typeof this.observer === 'undefined') {
    this.observer = new IntersectionObserver(obj_load, config)
  }
  this.observer.observe(o);
}
var onSpinner = function (t, w) {
  w = (typeof w === 'undefined' ? 5000 : w)
  $(t).parents('.canvas').find('.wait-spinner').removeClass('hidden')
  if (typeof window.timeoutSpinner !== 'undefined')
    clearTimeout(window.timeoutSpinner)
  window.timeoutSpinner = setTimeout(function () {
    offSpinner(t)
  }, w)
}
var offSpinner = function (t) {
  $(t).parents('.canvas').find('.wait-spinner').addClass('hidden')
  if (typeof window.timeoutSpinner !== 'undefined')
    clearTimeout(window.timeoutSpinner)
}

const showErrorMessage = function (error, title) {
  let text = error;
  let $M = $('#error-message');
  if (error instanceof Error) {
    text = error.message;
  }
  title = typeof title === 'undefined' ? 'Сообщение об ошибке' : title
  $('.modal-header .title', $M).text(title);
  $('.modal-body .text', $M).html(text);
  $M.modal('show');
}
