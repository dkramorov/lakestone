<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <!--div class="pull-right">
        <a href="<?php echo $download; ?>" data-toggle="tooltip" title="<?php echo $button_download; ?>" class="btn btn-primary"><i class="fa fa-download"></i></a>
        <a onclick="confirm('<?php echo $text_confirm; ?>') ? location.href='<?php echo $clear; ?>' : false;" data-toggle="tooltip" title="<?php echo $button_clear; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i></a>
      </div-->
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-download"></i> Импорт отзывов</h3>
      </div>
      <div class="panel-body">
        <ol>
          <li><?=$text_target?>: <a target="yamarket_review" href="<?=$target_url?>"><?=$target_url?></a></li>
          <li>Откройте в браузере панель разработчика, нажав на кнопку F12, и выберите панель Inspector</li>
          <li>В поле поиска "Search HTML" задайте идентификатор нужного блока: <b>scroll-to-reviews-list</b></li>
          <li>Последовательно нажимая Enter, найдите нужный блок: <a href="/image/admin/yamarket.png">"<b>&lt;div id="scroll-to-reviews-list"&gt;</b></a></li>
          <li>Кликните на него правой кнопкой мышки и выберите последовательно в контекстном меню: <a href="/image/admin/yamarket2.png">"<b>Copy -&gt; Outer HTML</b></a></li>
          <li>Вставьте содержимое скопированного блока в поле ниже: <b>Поле для импорта HTML</b></li>
          <li>Если необходимо, посмотрите небольшой <a href="/image/admin/yamarket.mp4">видео-ролик</a>, демонстрирующий процесс</li>
          <li>Нажмите кнопку "парсить данные"</li>
          <li>Нажмите кнопку "очистить поле импорта"</li>
          <li>Перейдите на <a target="yamarket_review" href="<?=$target_url?>">странице отзывов</a> на нужную страницу и повторите весь процесс</li>
        </ol>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-faq" class="form-horizontal">
          <div class="form_control">
            <label><?=$text_input?></label>
            <textarea name="input_html" wrap="off" rows="15" class="form-control"><?=$input_html?></textarea>
          </div>
          <button type="submit"><?=$button_parsing?></button>
          <button type="button" onClick="$('textarea[name=input_html]').val('')"><?=$button_reset?></button>
        </form>
      </div>
      <? if (!empty($reviews)) { ?>
        <? foreach ($reviews as $review) { ?>
          <pre><?var_dump($review)?></pre>
        <? } ?>
      <? } ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>
