<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="text"></span> <svg class="svg-angle red"><use xlink:href="#svg-angle"></svg>
  </button>
  <ul class="dropdown-menu">
    <ul class="scrolled_items">
      <? foreach ($DeliveryMethods as $method) { ?>
      <li data-type="<?=$method['method']?>"><?=$method['title']?></li>
      <? } ?>
    </ul>
  </ul>
</div>
