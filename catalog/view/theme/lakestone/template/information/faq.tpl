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
              <div class="title"><h1><?php echo $heading_title; ?></h1></div>
              <?php echo $content_top; ?>
              <div class="text_canvas-faq">
                <?php foreach ($faq_cats as $i => $faq_cat) { ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-2 col-md-1"><img class="cat_image" src="/image/<?=$faq_cat['image']?>" alt="<?=$faq_cat['title']?>"></div>
                  <div class="col-xs-9 col-sm-10 col-md-11">
                    <div class="cat_title"><?=$faq_cat['title']?></div>
                    <div class="row">
                      <?php foreach ($faq_cat['faqs'] as $faq) { ?>
                      <div class="col-sm-4"><div class="faq-item"><a data-toggle="modal" data-target="#faq_modal_<?=$faq['id']?>"><?=$faq['title']?></a></div></div>
                    <? }?>
                  </div>
                  </div>
                </div>
                <? if ($i < sizeof($faq_cats) - 1) { ?><hr><? } ?>
                <? } ?>
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
<script>
var SendMessage = function(b) {
  var $m = $(b).parents('.modal')
  var $f = $('form', $m)
  var $msg = $('.result-message', $f)
  // $('input, textarea', $f).each(function() {
  //   if ($(this).attr('type') === 'hidden') return
  //   if (!$(this).val()) $(this).parent().addClass('has-error')
  //   else $(this).parent().removeClass('has-error')
  // })
  $.ajax({
    url: 'index.php?route=information/contact',
    type: 'post',
    dataType: 'json',
    data: $f.serialize(),
    beforeSend: function() {
      $('#button-review').button('loading');
      $msg.empty()
    },
    complete: function() {
      $('#button-review').button('reset');
    },
    success: function(json) {
      $('.alert-success, .alert-danger').remove();

      if (json['error']) {
        Object.keys(json['error']).forEach(function(v, k) {
          $msg.append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'][v] + '</div>')
        })
      }

      if (json['status'] === 'OK') {
        $msg.append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['text'] + '</div>');

        $('input[type!=\'hidden\']', $f).val('');
        $('textarea', $f).val('');
        $m.modal('hide')
      }
    }
  })
}
</script>
<div id="faq_modal_question" class="modal fade faq" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></button>
        <div class="title">Контакт-форма</div>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-sm-12">
                  <div class="text">Заполните форму для отправки вопрос. Наш консультант ответ Вам в ближайшее время.</div>
              </div>
          </div>
          <form>
          <div class="result-message"></div>
            <input type="hidden" name="json" value="true">
          <div class="row">
              <div class="col-sm-6"><input name="name" placeholder="Ваше имя:" class="form-control"></div>
              <div class="col-sm-6"><input name="phone" placeholder="Номер телефона:" class="form-control"></div>
          </div>
          <div class="row">
              <div class="col-sm-12"><input name="email" placeholder="Ваш E-mail:" class="form-control"></div>
          </div>
          <div class="row">
              <div class="col-sm-12"><textarea name="enquiry" placeholder="Ваш комментарий" class="form-control"></textarea></div>
          </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-blue" onclick="SendMessage(this)">Отправить заявку</button>
        <p class="disclaimer text-center">Нажимая на кнопку <q>Отправить заявку</q>, вы принимаете условия <a class="blue" target="_blank" href="/publichnaya-oferta">Публичной оферты</a></p>
      </div>
    </div>
  </div>
</div>
<?php
foreach ($faq_cats as $i => $faq_cat) {
  foreach ($faq_cat['faqs'] as $faq) { ?>
    <div id="faq_modal_<?=$faq['id']?>" class="modal fade faq faq-question" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg><use xlink:href="#svg-close"></svg></button>
            <div class="title"><?=$faq['title']?></div>
          </div>
          <div class="modal-body"><?=$faq['text']?></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-blue" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
<? } }?>
<?php echo $footer; ?>
