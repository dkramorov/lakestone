var ProductTabs = {
	'description': 'Характеристики изделия',
	'warranty': 'Гарантия',
	'moneyback': 'Условия обмена и возврата',
}
var ReviewTabs = {
	'reviews': {
		'text': 'Написать отзыв',
		'modal': '#ModalReview'
	},
	'screens': {
		'text': 'Написать отзыв',
		'modal': '#ModalReview'
	},
	'questions': {
		'text': 'Задать вопрос',
		'modal': '#ModalQuestion'
	}
}
var viewTab = function (t) {
	var $pt = $('#product_tabs')
	var tab = $(t).attr('data-tab')
	if ($(t).hasClass('active')) return;
	$pt.find('.menu .item').removeClass('active')
	$(t).addClass('active')
	$pt.find('.header .title').text(ProductTabs[tab])
	$pt.find('.content .tab').addClass('hide')
	$pt.find('.content .' + tab).removeClass('hide')
}
var viewRTab = function (t) {
	var $pt = $('#product_reviews_content')
	var tab = $(t).attr('data-tab')
	if ($(t).hasClass('active')) return;
	$pt.find('.menu .item').removeClass('active')
	$(t).addClass('active')

	$pt.find('.header .buttons button')
		.attr('data-target', ReviewTabs[tab]['modal'])
		.text(ReviewTabs[tab]['text'])

	$pt.find('.content .tab').addClass('hide')
	$pt.find('.content .' + tab).removeClass('hide')
}
var getRating = function (e) {
	var $t = $(e.currentTarget)
	var $p = $t.parents('.stars')
	if (e.type === 'mouseleave') $p = $(e.currentTarget)
	var $s = $p.find('.star')
	var r = $p.attr('data-rating')
	var val = $p.attr('data-value')
	var v = $s.index($t)
	var setup = function (v) {
		$s.attr('class', 'star')
		for (var i = 0; i <= v; i++) {
			$($s.get(i)).attr('class', 'star full')
		}
	}
	if (e.type === 'mouseleave') {
		if (val) return
		setup(4)
		return
	}
	if (v === val) return
	if (e.type === 'click') {
		$p.attr('data-value', v)
		$('input', $p).val(v + 1)
		setup(v)
	} else {
		if (!val) setup(v)
	}
}
// var runZoom = function($r) {
//   if ($r.hasClass('zooming')) return
//   $r.addClass('zooming')
//   $r.find('.wait-spinner').addClass('hidden')
//   var img, lens, result, cx, cy;
//   $r.find('.big img').addClass('img-zoom')
//   img = $r.find('.big img')[0]
//   result = $r.find('.zoom-content img')[0]
//   lens = document.createElement("DIV");
//   lens.setAttribute("class", "img-zoom img-zoom-lens");
//   /*insert lens:*/
//   img.parentElement.insertBefore(lens, img);
//   /*calculate the ratio between result DIV and lens:*/
//   var z = $r.find('.popup')[0]
//   cx = z.offsetWidth / lens.offsetWidth;
//   cy = z.offsetHeight / lens.offsetHeight;
//   // cx = result.offsetWidth / lens.offsetWidth;
//   // cy = result.offsetHeight / lens.offsetHeight;
//   /*set background properties for the result DIV:*/
//   // result.style.backgroundImage = "url('" + img.src + "')";
//   // result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";
//   result.style.width = (img.width * cx) + "px"
//   result.style.height = (img.height * cy) + "px"
//   /*execute a function when someone moves the cursor over the image, or the lens:*/
//   lens.addEventListener("mousemove", moveLens);
//   img.addEventListener("mousemove", moveLens);
//   /*and also for touch screens:*/
//   // lens.addEventListener("touchmove", moveLens);
//   // img.addEventListener("touchmove", moveLens);
//   function moveLens(e) {
//     var pos, x, y;
//     /*prevent any other actions that may occur when moving over the image:*/
//     e.preventDefault();
//     /*get the cursor's x and y positions:*/
//     pos = getCursorPos(e);
//     /*calculate the position of the lens:*/
//     x = pos.x - (lens.offsetWidth / 2);
//     y = pos.y - (lens.offsetHeight / 2);
//     /*prevent the lens from being positioned outside the image:*/
//     if (x > img.width - lens.offsetWidth) {x = img.width - lens.offsetWidth;}
//     if (x < 0) {x = 0}
//     if (y > img.height - lens.offsetHeight) {y = img.height - lens.offsetHeight;}
//     if (y < 0) {y = 0}
//     /*set the position of the lens:*/
//     lens.style.left = x + "px";
//     lens.style.top = y + "px";
//     /*display what the lens "sees":*/
//     // result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";
//     result.style.top = "-" + (y * cy) + "px"
//     result.style.left = "-" + (x * cx) + "px"
//   }
//   function getCursorPos(e) {
//     var a, x = 0, y = 0;
//     e = e || window.event;
//     /*get the x and y positions of the image:*/
//     a = img.getBoundingClientRect();
//     /*calculate the cursor's x and y coordinates, relative to the image:*/
//     x = e.pageX - a.left;
//     y = e.pageY - a.top;
//     /*consider any page scrolling:*/
//     x = x - window.pageXOffset;
//     y = y - window.pageYOffset;
//     return {x : x, y : y};
//   }
// }
// var loadZoom = function($r, $z, i) {
//   // $r.find('.popup .wait-spinner').removeClass('hidden')
//   offSpinner($z)
//   $z.empty()
//   var $i = $('<img>')
//   $i.on('load', function() {runZoom($r)})
//   $i.attr('src', i)
//   $z.append($i)
// }
// var zoomIn = function(t) {
//   $(document).on('mousemove', zoomOut)
//   var $r = $(t).parents('.image')
//   var $p = $r.find('.popup')
//   var $z = $p.find('.zoom-content')
//   var i = $(t).attr('data-popup_src')
//   var $zi = $z.find('img')
//   var ww = $(window).width()
//   var po = $p.offset()
//   if ($p.width() + po['left'] > ww) {
//     $p.width(ww - po['left'])
//     $p.height(ww - po['left'])
//   }
//   $p.removeClass('hidden')
//   if ($zi.length && $zi.attr('src') == i)
//     runZoom($r)
//   else
//     loadZoom($r, $z, i)
// }
// var zoomOut = function(e) {
//   var $p = $('#product_main .image')
//   if (!$p.hasClass('zooming')) return
//   if (!$(e.target).hasClass('img-zoom')) {
//     $(document).off('mousemove', zoomOut)
//     $p.find('.big img')
//       .off()
//       .on('mousemove', function() {zoomIn(this)})
//       .on('click', function() {getFullScreen(this)})
//     $p.find('.big .img-zoom-lens').remove()
//     $p.removeClass('zooming').find('.popup').addClass('hidden')
//   }
// }

