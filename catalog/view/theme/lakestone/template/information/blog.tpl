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
            <div class="content_wrap" itemscope itemtype="http://schema.org/Article">
              <link itemprop="publisher" href="lakestone-organisation" itemtype="http://schema.org/Organization" />
              <?php /*<link itemprop="author" href="lakestone-organisation" itemtype="http://schema.org/Organization" />*/?>
              <link itemprop="image" href="https://www.lakestone.ru/image/catalog/logo5.png" />
              <meta itemprop="datePublished" content="<?php echo $datetimePublished; ?>" />
              <meta itemprop="dateModified" content="<?php echo $datetimeModified; ?>" />
              <link itemprop="mainEntityOfPage" href="/blog"/>
              <?php echo $content_top; ?>
              <div class="title" itemprop="headline"><h1><?php echo $heading_title; ?></h1></div>
              <div class="text_canvas" itemprop="articleBody"><?php echo $description; ?></div>
              <div class="row">
                <div class="col-sm-6">
                  <?php if ($review_status and $rating > 0) { ?>
                  <div class="rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                    Рейтинг статьи:
                    <meta itemprop="ratingValue" content="<?=$rating?>" />
                    <meta itemprop="ratingCount" content="<?=$reviews_num?>" />
                    <p>
                      <span class="stars">
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($rating < $i) { ?>
                        <svg class="star"><use xlink:href="#svg-star"></svg>
                        <?php } else { ?>
                        <svg class="star full"><use xlink:href="#svg-star"></svg>
                        <?php } ?>
                        <?php } ?>
                      </span>
                    </p>
                  </div>
                  <? } ?>
                  <? if ($rating_href) { ?>
                  <div id="blog-rating">
                    <span>Эта статья полезна?</span>
                    <button class="btn btn-primary like">Да</button>
                    <button class="btn btn-primary unlike">Нет</button>
                  </div>
                  <? } ?>
                </div>
                <div class="col-sm-6">
                  <?php if ($author) { ?>
                  <div itemprop="author" class="text-right"><?php echo $author; ?></div>
                  <?php } ?>
                  <div class="text-right"><?php echo $datePublished; ?></div>
                </div>
              </div>


              <? if ($review_status) { ?>
                <ul class="nav nav-tabs nav-review" role="tablist">
                  <li role="presentation" class="active"><a href="<?=$review_href?>#tab-reviews" aria-controls="tab-reviews" role="tab" data-toggle="tab">Комментарии</a></li>
                  <li role="presentation" class="pull-right"><a href="<?=$review_href?>#tab-rules" aria-controls="tab-rules" role="tab" data-toggle="tab">Правила оформления комментариев</a></li>
                </ul>
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="tab-reviews">
                    <? if ($review_guest) { ?>
                      <div role="tabpanel" class="tab-pane" id="review_write">
                        <button class="btn btn-primary" onClick="$('#review_form').toggle()">Написать комментарий</button>
                        <div id="review_form" style="display:none">
                          <form class="form-horizontal" id="form-review">
                              <input type="hidden" name="review_type" value="2">
                              <div id="review"></div>
                              <?php if ($review_guest) { ?>
                              <div class="form-group required">
                                <div class="col-sm-12">
                                  <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                                  <input type="text" name="name" id="input-name" class="form-control" />
                                </div>
                              </div>
                              <div class="review">
                                Считаете эту статью полезной:
                              <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-primary">
                                  <input type="radio" name="rating" value="5" id="yes" autocomplete="off"> Да
                                </label>
                                <label class="btn btn-primary">
                                  <input type="radio" name="rating" value="1" id="no" autocomplete="off"> Нет
                                </label>
                              </div>
                              </div>
                              <div class="form-group required">
                                <div class="col-sm-12">
                                  <label class="control-label" for="input-review"><?php echo $entry_review; ?></label>
                                  <textarea name="text" rows="5" id="input-review" class="form-control"></textarea>
                                </div>
                              </div>
                              <div class="buttons clearfix">
                                <div class="pull-right">
                                  <button type="button" id="button-review" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_continue; ?></button>
                                </div>
                              </div>
                              <?php } ?>
                          </form>
                        </div>
                      </div>
                    <? } ?>
                    <div id="reviews"></div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="tab-rules"></div>
                </div>
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
<script>
$(window).ready(function() {
  $('#reviews').load('index.php?route=information/blog/reviews&blog_id=<?php echo $blog_id; ?>');
  $('#tab-rules').load('index.php?route=information/blog/review_rules');
  $('#blog-rating button').on('click', function() { blog_rating(this) })
  $('#button-review').on('click', function() {
    $.ajax({
      url: 'index.php?route=information/blog/write&blog_id=<?php echo $blog_id; ?>',
      type: 'post',
      dataType: 'json',
      data: $("#form-review").serialize(),
      beforeSend: function() {
        $('#button-review').button('loading');
      },
      complete: function() {
        $('#button-review').button('reset');
      },
      success: function(json) {
        $('.alert-success, .alert-danger').remove();

        if (json['error']) {
          $('#review').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
        }

        if (json['success']) {
          $('#review').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

          $('#form-review input[name=\'name\']').val('');
          $('#form-review textarea[name=\'text\']').val('');
        }
      }
    })
  });
});
var blog_rating = function(t) {
  var b = $('#blog-rating')
  console.log(t)
  $.getJSON('<?=$rating_href?>', {'token':'<?=$rating_token?>', 'blog_id':'<?=$blog_id?>','like':$(t).hasClass('like')});
  b.empty()
  b.append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Спасибо за ваш голос!</div>')
}
var like = function(t) {
  var id = $(t).attr('data-id'),
      tk = $(t).attr('data-token'),
      l = $(t).parent().find('.like_value'),
      ul = $(t).parent().find('.unlike_value')
  $.getJSON("<?=$review_like_href?>", {'review_id':id,'token':tk}, function(d) {
    l.text(d.like)
    ul.text(d.unlike)
  })
}
var unlike = function(t) {
  console.log(t)
  var id = $(t).attr('data-id'),
      tk = $(t).attr('data-token'),
      l = $(t).parent().find('.like_value'),
      ul = $(t).parent().find('.unlike_value')
  $.getJSON("<?=$review_unlike_href?>", {'review_id':id,'token':tk}, function(d) {
    l.text(d.like)
    ul.text(d.unlike)
  })
}
</script>
<?php echo $footer; ?>
