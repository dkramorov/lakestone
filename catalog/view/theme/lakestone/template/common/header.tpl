<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php
foreach ($meta as $name => $content) {
  echo '<meta name="' . $name . '" content="' . $content . '">';
}
?>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<style>
@font-face {
  font-family: "Montserrat";
  font-display: fallback;
  src: url('/fonts/Montserrat-Medium.ttf') format('truetype');
  font-style: normal;
  font-weight: 500;
}
@font-face {
  font-family: "Montserrat";
  font-display: fallback;
  src: url('/fonts/Montserrat-SemiBold.ttf') format('truetype');
  font-style: normal;
  font-weight: 600;
}
@font-face {
  font-family: "Montserrat";
  font-display: fallback;
  src: url('/fonts/Montserrat-Bold.ttf') format('truetype');
  font-style: normal;
  font-weight: 700;
}
</style>
<script>
var LoadScript = new Array()
var DocumentReady = new Array()
var WindowLoad = new Array()
var LoadStyle = new Array()
var StartUp = new Array()
var StartUpCheck = new Array()
var ErrorMessages = new Array()
var ErrorWaiting = false
var Locality = '<?=$Locality?>'
var DPoint = '<?=$DPoint?>'
var Defaults = {
  'screen_sm': 768,
}
var MyError = function(msg, e) {
  this.message = msg
  this.err = e
}
var ErrorLog = function (e) {
    if (typeof console.trace === 'function')
      console.trace(e)
    if (typeof e !== 'undefined') {
	    console.error(e)
	    var msg = ''
    	if (typeof navigator === 'object') {
    	  msg = msg + 'UserAgent: ' + navigator.userAgent + "\n"
    	}
    	if (typeof location !== 'undefined') {
    	  msg = msg + 'Location: ' + location + "\n"
    	}
    	if (e instanceof MyError) {
    	  msg = msg + 'JS MyError message is: ' + e.message + "\n"
    	  e = e.err
    	}
    	if (e instanceof Error) {
    		var d = ': '
    		if ( typeof e.lineNumber != 'undefined')
    			d = '[' + e.lineNumber + ']: '
    		msg = msg + 'JS Error at ' + location.href + d + e.message
    		if ( typeof e.stack != 'undefined')
    			msg = msg + "\nStack trace:\n" + e.stack
    	}
    	if (typeof e === 'string') {
    	  msg = msg + 'Message: ' + e
    	}
      ErrorMessages.push(msg)
    }
    if ( typeof jQuery211 === 'function') {
      ErrorWaiting = false
      while(msg = ErrorMessages.pop()) jQuery211.post('/index.php?route=tool/errorlog', msg)
    } else {
      if (!ErrorWaiting || typeof e === 'undefined')
        ErrorWaiting = setTimeout(ErrorLog, 1000)
    }
}
var checkStartUp = function(resource, callback_ok, callback_fail, refresh) {
    var t
    if ( typeof refresh === 'undefined' )
        refresh = 500;
    for (r in resource) {
    	eval('t = typeof '+resource[r]+';')
	if ( t === 'undefined' ) {
            if ( refresh-- > 0) {
                window.setTimeout(function() { checkStartUp(resource, callback_ok, callback_fail, refresh) }, 10)
            } else {
                console.error('We have a loading error for: ' + resource[r])
                try { if (typeof callback_fail === 'function') callback_fail() }
                catch(e) { ErrorLog(new MyError('Error in "callback_fail" function for the loading of ' + resource[r], e)) }

            }
            return;
    	}
    }
    try { if (typeof callback_ok === 'function') callback_ok() }
    catch(e) { ErrorLog(new MyError('Error in "callback_ok" function for the loading of ' + resource[r], e)) }
}
</script>
<?php
  foreach ($links as $href => $link) {
    if (isset($link['rel'])) {
      if (!empty($link['type'])) {
        echo '<link rel="' . $link['rel'] . '" href="' . $href . '" type="' . $link['type'] . '" />';
      } else {
        echo '<link rel="' . $link['rel'] . '" href="' . $href . '" />';
      }
    }
  }
?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if (false/*$keywords*/) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<style><?php echo $style; ?></style>
<script>//<![CDATA[
<?php
  foreach ($styles as $style) {
    echo "LoadStyle.push(['" . $style['href'] . "', '" . $style['rel']  . "', '" . $style['media'] . "']);";
  }
  /*foreach ($links as $link) {
    echo "LoadStyle.push(['" . $link['href'] . "', '" . $link['rel']  . "']);";
  }*/
  foreach ($scripts as $script_file) {
    echo "LoadScript.push('" . $script_file  . "');";
  }
  echo $script;