var imgZoom = function (e) {
	// console.log('on', e, this);
	if ($(window).width() <= Defaults['screen_sm']) return
	$i = $(this)
	$p = $i.parent()
	var z = 2.5
	if (!$p.hasClass('zooming')) {
		$p.addClass('zooming')
		$i.css({'opacity': 0})
		$(document).on('mousemove', offZoom)
		$p.css({
			// 'background-image' : 'url("' + $i.attr('data-popup') + '")',
			'backgroundSize': $i.width() * z + 'px ' + $i.height() * z + 'px',
			'background-repeat': 'no-repeat'
		})
	}
	var p = getCursorPos(e)
	var zz = ($i.width() * z - $i.width()) / $i.width()
	$p.css('backgroundPosition', '-' + p.x * zz + 'px -' + p.y * zz + 'px')

	function getCursorPos(e) {
		var a, x = 0, y = 0;
		e = e || window.event;
		a = $i[0].getBoundingClientRect();
		x = e.pageX - a.left;
		y = e.pageY - a.top;
		x = x - window.pageXOffset;
		y = y - window.pageYOffset;
		return {x: x, y: y};
	}
}

var offZoom = function (e) {
	// console.log('off', e.target);
	var $t = $('#product_main .image .big .img')
	if (!$t.hasClass('zooming')) return
	if (typeof e === 'undefined' || !$(e.target).parent().hasClass('zooming')) {
		// console.log('remove', e.target, e);
		$(document).off('mousemove', offZoom)
		$t.removeClass('zooming')
		$('img', $t).css({'opacity': 1})
	}
}

