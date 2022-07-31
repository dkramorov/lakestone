<div id="banner_smart<?php echo $module; ?>" class="banner_smart_technics">
      <div class="maintitle">
        <div class="line-through"></div>
        <div class="plaintext"><?=$maintitle?></div>
      </div>
      <div class="plaintext description"><?=$article?></div>
  <div class="banners">
      <? $banner = $banners[0] ?>
      <? if (isset($banner)) { ?>
      <div class="banner banner_left" <? if ($banner['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner['link'] . '\'"';}?>>
        <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" data-src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" class="img-responsive lazyLoad" style="/*width:<?=$banner['width']?>px;*/height:<?=$banner['height']?>px" />
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
    <? } ?>
      <? if (isset($banner1)) { ?>
      <div class="banner banner_center" <? if ($banner1['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner1['link'] . '\'"';}?>>
        <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" data-src="<?php echo $banner1['image']; ?>" alt="<?php echo $banner1['title']; ?>" class="img-responsive lazyLoad" style="/*width:<?=$banner1['width']?>px;*/height:<?=$banner1['height']?>px" />
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
      <? } ?>
      <? if (isset($banner2)) { ?>
      <div class="banner banner_right" <? if ($banner2['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner2['link'] . '\'"';}?>>
        <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" data-src="<?php echo $banner2['image']; ?>" alt="<?php echo $banner2['title']; ?>" class="img-responsive lazyLoad" style="/*width:<?=$banner2['width']?>px;*/height:<?=$banner2['height']?>px" />
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
      <? } ?>
  </div>
</div>
