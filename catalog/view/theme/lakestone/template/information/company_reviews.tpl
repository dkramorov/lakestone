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
            <?php $class = 'col-sm-6'; ?>
          <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-10'; ?>
          <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
          <?php } ?>
          <div id="content" class="<?php echo $class; ?>">
            <div class="content_wrap">
              <?php echo $content_top; ?>
              <div class="reviews-top clearfix"><h1>Отзывы о нас с <a href="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/386426/reviews">Яндекс.Маркет</a></h1>
              <a href="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/386426/reviews">Оставить отзыв</a></div>
              <div class="pull-right">
                  <div class="rait-informer">
                <a href="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/386426/reviews" target="_blank"><img src="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2507/*https://grade.market.yandex.ru/?id=386426&action=image&size=2" style="width:150px;height:101px;border:none" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете" /></a>
              </div></div>
              <?php foreach ($reviews as $num => $review) { ?>
              <div class="row item review">
                  <div class="col-sm-2" itemscope itemtype="http://schema.org/Review">
                    <link itemprop="itemReviewed" href="lakestone-organisation" itemtype="http://schema.org/Organization" />
                    <div><?if (isset($review['author']['image'])) { ?>
                      <img src="<?=$review['author']['image']?>" alt="avatar">
                        <? } else { ?>
                            <div class="no-photo"></div>
                      <? } ?>
                    </div>
                  </div>
                  <div class="col-sm-<? echo $num == 0 ? '7' : '10' ?>">
                      <div class="details">
                          <div itemprop="author"><?=$review['author']['name']?></div>
                          <div><?=$review['region']?></div>
                          <div><?=$review['date']?></div>
                      </div>
                      <div class="rev-body" itemprop="reviewBody">
                        <div><?=$review['text']?></div>
                        <? if ($review['pros']) { ?>
                          <div><strong class="pros">Достоинства:</strong> <?=$review['pros']?></div>
                        <? } ?>
                        <? if ($review['cons']) { ?>
                          <div><strong class="cons">Недостатки:</strong> <?=$review['cons']?></div>
                        <? } ?>
                      </div>
                  </div>
              </div>
              <?php } ?>
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