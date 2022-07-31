var fullDescription = function() {
  var $d = $('.description')
  var h = $('#BtnGoProduct').offset()['top'] - $d.find('.short').offset()['top'] - 15

  $d.find('.text').css({'max-height': h})
  $('#BtnProductFull').fadeOut()
  $d.find('.short').fadeOut(function() {$d.find('.full').fadeIn()})
}
var setupThumbs = function() {
  $('.product-quickview .images .thumbs img').on('mouseenter click', function() {
    showBig(this)
  })
/*
  $('.product-quickview .images .thumbs').owlCarousel({
  	items: 10,
  	autoPlay: 3000,
  	singleItem: false,
  	nav: true,
    navText: ['<svg class="owl_button"><path d="M10 0 l-10 10 l10 10" filter="url(#feShadow)"></svg>', '<svg class="owl_button"><path d="M0 0 l10 10 l-10 10" filter="url(#feShadow)"></svg>'],
  	dots: false,
    margin: 3,
  	transitionStyle: 'fade'
  });
*/
}
var showBig = function(t) {
  if ($(t).hasClass('active')) return
  var $q = $('.modal .product-quickview')
  // var $ms = $q.parents('.canvas').find('.wait-spinner')
  var $t = $q.find('.images .thumbs')
  var $i = $('.images .image', $q)
  // $ms.fadeIn(0)
  onSpinner($q)
  $('img', $t).removeClass('active')
  $(t).addClass('active')
  $('img', $i).attr({'src':$(t).attr('data-big'), 'data-popup':$(t).attr('data-popup')})
  .on('load', function() {offSpinner($q)})
  $i.css({'background-image' : 'url("' + $(t).attr('data-popup') + '")'})
  // setTimeout(function () {$ms.fadeOut(0)}, 500)
}
var imgZoom = function(e) {
  // console.log('on', e, this);
  var $i = $('img', this)
  var z = 2.5
  if (!$(this).hasClass('zooming')) {
    $(this).addClass('zooming')
    $i.css({'opacity':0})
    $(document).on('mousemove', offZoom)
    $(this).css({
      // 'background-image' : 'url("' + $i.attr('data-popup') + '")',
      'backgroundSize': $i.width() * z + 'px ' + $i.height() * z + 'px',
      'background-repeat': 'no-repeat'
    })
  }
  var p = getCursorPos(e)
  var zz = ($i.width() * z - $i.width()) / $i.width()
  $(this).css('backgroundPosition', '-' + p.x * zz + 'px -' + p.y * zz + 'px')
  function getCursorPos(e) {
    var a, x = 0, y = 0;
    e = e || window.event;
    a = $i[0].getBoundingClientRect();
    x = e.pageX - a.left;
    y = e.pageY - a.top;
    x = x - window.pageXOffset;
    y = y - window.pageYOffset;
    return {x : x, y : y};
  }
}
var offZoom = function(e) {
  // console.log('off', e.target);
  var $t = $('.modal .product-quickview .images .image')
  if (!$t.hasClass('zooming')) return
  if (!$(e.target).parent().hasClass('zooming')) {
    // console.log('remove', e.target, e);
    $(document).off('mousemove', offZoom)
    $t.removeClass('zooming')
    $('img', $t).css({'opacity':1})
  }
}
var loadImages = function(pid) {
  var $m = $('.modal .product-quickview').parents('.modal')
  var $ms = $m.find('.wait-spinner')
  var $t = $m.find('.product-quickview .images .thumbs')
  $ms.fadeIn(0)
  $m.find('.modal-dialog').width($(window).width() * 0.6)
  $t.empty()
  // $t.trigger('destroy.owl.carousel')
  $.ajax({
    'url': '/index.php?route=product/product/productImages',
    'method': 'GET',
    'data': {'product_id':pid, 'limit':9},
    'success': function(d) {
      d.images.forEach(function(t){
        var $i = $('<img>').attr({'class':'img-responsive', 'src':t.thumb, 'data-big':t.image, 'data-popup':t.popup})
        $t.append($i)
      })
      setupThumbs()
      $t.find('img').first().click()
    },
    'complete': function() {
      $m.find('.wait-spinner').fadeOut();
    }
  })
}
