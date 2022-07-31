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
<div class="<? echo ($column_left ? 'container-fluid' : '')?>">
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
              <div class="text-center"><h1><?=$heading_title?></h1></div>
              <div class="row">
                  <?php foreach ($articles as $article) { ?>
                  <div class="col-sm-12 clearfix">
                    <?php if ($article['image']) { ?>
                      <a href="<?php echo $article['url'] ?>"><img class="pull-left item_image" src="<?php echo $article['image'] ?>" alt="<?php echo $article['title']?>"></a>
                    <?php } ?>
                    <div><?php echo $article['date'] ?></div>
                    <div class="title"><a class="black" href="<?php echo $article['url'] ?>"><?php echo $article['title']?></a></div>
                    <div>
                      <a class="black" href="<?php echo $article['url'] ?>">
                        <div><?php echo $article['announce'] ?></div>
                      </a>
                    </div>
                    <?php /*
                    <div class="link">
                      <a class="black tr2orange" href="<?php echo $article['url'] ?>">Подробнее <span class="glyphicon glyphicon-menu-right"></span></a>
                    </div>
                    */ ?>
                  </div>
                  <?php } ?>
              </div>
              <div class="row">
                <div class="news-pagination">
                  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
              </div>
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