////////////////
var setupThumbs = function () {
	if ($(window).width() > Defaults['screen_sm']) {
		let $i = $('#product_main .thumbs .item');
		// $('svg.pause', $i).fadeOut(0);
		// $('#product_main .image .big .video').fadeOut(0);
		$i.on('mouseenter click', function () {
			if ($('svg.play', this).length) showVideo($('svg.play', this));
			else if ($('img[data-num]', this).length) showBig($('img[data-num]', this));
			// else if ($('svg.pause', this).length) pauseVideo($('svg.pause', this));
		});
		$('#product_main .image .big').on('click', function (e) {
			offZoom();
			getFullScreen(this);
		})
	} else {
		$('#product_main .thumbs .item').on('click', function () {
			if ($('img[data-num]', this).length) swipeBig($('img[data-num]', this));
			// else if ($('svg.play', this).length) swipeVideo($('svg.play', this));
		})
	}
}

var showVideo = function (t) {
	let $p = $(t).parent();
	if ($p.hasClass('active')) return;
	$('#product_main .thumbs .item').removeClass('active');
	$p.addClass('active');
	startVideo($p);
	var $b = $('#product_main .image .big');
	var $v = $('video', $b);
	if ($v.attr('src') === $(t).attr('data-video')) return;
	onSpinner($v, 5000);
	$v.on('canplay', function () {
		offSpinner($v);
	})
		.attr({'src': $(t).attr('data-video')})
}

var startVideo = function ($i) {
	let $b = $('#product_main .image .big');
	$b.addClass('video');
	$('video', $b).get(0).play();
	$('.video', $b).fadeIn(0);
	$('.img', $b).fadeTo(0, 0);
	$('svg.play', $i).fadeIn(0);
	$('svg.pause', $i).fadeOut(0);
}

var pauseVideo = function ($i) {
	let $b = $('#product_main .image .big');
	$b.removeClass('video');
	$('video', $b).get(0).pause();
	$('.video', $b).fadeOut(0);
	$('.img', $b).fadeTo(0, 1);
	$('svg.play', $i).fadeOut(0);
	$('svg.pause', $i).fadeIn(0);
}

var showBig = function (t) {
	if ($(t).parent().hasClass('active')) return
	let $a = $('#product_main .thumbs .item.active');
	pauseVideo($a);
	$a.removeClass('active')
	$(t).parent().addClass('active')
	var $b = $('#product_main .image .big img')
	onSpinner($b, 500)
	$b.on('load', function () {
		offSpinner($b)
	})
		.attr({'src': $(t).attr('data-big_src'), 'data-popup_src': $(t).attr('data-popup_src')})
	$b.parent().css({'background-image': 'url("' + $(t).attr('data-popup_src') + '")'})
}

