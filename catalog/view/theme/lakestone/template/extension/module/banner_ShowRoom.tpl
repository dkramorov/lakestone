<? if ($Locality == 'г. Москва') { ?>
<div id="banner<?php echo $module; ?>" class="banner-ShowRoom">
<div class="panel description">
  <?php foreach ($banners as $banner) { ?>
  <h5><?=$banner['title']?></h5>
  <div class="panel-text"><?=$banner['text']?></div>
  <div class="address">
    <svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"></svg><?=$banner['button']?></div>
  </div>
  <div class="image"><img src="<?=$banner['image']?>" class="img-responsive" alt="show room"></div>
  <? } ?>
</div>
<? } ?>