?>;
var google_tag_params = {ecomm_pagetype: "other"};
//]]></script>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
</head>
<body class="<?php echo $class; ?>">

<div class="modal fade ajax" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="canvas">
        <div class="ajax-content"></div>
        <div class="wait-spinner">
          <svg class="spinner"><use xlink:href="#svg-spinner"/></svg>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade default" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><span aria-hidden="true"><svg><use xlink:href="#svg-close"/></svg></span><span class="sr-only">Закрыть</span></button>
      <div class="canvas">
        <div class="content"></div>
        <div class="wait-spinner">
          <svg class="spinner"><use xlink:href="#svg-spinner"/></svg>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="error-message" class="modal fade"  tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <span class="title"></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></span></button>
      </div>
      <div class="modal-body">
        <p class="text"></p>
        <button class="btn" type="button" data-dismiss="modal"><span aria-hidden="true">OK</span></button>
      </div>
    </div>
  </div>
</div>

<?php echo $locality; ?>

<nav id="advantage" class="_navbar _navbar-default navbar-fixed-bottom" style="border-top-width:0">
 <div class="container">
   <div class="grid">
     <div class="_col-xs-6 _col-sm-4 _col-md-3 delivery">
       <a title="Бесплатная доставка" href="/delivery" target="_blank">
        <svg><use xlink:href="#svg-delivery"></svg><div class="cap">
          <div class="title">Бесплатная доставка</div>
          <div>По всей России</div>
        </div>
       </a>
     </div>
     <div class="_col-xs-6 _col-sm-4 payment _col-md-3">
       <a title="Никакой предоплаты" href="/delivery" target="_blank">
         <svg><use xlink:href="#svg-payment"></svg><div class="cap"><div class="title">Никакой предоплаты!</div><div>Оплата при получении</div></div>
       </a>
     </div>
     <div class="_col-md-3 _hidden-sm _hidden-xs material"><a title="Только натуральная кожа" href="/natural-leather" target="_blank">
       <svg><use xlink:href="#svg-material"></svg><div class="cap"><div class="title">Только натуральная кожа</div><div>Никаких заменителей!</div></div>
     </a></div>
     <div class="_col-sm-4 _col-md-3 _hidden-xs warranty">
       <a title="Гарантия 365 дней" href="/the_certificates">
         <svg><use xlink:href="#svg-warranty"></svg><div class="cap"><div class="title">Гарантия 365 дней</div><div>30 дней на обмен / возврат</div></div>
       </a>
     </div>
   </div>
 </div>
</nav>

<nav id="menu2m">
  <div class="delivery-setup">
    <span class="marker"><svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"></svg></span>
      <a title="Выбрать регион доставки" role="button" data-toggle="modal" data-target="#SelectLocality" id="setup_locality"><span class="city"><?php echo $Locality ?></span></a>
      <? if ($tooltip) { ?>
      <div class="tooltip locality_suggestion">
        <div class="tooltip-inner">
          <div class="text-center">Ваш город - <?=$LocalityShort?>?</div>
          <div class="text-center tooltip-buttons">
          <button type="button" class="btn btn-default btn-xs success">Да</button>
          <button type="button" class="btn btn-default btn-xs select">Другой город</button>
          </div>
        </div>
      </div>
      <? } ?>
    </div>
  </div>
  <div class="phone text-right">
    <svg class="phone red bg-red"><use xlink:href="#svg-phone"></svg>
    <span class="new_call_phone_4 phone-number"><a rel="nofollow" href="tel:<?=$telephone_href ?>"><?=$telephone?></a></span>
  </div>
