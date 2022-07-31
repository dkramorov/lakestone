<div id="banner_smart<?php echo $module; ?>" class="banner_smart banner_smart_front">
  <div class="_owl-carousel banner banner_smart_slider">
    <?php foreach ($banners as $banner) { ?>
    <div class="banner_darken item" <? if ($banner['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner['link'] . '\'"';}?>>
      <img style="height:<?=$banner['height']?>px" src="/image/empty.png" data-src="<?=$banner['image']?>" alt="<?=$banner['title']?>" class="img-responsive _owl-lazy lazyLoad" />
      <? if ($banner['title']) { ?>
      <div class="title"><?=$banner['title']?></div>
      <? } ?>
      <? if ($banner['text']) { ?>
      <div class="text"><?=$banner['text']?></div>
      <? } ?>
      <? if ($banner['button']) { ?>
      <div class="button"><div role="button" <? if ($banner['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner['link'] . '\'"';}?>><?=$banner['button']?></div></div>
      <? } ?>
    </div>
    <?php } ?>
  </div>
  <div class="banner banner_darken banner_darken_button banner_smart_right_top"
  <? if ($banner1['link']) { ?>
    <? if ($banner1['video']) { ?>
      style="cursor:pointer" onClick="showVideo('<?=$banner1['video']['vid']?>')"
    <? } else { ?>
      style="cursor:pointer" onClick="document.location = '<?=$banner1['link']?>'"
    <? } ?>
  <? } ?>
  >
    <img style="height:<?=$banner1['height']?>px" src="/image/empty.png" data-src="<?php echo $banner1['image']; ?>" alt="<?php echo $banner1['title']; ?>" class="img-responsive lazyLoad" />
    <? if ($banner1['title']) { ?>
    <div class="title"><?=$banner1['title']?></div>
    <? } ?>
    <? if ($banner1['text']) { ?>
    <div class="text"><?=$banner1['text']?></div>
    <? } ?>
      <? if ($banner1['button']) { ?>
      <div class="button"><div role="button" <? if ($banner1['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner1['link'] . '\'"';}?>><?=$banner1['button']?></div></div>
      <? } ?>
  </div>
  <div class="banner banner_darken banner_manual banner_darken_button banner_smart_right_bottom" style="height:<?=$banner2['height']?>px"
  <? if ($banner2['link']) { ?>
    <? if ($banner2['video']) { ?>
      style="cursor:pointer" onClick="showVideo('<?=$banner2['video']['vid']?>')"
    <? } else { ?>
      style="cursor:pointer" onClick="document.location = '<?=$banner2['link']?>'"
    <? } ?>
  <? } ?>
  >
    <img style="height:<?=$banner2['height']?>px" src="/image/empty.png" data-src="<?php echo $banner2['image']; ?>" alt="<?php echo $banner2['title']; ?>" class="_img-responsive lazyLoad" />
    <? if ($banner2['title']) { ?>
    <div class="title"><?=$banner2['title']?></div>
    <? } ?>
    <? if ($banner2['text']) { ?>
    <div class="text"><?=$banner2['text']?></div>
    <? } ?>
    <? if ($banner2['button']) { ?>
      <div class="button"><div role="button" <? if ($banner2['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner2['link'] . '\'"';}?>><?=$banner2['button']?></div></div>
    <? } ?>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
  LazyLoadObject($('#banner_smart<?=$module?>'), function(){
    var $s = $('#banner_smart<?=$module?> .banner_smart_slider')
    $s.width($s.width()-1)
      .addClass('owl-carousel')
      .owlCarousel({
    	items: 1,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
    	nav: true,
    	rewind: true,
    	slideTransition: 'fade',
      animateOut: 'fadeOut',
      // lazyLoad: true,
      navText: ['<svg class="owl_button"><use xlink:href="#svg-angle-left"/></svg>', '<svg class="owl_button"><use xlink:href="#svg-angle-right"/></svg>'],
      onLoadedLazy: function (e) {
        var i = e.target
        setTimeout(function(){owlResizer(i)}, 100)
      },
    })
  })
  // LazyLoadObject($('#banner_smart<?=$module?> img.lazyLoad'), function() {
  //   var i = this.target
  //   setTimeout(function(){owlResize(i)}, 100)
  // })
})
// var owlResize = function(img) {
//   $(img).parents('.owl-carousel').height($(img).height())
// }
--></script>