var getFullScreen = function (t, builder) {
	if ($(t).hasClass('video')) return;
	var $r = $('#product_main')
	var $m = $('.modal.full')
	var $c = $m.find('.content')
	var $pc = $('<div id="productFullScreen"></div>')
	var $owl = $('<div class="owl-carousel"></div>')
	var StartPosition = 0
	var tp = $('#menu2').outerHeight();
	var wh = $(window).height() - tp;
	var ww = $(window).width() * 0.8
	var ip = $('img', t).attr('data-popup_src')
	var owlResizer = function (e) {
		let ih = e.element[0].height;
		let iw = e.element[0].width;
		let fr = iw / ih;
		if (ih === 0) {
			console.log(e)
			return
		}
		if (iw > ww) iw = ww;
		if (ih > wh) ih = wh;
		if (ww > wh) {
			// console.log('hor', fr)
			var nh = $owl.height();
			var nw = $owl.height() * fr;
			if (nw > ww) {
				nw = ww;
				nh = ww / fr;
			}
		} else {
			console.log('ver', fr)
			var nh = Math.floor(ww / fr);
			var nw = Math.floor(ww);
			if (nh > wh) {
				nh = wh;
				nw = wh * fr;
			}
		}
		$owl.css({
			'display': 'flex',
			'align-items': 'center',
		});
		$('.modal-dialog', $m).css({
			'margin': 'auto',
		});
		$('.modal-content', $m).css({
			'border': 'none',
			'background-color': 'transparent',
		});
		$(e.element).height(nh)
		$(e.element).width(nw)
		$pc.width(nw)
		$pc.height(nh);
		$pc.css('margin-top', tp);
		$m.find('.modal-dialog').width($pc.outerWidth())
		$owl.trigger('refresh.owl.carousel');
	}
	var owlChanged = function (e) {
		let $pc = $('#productFullScreen');
		let $a = $('.owl-item.active', e.target);
		let $i = $('img', $a);
		let $m = $('.modal.full .modal-dialog');
		let iw = $i.width();
		let max = $(window).width() * 0.8;
		if (iw > max) {
			iw = max;
		}
		if (iw != $m.width()) {
			$m.width(iw);
			// $pc.height($i.height());
			$pc.width(iw);
			$owl.trigger('refresh.owl.carousel');
		}
		let src = $('.owl-item.active img', e.target).attr('data-src');
		// $('#product_main .thumbs .item').removeClass('active');
		$('#product_main .thumbs .item img[data-num]').each(function () {
			if ($(this).attr('data-popup_src') !== src) return;
			showBig(this);
			// let $p = $(this).parent();
			// $p.addClass('active');
		})
	};
	if (!$m.data('keydown_listener')) {
		$m.on('keydown', function (e) {
			if (e.keyCode == 39) $('.modal.full .owl-carousel').trigger('next.owl.carousel', [0]);
			else if (e.keyCode == 37) $('.modal.full .owl-carousel').trigger('prev.owl.carousel', [0]);
		});
		$m.data('keydown_listener', true);
	}
	if (!$('.close', $m).length) {
		let $close = $('<button data-dismiss="modal" class="modal-close"><svg class="svg-close"><use xlink:href="#svg-close"></use></svg></button>');
		$m.append($close);
	}
	if (!$('.owl-nav[disabled]', $m).length) {
		let $prev = $('<button type="button" role="presentation" class="owl-prev"><svg class="owl_button"><use xlink:href="#svg-angle-left"></use></svg></button>');
		let $next = $('<button type="button" role="presentation" class="owl-next"><svg class="owl_button"><use xlink:href="#svg-angle-right"></use></svg></button>');
		$next.on('click', function () {
			$('#productFullScreen .owl-carousel').trigger('next.owl.carousel');
		});
		$prev.on('click', function () {
			$('#productFullScreen .owl-carousel').trigger('prev.owl.carousel');
		});
		let $container = $('<div class="owl-nav"></div>');
		$container.append($prev).append($next);
		$m.append($container);
	}
	$c.empty()
	$pc.append($owl)
	$c.append($pc)
	// $('.modal-dialog', $m).width($(window).width() * 0.8);
	// $('.modal-dialog', $m).height($(window).height() * 0.9)
	$('.modal-dialog', $m).css({
		// 'top': 0,
		// 'width': ww +'px',
		// 'height': wh + 'px',
	});
	// $pc.height()
	$owl.height(wh - 2)
	$m.find('.wait-spinner').fadeOut()
	if (typeof builder === 'function') {
		StartPosition = builder($owl);
	} else {
		$r.find('.thumbs .item img.image[data-num]').each(function (n, i) {
			if (ip === $(i).attr('data-popup_src')) StartPosition = n
			var $i = $('<img class="img-responsive owl-lazy" data-src="' + $(i).attr('data-popup_src') + '">')
			$owl.append($i)
		})
	}
	$owl.owlCarousel({
		items: 1,
		// nav: true,
		dots: false,
		rewind: true,
		lazyLoad: true,
		// autoWidth: true,
		startPosition: StartPosition,
		onTranslated: owlChanged,
		onLoadedLazy: owlResizer,
		// navText: ['<svg class="owl_button"><use xlink:href="#svg-angle-left"/></svg>', '<svg class="owl_button"><use xlink:href="#svg-angle-right"/></svg>'],
	})
	// console.log($('.modal-dialog', $m).attr('style'));
	$m.modal('show')
}

