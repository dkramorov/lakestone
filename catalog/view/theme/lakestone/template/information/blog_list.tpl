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
            <div class="content_wrap"><?php echo $content_top; ?>
              <div class="text-center"><h1><?=$heading_title?></h1></div>
              <div itemprop="mainEntity" itemscope itemtype="http://schema.org/ItemList" class="row">
                  <?php foreach ($articles as $pos => $article) { ?>
                  <div class="col-sm-12 clearfix" itemprop="itemListElement" itemscope itemtype="http://schema.org/Article">
                    <meta itemprop="datePublished" content="<?=$article['isodate']?>" />
                    <meta itemprop="dateModified" content="<?=$article['isomod']?>" />
                    <link itemprop="mainEntityOfPage" href="/" />
                    <meta itemprop="position" content="<?=$pos?>" />
                    <link itemprop="publisher" href="lakestone-organisation" itemtype="http://schema.org/Organization" />
                    <?php if ($article['image']) { ?>
                      <a href="<?php echo $article['url'] ?>"><img itemprop="image" class="pull-left item_image" src="<?php echo $article['image'] ?>" alt="<?php echo $article['title']?>"></a>
                    <?php } ?>
                    <div><span itemprop="dateCreated"><?php echo $article['date'] ?></span>
                    <?php if ($author) { ?> - <span itemprop="author"><?php echo $author ?></span><?php } ?>

                      <?php if ($review_status and $article['rating'] > 0) { ?>
                      <span class="rating pull-right" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        <meta itemprop="ratingValue" content="<?=$article['rating']?>" />
                        <meta itemprop="ratingCount" content="<?=$article['reviews']?>" />
                          <?php for ($i = 1; $i <= 5; $i++) { ?>
                          <?php if ($article['rating'] < $i) { ?>
                          <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                          <?php } else { ?>
                          <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                          <?php } ?>
                          <?php } ?>
                      </span>
                      <?php } ?>

                    </div>
                    <div class="title"><a itemprop="url" class="black " href="<?php echo $article['url'] ?>"><span itemprop="headline"><?php echo $article['title']?></span></a></div>
                    <div>
                      <a class="black " href="<?php echo $article['url'] ?>">
                        <div itemprop="description"><?php echo $article['announce'] ?></div>
                      </a>
                    </div>
                    <?php /*
                    <div class="link">
                      <a class="black " href="<?php echo $article['url'] ?>">Подробнее <span class="glyphicon glyphicon-menu-right"></span></a>
                    </div>
                    */ ?>
                  </div>
                  <?php } ?>
              </div>
              <div class="row">
                <div class="blog-pagination">
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