</nav>
<nav id="menu2">
  <div class="container-fluid">
    <div class="container">
      <div class="row">
        <div class="col-xs-12">
          <div class="grid">
            <div class="menu">
              <?php
              foreach ($informations as $information) {
                $add_class = '';
                echo '<div class="item"><a class=" ' . $add_class . '" href="' . $information['href'] . '">' . $information['title'] . '</a></div>';
              } ?>
              <div class="item"><a class="" href="/blog">Блог</a></div>
              <div class="item"><a class="" href="/reviews">Отзывы</a></div>
              <div class="item"><a class="" href="/contact">Контакты</a></div>
            </div>
            <div class="schedule">
              <svg viewBox="0 0 438.533 438.533"><use xlink:href="#svg-clock"></svg>
              <span><?php echo $open; ?></span>
            </div>
            <div class="sep"><svg width="10" height="40"><polygon points="-1,-1 10,20 -1,41"/></svg></div>
            <div class="new_call_phone_1">
              <a rel="nofollow" href="tel:<?=$telephone_href ?>">
              <div class="phone">
                <div class="icon-phone"><svg viewBox="0 0 348.077 348.077"><use xlink:href="#svg-phone"></svg></div>
                <div class="phone-number"><?=$telephone?><div class="note">бесплатный звонок</div></div>
              </div>
            </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>


<nav id="up-nav2m">
  <div class="smenu">
    <div class="menu_button" onClick="toggleMMenu(this)">
      <svg><path d="M2 3 l20 0 M2 8 l20 0 M2 13 l20 0"/></svg>
    </div>
    <div class="top_logo">
      <a href="<?=$home?>" title="<?=$name?>">
      <svg><use xlink:href="#svg-mainlogo_mob"></svg>
      </a>
    </div>
    <div class="cart_button"><?=$cart?></div>
  </div>
  <div class="dmenu">
    <div onClick="toggleMMenu(this)">Каталог товаров</div>
    <div class="delim"></div>
    <div><a class="red" href="/sale">Распродажа</a></div>
  </div>
</nav>


<nav id="up-nav2">
  <div class="container">
    <div class="grid">
      <div class="top-logo">
        <a href="<?=$home?>" title="<?=$name?>">
          <svg><use xlink:href="#svg-mainlogo_wide"></svg>
        </a>
      </div>
      <div class="delivery-setup flex">
        <div class="marker"><svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"></svg></div>
        <div class="region">
          <div><span class="text">Ваш регион доставки: </span><a title="Выбрать регион доставки" class="blue" role="button" data-toggle="modal" data-target="#SelectLocality" id="setup_locality"><span class="city"><?php echo $Locality ?></span><svg class="svg-caret blue"><use xlink:href="#svg-caret-down"></svg></a></div>
          <div class="pick-point"><?php echo $pick_point; ?></div>
        </div>
        <? if ($tooltip) { ?>
        <div class="tooltip locality_suggestion">
          <div class="tooltip-inner">
            <div class="text-center">Ваш город - <?=$LocalityShort?>?</div>
            <div class="text-center tooltip-buttons">
            <button type="button" class="btn btn-default btn-xs success">Да</button>
            <button type="button" class="btn btn-default btn-xs select">Другой город</button>
            </div>
          </div>
        </div>
        <? } ?>
      </div>
      <div class="top-other">
        <div class="search"><?=$search?></div>
        <div class="cart"><?=$cart?></div>
      </div>
    </div>
  </div>
</nav>

<div id="categories2m" class="fluid-container">
  <div class="close_m">
    <div onClick="toggleMMenu(this)">
      <svg><path d="M1 1 l12 11 M1 12 l12 -11"></svg>
    </div>
  </div>
  <div class="categories">
  <? foreach ($categories as $category) { ?>
      <? if (isset($category['ext_menu'])) { ?>
      <div class="item" onClick="toggleSMenu(this)"><div><?=$category['name']?></div>
        <div class="operate">
          <svg class="red sub_open"><path d="M1 1 l5 5 l5 -5"></svg>
          <svg class="red sub_close"><path d="M1 6 l5 -5 l5 5"></svg>
        </div>
      </div>
      <? } else { ?>
      <div class="item"><a class="<?=($category['mclass']?$category['mclass']:'')?>" href="<?=$category['href']?>"><?=$category['name']?></a></div>
      <? } ?>
    <? if (isset($category['ext_menu'])) { ?>
    <div class="sub_cat">
<?/*
      <div class="subcat subcat_unnamed">
        <div class="item"><a class="<?=($category['mclass']?$category['mclass']:'')?>" href="<?=$category['href']?>"><?=$category['name']?></a></div>
      </div>
*/?>
      <? foreach ($category['ext_menu'] as $name => $ext_menu) { ?>
        <? if ($name == 'unnamed') { ?>
          <div class="subcat subcat_unnamed">
          <? foreach($ext_menu as $link) { ?>
            <div class="item"><a href="<?=$link['href']?>"><?=$link['name']?></a></div>
          <? } ?>
          </div>
          <? continue; ?>
        <? } ?>
        <div class="subcat">
          <div class="title"><?=$name?></div>
          <? foreach($ext_menu as $menu) { ?>
          <div class="item"><a class="" href="<?=$menu['href']?>"><?=$menu['name']?></a></div>
        <? } ?>
        </div>
      <? } ?>
    </div>
    <? } ?>
  <? } ?>
  </div>
  <div class="mainmenu">
    <?php
    foreach ($informations as $information) {
      $add_class = '';
      echo '<div class="item"><a class=" ' . $add_class . '" href="' . $information['href'] . '">' . $information['title'] . '</a></div>';
    } ?>
    <div class="item"><a class="" href="/blog">Блог</a></div>
    <div class="item"><a class="" href="/reviews">Отзывы</a></div>
    <div class="item"><a class="" href="/contact">Контакты</a></div>
  </div>