const showReviewImages = function (t) {
	let $p = $(t).parents('.review-images')
	let $m = $('.modal.full');
	let builder = function ($container) {
		let StartPosition = 0;
		$('img', $p).each(function (n, i) {
			if ($(i).attr('data-popup_src') === $(t).attr('data-popup_src')) StartPosition = n;
			$container.append($('<img class="img-responsive owl-lazy" data-src="' + $(i).attr('data-popup_src') + '">'));
		});
		return StartPosition;
	}
	getFullScreen($p, builder);
	/*
	  $('.modal-dialog', $m).css({
		width: '0.85vw',
		margin: 'auto',
	  });
	*/
}

var createSwipe = function ($s, $t) {
	$si = $('.item', $s)
	$t.addClass('owl-carousel')
		.css({
			'background-image': '',
			'ovwerflow': 'hidden',
			'touch-action': 'pan-y',
			'max-width': $('#product_main').width()
		})
	for (var i = 1; i < $si.length; i++) {
		if ($('svg.play', $si[i]).length) {
			$t.append('<video controls autoplay loop muted class="img-responsive" src="' + $('svg.play', $si[i]).attr('data-video') + '">')
		} else {
			$t.append('<img src="image/empty.png" data-src="' + $('img', $si[i]).attr('data-big_src') + '" class="owl-lazy">')
		}
	}
	$t.owlCarousel({
		items: 1,
		nav: false,
		dots: false,
		rewind: true,
		lazyLoad: true,
		onTranslate: function (e) {
			swipeBig(e.item.index)
		},
	})
}
var swipeBig = function (t) {
	var $o = $('#product_main .image .big .img');
	var $t = $('#product_main .thumbs .item');
	$t.filter('.active').removeClass('active');
	$('video', $o).each(function () {
		this.pause()
	});
	$t.filter('.playing').each(function () {
		$('svg.play', $t).fadeOut(0);
		$('svg.pause', $t).fadeIn(0);
	});
	if (typeof t !== 'number') {
		$(t).parent().addClass('active')
		$o.trigger('to.owl.carousel', $(t).attr('data-num'))
	} else {
		$t.eq(t).addClass('active');
		let $oi = $('.owl-item', $o).eq(t);
		console.log($oi);
		if ($('video', $oi).length) {
			$('video', $oi).get(0).play();
			$t.eq(t).addClass('playing');
			$('svg.play', $t).fadeIn(0);
			$('svg.pause', $t).fadeOut(0);
		}
	}
}
var loadImages = function (pid) {
	var $m = $('#product_main')
	var $t = $m.find('.thumbs')
	onSpinner($m.find('.image .big img'))
	$t.empty()
	$.ajax({
		'url': '/index.php?route=product/product/productImages',
		'method': 'GET',
		'data': {'product_id': pid},
		'success': function (d) {
			d.images.forEach(function (t) {
				var $i = $('<img class="img-responsive">').attr({
					'src': t.thumb,
					'data-big_src': t.image,
					'data-popup_src': t.popup
				})
				$t.append($('<div class="item"></div>').append($i))
			})
			setupThumbs()
			$t.find('img').first().click()
		},
		'complete': function () {
			offSpinner($m.find('.image .big img'))
		}
	})
}
var checkShowRoom = function (l) {
	if (l === 'г. Москва')
		$('.banner-ShowRoom').fadeIn(0)
	else
		$('.banner-ShowRoom').fadeOut(0)
}
// var openReviewWrite = function() {
//   $('#review_form').show()
//   $('#review_write').trigger('click')
// }

