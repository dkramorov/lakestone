<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-review" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-review" class="form-horizontal">

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-author"><?php echo $entry_author; ?></label>
            <div class="col-sm-10">
              <input type="text" name="author_name" value="<?php echo $author_name; ?>" placeholder="<?php echo $entry_author; ?>" id="input-author" class="form-control" />
              <?php if ($error_author) { ?>
              <div class="text-danger"><?php echo $error_author; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-date-review">Дата отзыва</label>
            <div class="col-sm-3">
              <div class="input-group datetime">
                <input type="text" name="date_review" value="<?php echo $date_review; ?>" placeholder="<?php echo $entry_date_review; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-review" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Рейтинг</label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($rating_value == 1) { ?>
                <input type="radio" name="rating_value" value="1" checked="checked" />
                1
                <?php } else { ?>
                <input type="radio" name="rating_value" value="1" />
                1
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_value == 2) { ?>
                <input type="radio" name="rating_value" value="2" checked="checked" />
                2
                <?php } else { ?>
                <input type="radio" name="rating_value" value="2" />
                2
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_value == 3) { ?>
                <input type="radio" name="rating_value" value="3" checked="checked" />
                3
                <?php } else { ?>
                <input type="radio" name="rating_value" value="3" />
                3
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_value == 4) { ?>
                <input type="radio" name="rating_value" value="4" checked="checked" />
                4
                <?php } else { ?>
                <input type="radio" name="rating_value" value="4" />
                4
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_value == 5) { ?>
                <input type="radio" name="rating_value" value="5" checked="checked" />
                5
                <?php } else { ?>
                <input type="radio" name="rating_value" value="5" />
                5
                <?php } ?>
              </label>
              <?php if ($error_rating) { ?>
              <div class="text-danger"><?php echo $error_rating; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-author">Рейтинг текстом</label>
            <div class="col-sm-10">
              <input type="text" name="rating_text" value="<?php echo $rating_text; ?>" placeholder="Рейтинг текстом" id="input-author" class="form-control" />
              <?php if ($error_author) { ?>
              <div class="text-danger"><?php echo $error_author; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-author">Регион</label>
            <div class="col-sm-10">
              <input type="text" name="author_region" value="<?php echo $author_region; ?>" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-author">Аватарка</label>
            <div class="col-sm-10">
              <input type="text" name="author_avatar_link" value="<?php echo $author_avatar_link; ?>" placeholder="ссылка на аватарку" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-text">Комментарий</label>
            <div class="col-sm-10">
              <textarea name="comment" cols="60" rows="8" placeholder="<?php echo $entry_text; ?>" id="input-text" class="form-control"><?php echo $comment; ?></textarea>
              <?php if ($error_text) { ?>
              <div class="text-danger"><?php echo $error_text; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-values">Достоинства</label>
            <div class="col-sm-10">
              <textarea name="values" cols="60" rows="8" placeholder="<?php echo $entry_text; ?>" id="input-values" class="form-control"><?php echo $values; ?></textarea>
              <?php if ($error_text) { ?>
              <div class="text-danger"><?php echo $error_text; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-defects">Недостатки</label>
            <div class="col-sm-10">
              <textarea name="defects" cols="60" rows="8" placeholder="<?php echo $entry_text; ?>" id="input-defects" class="form-control"><?php echo $defects; ?></textarea>
              <?php if ($error_text) { ?>
              <div class="text-danger"><?php echo $error_text; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <input type="hidden" name="source_id" value="1">

        </form>

      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
//--></script>
</div>
<?php echo $footer; ?>