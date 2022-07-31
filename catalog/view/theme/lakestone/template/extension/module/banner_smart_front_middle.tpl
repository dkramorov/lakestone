<div id="banner_smart<?php echo $module; ?>" class="banner_smart_front_middle">
  <div class="row">
    <div class="col-sm-5">
      <? $banner = $banners[0] ?>
      <? if (isset($banner)) { ?>
      <div class="banner banner_left" <? if ($banner['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner['link'] . '\'"';}?>>
        <img data-src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" class="img-responsive lazyLoad" style="/*width:<?=$banner['width']?>px;*/height:<?=$banner['height']?>px" />
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
      <?php } ?>
    </div>
    <div class="col-sm-7">
      <div class="row">
        <div class="col-xs-12">
          <? if (isset($banner1)) { ?>
          <div class="banner banner_right banner_right_top"
          <? if ($banner1['link']) { ?>
            <? if ($banner1['video']) { ?>
              style="cursor:pointer" onClick="showVideo('<?=$banner1['video']['vid']?>')"
            <? } else { ?>
              style="cursor:pointer" onClick="document.location = '<?=$banner1['link']?>'"
            <? } ?>
          <? } ?>
          >
            <img data-src="<?php echo $banner1['image']; ?>" alt="<?php echo $banner1['title']; ?>" class="img-responsive lazyLoad" style="/*width:<?=$banner1['width']?>px;*/height:<?=$banner1['height']?>px" />
            <? if ($banner1['title']) { ?>
            <div class="title"><?=$banner1['title']?></div>
            <? } ?>
            <? if ($banner1['text']) { ?>
            <div class="text"><?=$banner1['text']?></div>
            <? } ?>
              <? if ($banner1['button']) { ?>
              <div class="button"><div class="title1" role="button" <? if ($banner1['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner1['link'] . '\'"';}?>><?=$banner1['button']?></div></div>
              <? } ?>
          </div>
          <? } ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12"><div class="delim"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <? if (isset($banner2)) { ?>
          <div class="banner banner_right banner_right_bottom"
          <? if ($banner2['link']) { ?>
            <? if ($banner2['video']) { ?>
              style="cursor:pointer" onClick="showVideo('<?=$banner2['video']['vid']?>')"
            <? } else { ?>
              style="cursor:pointer" onClick="document.location = '<?=$banner2['link']?>'"
            <? } ?>
          <? } ?>
          >
            <img data-src="<?php echo $banner2['image']; ?>" alt="<?php echo $banner2['title']; ?>" class="img-responsive lazyLoad" style="/*width:<?=$banner2['width']?>px;*/height:<?=$banner2['height']?>px" />
            <? if ($banner2['title']) { ?>
            <div class="title"><?=$banner2['title']?></div>
            <? } ?>
            <? if ($banner2['text']) { ?>
            <div class="text"><?=$banner2['text']?></div>
            <? } ?>
            <? if ($banner2['button']) { ?>
              <div class="button"><div class="button-black hover-effect07" role="button" <? if ($banner2['link']) { echo 'style="cursor:pointer" onClick="document.location = \'' . $banner2['link'] . '\'"';}?>><?=$banner2['button']?></div></div>
            <? } ?>
          </div>
          <? } ?>
        </div>
      </div>
    </div>
  </div>
</div>
