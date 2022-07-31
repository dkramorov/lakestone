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
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-author"><?php echo $entry_author; ?></label>
            <div class="col-sm-10">
              <input type="text" name="author" value="<?php echo $author; ?>" placeholder="<?php echo $entry_author; ?>" id="input-author" class="form-control" />
              <?php if ($error_author) { ?>
              <div class="text-danger"><?php echo $error_author; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-product"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_product; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="product" value="<?php echo $product; ?>" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
              <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
              <?php if ($error_product) { ?>
              <div class="text-danger"><?php echo $error_product; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-text"><?php echo $entry_text; ?></label>
            <div class="col-sm-10">
              <textarea name="text" cols="60" rows="8" placeholder="<?php echo $entry_text; ?>" id="input-text" class="form-control"><?php echo $text; ?></textarea>
              <?php if ($error_text) { ?>
              <div class="text-danger"><?php echo $error_text; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
          <? if ($images) { ?>
          <div class="col-sm-2 text-right"><b>Фотографии</b></div>
          <div id="UploadedFiles" class="col-sm-10">
            <div class="row">
            <? foreach ($images as $image) { ?>
            <div class="col-sm-2 image"><img onclick="viewImage(this)" src="<?=$image['thumb']?>"  data-image="<?=$image['popup']?>" role="button">
              <input name="image_status[<?=$image['image_id']?>]" value="1" type="checkbox" title="включена" <?=($image['status']==1 ? 'checked':'')?>>
            </div>
            <? } ?>
            </div>
          </div>
          <? } ?>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-answer"><?php echo $entry_answer; ?></label>
            <div class="col-sm-10">
              <textarea name="answer" cols="60" rows="8" placeholder="<?php echo $entry_answer; ?>" id="input-answer" class="form-control"><?php echo $answer; ?></textarea>
              <?php if ($error_answer) { ?>
              <div class="text-danger"><?php echo $error_answer; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-answer_status"><?php echo $entry_answer_status; ?></label>
            <div class="col-sm-10">
              <select name="answer_status" id="input-answer_status" class="form-control">
                <?php if ($answer_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-review_type"><?php echo $entry_review_type; ?></label>
            <div class="col-sm-10">
              <select name="review_type" id="input-review_type" class="form-control">
                <option <? echo ($review_type == '0' ? 'selected="yes"' : '' ) ?> value="0">Отзыв</option>
                <option <? echo ($review_type == '1' ? 'selected="yes"' : '' ) ?> value="1">Вопрос</option>
                <option <? echo ($review_type == '2' ? 'selected="yes"' : '' ) ?> value="2">Отзыв на статью</option>
              </select>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-rating"><?php echo $entry_rating; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($rating == 1) { ?>
                <input type="radio" name="rating" value="1" checked="checked" />
                1
                <?php } else { ?>
                <input type="radio" name="rating" value="1" />
                1
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating == 2) { ?>
                <input type="radio" name="rating" value="2" checked="checked" />
                2
                <?php } else { ?>
                <input type="radio" name="rating" value="2" />
                2
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating == 3) { ?>
                <input type="radio" name="rating" value="3" checked="checked" />
                3
                <?php } else { ?>
                <input type="radio" name="rating" value="3" />
                3
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating == 4) { ?>
                <input type="radio" name="rating" value="4" checked="checked" />
                4
                <?php } else { ?>
                <input type="radio" name="rating" value="4" />
                4
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating == 5) { ?>
                <input type="radio" name="rating" value="5" checked="checked" />
                5
                <?php } else { ?>
                <input type="radio" name="rating" value="5" />
                5
                <?php } ?>
              </label>
              <?php if ($error_rating) { ?>
              <div class="text-danger"><?php echo $error_rating; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-rating_photo"><?php echo $entry_rating_photo; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($rating_photo == 1) { ?>
                <input type="radio" name="rating_photo" value="1" checked="checked" />
                1
                <?php } else { ?>
                <input type="radio" name="rating_photo" value="1" />
                1
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_photo == 2) { ?>
                <input type="radio" name="rating_photo" value="2" checked="checked" />
                2
                <?php } else { ?>
                <input type="radio" name="rating_photo" value="2" />
                2
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_photo == 3) { ?>
                <input type="radio" name="rating_photo" value="3" checked="checked" />
                3
                <?php } else { ?>
                <input type="radio" name="rating_photo" value="3" />
                3
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_photo == 4) { ?>
                <input type="radio" name="rating_photo" value="4" checked="checked" />
                4
                <?php } else { ?>
                <input type="radio" name="rating_photo" value="4" />
                4
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_photo == 5) { ?>
                <input type="radio" name="rating_photo" value="5" checked="checked" />
                5
                <?php } else { ?>
                <input type="radio" name="rating_photo" value="5" />
                5
                <?php } ?>
              </label>
              <?php if ($error_rating_photo) { ?>
              <div class="text-danger"><?php echo $error_rating_photo; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-rating_description"><?php echo $entry_rating_description; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($rating_description == 1) { ?>
                <input type="radio" name="rating_description" value="1" checked="checked" />
                1
                <?php } else { ?>
                <input type="radio" name="rating_description" value="1" />
                1
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_description == 2) { ?>
                <input type="radio" name="rating_description" value="2" checked="checked" />
                2
                <?php } else { ?>
                <input type="radio" name="rating_description" value="2" />
                2
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_description == 3) { ?>
                <input type="radio" name="rating_description" value="3" checked="checked" />
                3
                <?php } else { ?>
                <input type="radio" name="rating_description" value="3" />
                3
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_description == 4) { ?>
                <input type="radio" name="rating_description" value="4" checked="checked" />
                4
                <?php } else { ?>
                <input type="radio" name="rating_description" value="4" />
                4
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if ($rating_description == 5) { ?>
                <input type="radio" name="rating_description" value="5" checked="checked" />
                5
                <?php } else { ?>
                <input type="radio" name="rating_description" value="5" />
                5
                <?php } ?>
              </label>
              <?php if ($error_rating_description) { ?>
              <div class="text-danger"><?php echo $error_rating_description; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
            <div class="col-sm-3">
              <div class="input-group datetime">
                <input type="text" name="date_added" value="<?php echo $date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-added" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
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

        </form>

      </div>
    </div>
  </div>
  <div class="modal fade" id="ImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Загруженная фотография</h4>
        </div>
        <div class="modal-body text-center"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
//--></script>
<script type="text/javascript"><!--
var autocomplete = '<?php echo $autocomplete; ?>'.replace('&amp;', '&');
$('input[name=\'product\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: autocomplete + '&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'product\']').val(item['label']);
		$('input[name=\'product_id\']').val(item['value']);		
	}	
});
const viewImage = function(t) {
    console.log(t)
    let $M = $('#ImageModal');
    $('.modal-body', $M).html($('<img class="img-responsive" src="' + $(t).attr('data-image') + '"/>'));
    $M.modal('show');
}
//--></script></div>
<?php echo $footer; ?>