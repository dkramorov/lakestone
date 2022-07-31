<div id="banner_smart<?php echo $module; ?>" class="banner_smart banner_smart_front_middle">
  <? $banner = $banners[0]; ?>
  <? if (isset($banner)) { ?>
  <div class="banner banner_darken banner_darken_button banner_left" <? if ($banner['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner['link'] . '\'"';}?>>
    <img style="height:<?=$banner['height']?>px" src="/image/empty.png" data-src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" class="img-responsive lazyLoad" />
    <? if ($banner['title']) { ?>
    <div class="title"><?=$banner['title']?></div>
    <? } ?>
    <? if ($banner['text']) { ?>
    <div class="text"><?=$banner['text']?></div>
    <? } ?>
    <? if ($banner['button']) { ?>
    <div class="button"><div class="button-black hover-effect07" role="button" <? if ($banner['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner['link'] . '\'"';}?>><?=$banner['button']?></div></div>
    <? } ?>
  </div>
  <? } ?>
  <? if (isset($banner1)) { ?>
  <div class="banner banner_darken banner_darken_button banner_right banner_right_top"
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
      <div class="button"><div class="button-black hover-effect07" role="button" <? if ($banner2['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner2['link'] . '\'"';}?>><?=$banner1['button']?></div></div>
    <? } ?>
    <? if ($banner1['video']) { ?>
      <div class="button-video"><div role="button" onClick="showVideo('<?=$banner1['video']['vid']?>')">
      <svg viewBox="0 0 400 400">
        <filter id="dropshadow" height="2" width="2" x="-0.4" y="-0.4">
          <feGaussianBlur in="SourceAlpha" stdDeviation="50"/>
          <feOffset dx="0" dy="0" result="offsetblur"/>
          <feComponentTransfer>
            <feFuncA type="linear" slope="0.7"/>
          </feComponentTransfer>
          <feMerge>
            <feMergeNode/> <!-- this contains the offset blurred image -->
            <feMergeNode in="SourceGraphic"/> <!-- this contains the element that the filter is applied to -->
          </feMerge>
        </filter>
        <circle cx="200" cy="200" r="99" stroke="white" fill="white" style="filter:url(#dropshadow)" />
        <circle cx="200" cy="200" r="80" stroke="#ff5155" fill="#ff5155" />
        <path d="M190,175 l0,50 l35,-25" stroke="white" fill="white"/>
      </svg>
      </div></div>
    <? } ?>
  </div>
  <? } ?>
  <? if (isset($banner2)) { ?>
  <div class="banner banner_darken banner_darken_button banner_right banner_right_bottom"
  <? if ($banner2['link']) { ?>
    <? if ($banner2['video']) { ?>
      style="cursor:pointer" onClick="showVideo('<?=$banner2['video']['vid']?>')"
    <? } else { ?>
      style="cursor:pointer" onClick="document.location = '<?=$banner2['link']?>'"
    <? } ?>
  <? } ?>
  >
    <img style="height:<?=$banner2['height']?>px" src="/image/empty.png" data-src="<?php echo $banner2['image']; ?>" alt="<?php echo $banner2['title']; ?>" class="img-responsive lazyLoad" />
    <? if ($banner2['title']) { ?>
    <div class="title"><?=$banner2['title']?></div>
    <? } ?>
    <? if ($banner2['text']) { ?>
    <div class="text"><?=$banner2['text']?></div>
    <? } ?>
    <? if ($banner2['button']) { ?>
      <div class="button"><div class="button-black hover-effect07" role="button"
        <? if ($banner2['video']) { ?> onClick="showVideo('<?=$banner2['video']['vid']?>')"
        <? } elseif ($banner2['link']) { ?> style="cursor:pointer" onClick="document.location='<?=$banner2['link']?>'"
        <? } ?>
      ><?=$banner2['button']?></div></div>
    <? } ?>
    <? if ($banner2['video']) { ?>
      <div class="button-video"><div role="button" onClick="showVideo('<?=$banner2['video']['vid']?>')">
      <svg viewBox="0 0 400 400">
        <filter id="dropshadow" height="2" width="2" x="-0.4" y="-0.4">
          <feGaussianBlur in="SourceAlpha" stdDeviation="50"/>
          <feOffset dx="0" dy="0" result="offsetblur"/>
          <feComponentTransfer>
            <feFuncA type="linear" slope="0.7"/>
          </feComponentTransfer>
          <feMerge>
            <feMergeNode/> <!-- this contains the offset blurred image -->
            <feMergeNode in="SourceGraphic"/> <!-- this contains the element that the filter is applied to -->
          </feMerge>
        </filter>
        <circle cx="200" cy="200" r="99" stroke="white" fill="white" style="filter:url(#dropshadow)" />
        <circle cx="200" cy="200" r="80" stroke="#ff5155" fill="#ff5155" />
        <path d="M190,175 l0,50 l35,-25" stroke="white" fill="white"/>
      </svg>
      </div></div>
    <? } ?>
  </div>
  <? } ?>
</div>