/*
const dropHandler = function () {
  let ev = window.event;
  ev.preventDefault();
  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.items.length; i++) {
      // If dropped items aren't files, reject them
      if (ev.dataTransfer.items[i].kind === 'file') {
        var file = ev.dataTransfer.items[i].getAsFile();
        console.log('... file[' + i + '].name = ' + file.name);
      }
    }
  } else {
    // Use DataTransfer interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.files.length; i++) {
      console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
    }
  }
  console.log(ev);
}
*/

$(document).ready(function () {
	FileUploader.init();
	//ondrop="dropHandler();" ondragover="dragOverHandler();"
})

const FileUploader = {
	template: $('<div class="item"><div class="image"><img src="/image/image.svg"></div><div class="bar"></div><svg class="remove"><title>Удалить</title><use xlink:href="#svg-close"/></svg></div>'),
	maxCount: 2,
	acceptable: ['image/png', 'image/jpeg', 'image/gif', 'image/webp'],
	maxSize: 1024 * 1024 * 300,
	init: function () {
		let FU = this;
		if (typeof FU.config === 'object') {
			if (FU.config.max_count) FU.maxCount = FU.config.max_count;
			if (FU.config.max_size) FU.maxSize = FU.config.max_size;
			if (FU.config.acceptable) FU.acceptable = FU.config.acceptable
		}
		;
		FU.$Input = $('#FileUploadInput');
		FU.$Container = $('#FileDropContainer');
		FU.$FileAddIcon = $('#FileAddIcon');
		FU.$Input.attr('accept', FU.acceptable.join(','));
		FU.$Area = $('#FileDropField');
		FU.$Input.on('change', function (e) {
			FU.upload();
		});
		FU.token = FU.$Area.attr('data-token');
		this.$Area
			.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
				e.preventDefault();
				e.stopPropagation();
			})
			.on('drop', function (e) {
				FU.drop(e);
			})
			.on('dragover dragenter', function (e) {
				FU.dragover(e);
			})
			.on('dragleave dragend', function (e) {
				FU.dragleave(e);
			})
	},
	remove: function (e) {
		e.stopPropagation();
		let FU = FileUploader;
		console.log(e)
		let $p = $(e.target).parents('.item');
		$p.remove();
		FU.checkItems();
	},
	checkItems: function () {
		let FU = FileUploader;
		let count = $('.item', FU.$Area).length;
		if (count) $('.note.note-label', FU.$Area).css('display', 'none');
		// else $('.note.note-label', FU.$Area).css('display', 'inline-flex');
		if (count >= FU.maxCount) {
			$('.note.note-grey', FU.$Area).css('display', 'none');
			FU.$FileAddIcon.css('display', 'none');
		} else {
			$('.note.note-grey', FU.$Area).css('display', 'block');
			FU.$FileAddIcon.css('display', 'block');
		}
	},
	checkFiles: function (DataTransferFiles) {
		let FU = FileUploader;
		let files = [];
		let error = '';
		let counter = $('.item', this.$Area).length;
		Object.keys(DataTransferFiles).forEach(function (k) {
			let file = DataTransferFiles[k];
			if (!FU.acceptable.includes(file.type)) {
				error += 'Не поддерживается файл такого типа: ' + file.name + '<br>';
				return;
			}
			if (file.size > FU.maxSize) {
				error += 'Этот файл слишком большой: ' + file.name + '<br>';
				return;
			}
			if (counter++ >= FU.maxCount) {
				error += 'Превышен лимит файлов, пропущен файл: ' + file.name + '<br>';
				return;
			}
			files.push(file);
		})
		if (error) showErrorMessage(error);
		return files;
	},
	upload: function () {
		let FU = FileUploader;
		let files = FU.checkFiles(FU.$Input[0].files);
		if (files.length) FU.post(files);
		FU.$Input.val(null);
		FU.checkItems();
	},
	drop: function (e) {
		let FU = FileUploader;
		let ev = e.originalEvent;
		// if (ev.dataTransfer.items) {
		//   console.log(ev.dataTransfer.items);
		// }
		// if (ev.dataTransfer.files) {
		//   console.log(ev.dataTransfer.files);
		// }
		let files = FU.checkFiles(e.originalEvent.dataTransfer.files);
		if (files.length) FU.post(files);
		FU.dragleave();
		FU.checkItems();
	},
	post: function (files) {
		let FU = FileUploader;
		try {
			files.forEach(function (file) {
				let formdata = new FormData();
				let name = 'file' + Math.ceil(Math.random() * 1000000000);
				formdata.append('token', FU.token);
				formdata.append(name, file);
				let $img = FU.template.clone();
				$img.attr('data-name', name);
				$('.remove', $img).on('click', function (e) {
					FU.remove(e)
				});
				FU.$Container.append($img);
				FU.$Container.append(FU.$FileAddIcon);
				$.ajax({
					url: "/uploadReviewPhotos",
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					xhr: function () {
						let xhr = new XMLHttpRequest(); //$.ajaxSettings.xhr();
						if (xhr.upload) {
							xhr.upload.addEventListener('progress', function (event) {
								if (event.lengthComputable) {
									FU.setProgress(name, Math.ceil(event.loaded / event.total * 100));
								}
							}, false);
						}
						return xhr;
					},
					success: function (d) {
						console.log('success', name, d)
						if (d.status === 'OK') {
							FU.setProgress(name, 100);
							FU.turnOn(name, d[name]['file_href']);
						} else {
							FU.turnOff(name, d.error);
						}
					},
					error: function (res) {
						console.log('error', name, res)
						FU.turnOff(name);
					}
				});
			});
		} catch (e) {
			showErrorMessage(e);
		}
	},
	setProgress: function (name, percent) {
		let FU = FileUploader;
		$('.item[data-name="' + name + '"] .bar', FU.$Container).css('width', percent + '%');
	},
	turnOn: function (name, href) {
		let FU = FileUploader;
		let $I = $('.item[data-name="' + name + '"]', FU.$Container);
		let $img = $('img', $I);
		if (!$I.length) return;
		if (!href) return;
		$img.attr('src', href);
		$img.on('load', function () {
			if ($img.width() > $img.height()) {
				$img.css('height', '100%');
			} else {
				$img.css('width', '100%');
			}
		})
		$I.on('click', function () {
			FU.showImage(this);
		})
	},
	turnOff: function (name, error) {
		let FU = FileUploader;
		let $I = $('.item[data-name="' + name + '"]', FU.$Container);
		if (!$I.length) return;
		if (error instanceof Error) text = error.message;
		else if (typeof error !== 'undefined') text = error;
		else text = 'Ошибка при загрузке файла';
		$I
			.attr('title', text)
			.addClass('disabled');
	},
	showImage: function (t) {
		console.log('showImage', t);
		let $img = $('img', t).clone();
		$img.css({
			'display': 'block',
			'width': '100%',
			'height': 'auto',
			'padding': '2rem 3rem',
		});
		let $M = $('.modal.default');
		let $mmc = $('.modal-content', $M);
		let $mc = $('.content', $mmc);
		$mc.empty().append($img);
		$M.css('z-index', 100500);
		$('.modal-dialog', $M).css({
			'height': '80vh'
		});
		$M.modal('show');
		offSpinner($mc);
	},
	dragover: function (e) {
		let FU = FileUploader;
		let list;
		console.log(e);
		if (e.originalEvent.dataTransfer.items)
			list = e.originalEvent.dataTransfer.items;
		else
			list = e.originalEvent.dataTransfer.files;
		let error = true;
		Object.keys(list).forEach(function (k) {
			let file = list[k];
			if (FU.acceptable.includes(file.type)) {
				error = false;
			}
		})
		if (error) {
			e.originalEvent.dataTransfer.dropEffect = 'none';
			FU.$Area.addClass('dragstop');
		} else {
			e.originalEvent.dataTransfer.dropEffect = 'copy';
			FU.$Area.addClass('dragover');
		}
	},
	dragleave: function () {
		let FU = FileUploader;
		FU.$Area.removeClass('dragover');
		FU.$Area.removeClass('dragstop');
	}
}