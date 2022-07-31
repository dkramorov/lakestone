<?php echo $header; ?>
<div class="container">
  <ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $breadcrumb['href']; ?>"><span itemprop="name"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <? if (!$column_left) { ?>
  <hr>
  <? } ?>
</div>
<div class="<? echo ($column_left ? 'container-fluid' : 'container')?>">
  <div class="row">
    <div class="panel-grey">
      <div class="container">
        <div class="row"><?php echo $column_left; ?>
          <?php if ($column_left && $column_right) { ?>
            <?php $class = 'col-sm-6 col-md-7'; ?>
          <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-9 col-md-10'; ?>
          <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
          <?php } ?>
          <div id="content" class="<?php echo $class; ?>">
            <div class="content_wrap">
              <?php echo $content_top; ?>
              <? if ($heading_title) { ?>
                <div class="title"><h1><?php echo $heading_title; ?></h1></div>
                <div class="text_canvas"><?php echo $description; ?></div>
              <? } ?>
              <?php echo $content_bottom; ?>
            </div>
          </div>
          <?php //echo $column_right; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
