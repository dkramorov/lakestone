<?php if ($modules) { ?>
<aside id="column-left" class="<?=(isset($column_left_size)?$column_left_size:'col-sm-3')?> hidden-xs">
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
</aside>
<?php } ?>