</div>

<div id="categories2" class="fluid-container">
  <nav class="container">
    <div class="menu">
    <? foreach ($categories as $category) { ?>
        <div class="item">
          <? if (isset($category['ext_menu'])) { ?>
          <a data-category_id="<?=$category['category_id']?>" class="<?=($category['mclass']?$category['mclass']:'')?>" href="<?=$category['href']?>"><?=$category['name']?></a>
          <? } else { ?>
          <a class="<?=($category['mclass']?$category['mclass']:'')?>" href="<?=$category['href']?>"><?=$category['name']?></a>
          <? } ?>
        </div>
    <? } ?>
    </div>
    <div id="categories_ext">
    <? foreach ($categories as $category) { ?>
      <? if (isset($category['ext_menu'])) { ?>
      <div id="cat_ext_<?=$category['category_id']?>" class="cat_ext">
        <div class="grid">
          <? foreach ($category['ext_menu'] as $name => $ext_menu) { ?>
            <? if ($name == 'unnamed') { ?>
              <div class="subcat subcat_unnamed">
              <? foreach($ext_menu as $link) { ?>
                <div class="item"><a href="<?=$link['href']?>"><?=$link['name']?></a></div>
              <? } ?>
              </div>
              <? continue; ?>
            <? } ?>
            <div class="subcat">
              <div class="title"><?=$name?></div>
              <? foreach($ext_menu as $menu) { ?>
              <div class="item"><a class="" href="<?=$menu['href']?>"><?=$menu['name']?></a></div>
            <? } ?>
            </div>
          <? } ?>
          </div>
        </div>
        <? } ?>
    <? } ?>
    </div>
  </nav>
</div>

<script>

$(document).ready(function() {
  if ($(window).width() >= Defaults['screen_sm']) {
    $('#categories2 .menu .item')
    .on('click', function(e) {
      var $a = $('a[href]', e.target)
      if ($a.length > 0) window.location = $a.attr('href')
    })
    .on('mouseover', function(e) {
      var cid = $('a', this).attr('data-category_id');
          $c = $('#categories2'),
          $ce = $('#cat_ext_' + cid);
      $('.cat_ext').css({'display':'none'});
      $('#categories2 .menu .item').removeClass('active');
      if (!cid)
        return;
      if (!$c.hasClass('ext-opened')) {
        $ce.fadeIn(300);
        $(document).on('mousemove', menu_watch);
        $c.addClass('ext-opened');
      }
      $ce.css({'display':'block'});
      $(this).addClass('active');
    });
  }
});
<? if ($tooltip) { ?>
  $('.locality_suggestion button').on('click', function() {
    if ($(this).hasClass('success')) {
      $('.locality_suggestion').fadeOut(300)
    } else {
      $('.locality_suggestion').fadeOut(100);
      $('#SelectLocality').modal('show');
      ErrorLog('Changing the city activated');
    }
  })
  $(document).ready(function() {
    var t = $('.locality_suggestion')
    var d = function() {t.fadeOut(500,function(){t.remove()})}
    t.on('mouseover', function(){
      if (typeof window.region_tooltip_timer !== 'undefined') {
        clearTimeout(window.region_tooltip_timer)
        delete window.region_tooltip_timer
      }
    }).on('mouseout', function(){
      window.region_tooltip_timer = setTimeout(d, 7000)
    })
    window.region_tooltip_timer = setTimeout(d, 7000)
    setTimeout(function() {t.fadeIn(300)}, 1000)
  })
<? } ?>
</script>
