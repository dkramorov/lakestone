<div class="information-sidebar">
  <?php foreach ($sidebar as $item) { ?>
    <? if ($item['type'] == 1) { ?>
      <?php if ($item['active']) { ?>
      <a href="<?php echo $item['href']; ?>"><div class="item active"><?php echo $item['title']; ?></div></a>
      <?php } else { ?>
      <a href="<?php echo $item['href']; ?>"><div class="item "><?php echo $item['title']; ?></div></a>
      <?php } ?>
    <? } else { ?>
      <div class="item title"><?php echo $item['title']; ?></div>
    <? } ?>
  <?php } ?>
</div>
