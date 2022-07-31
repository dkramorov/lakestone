<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-banner_smart1" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner_smart1" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_maintitle; ?></label>
            <div class="col-sm-10">
              <input type="text" name="maintitle" value="<?php echo $maintitle; ?>" placeholder="<?php echo $entry_maintitle; ?>" id="input-title" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-template"><?php echo $entry_template; ?></label>
            <div class="col-sm-10">
              <input type="text" name="template" value="<?php echo $template; ?>" placeholder="<?php echo $entry_template; ?>" id="input-template" class="form-control" />
              <?php if ($error_template) { ?>
              <div class="text-danger"><?php echo $error_template; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-article">Сопроводительный текст</label>
            <div class="col-sm-10">
              <textarea type="text" name="article" placeholder="" id="input-article" class="form-control" ><?=$article?></textarea>
              <?php if ($error_article) { ?>
              <div class="text-danger"><?php echo $error_article; ?></div>
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
          <hr>
          <div class="panel panel-default">
            <div class="panel-body">
              <h3>Большой слайдер слева</h3>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-banner"><?php echo $entry_banner; ?></label>
                <div class="col-sm-10">
                  <select name="banner_id" id="input-banner" class="form-control">
                    <?php foreach ($banners as $banner) { ?>
                    <?php if ($banner['banner_id'] == $banner_id) { ?>
                    <option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_width; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
                  <?php if ($error_width) { ?>
                  <div class="text-danger"><?php echo $error_width; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-height"><?php echo $entry_height; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
                  <?php if ($error_height) { ?>
                  <div class="text-danger"><?php echo $error_height; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="title" value="<?php echo $title; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title" class="form-control" />
                </div>
              </div>
<?/*
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-text"><?php echo $entry_text; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="text" value="<?php echo $text; ?>" placeholder="<?php echo $entry_text; ?>" id="input-text" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-button"><?php echo $entry_button; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="button" value="<?php echo $button; ?>" placeholder="<?php echo $entry_button; ?>" id="input-button" class="form-control" />
                </div>
              </div>
*/?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-body">
              <h3>Маленькие баннеры справа</h3>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-width1"><?php echo $entry_width; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="width1" value="<?php echo $width1; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width1" class="form-control" />
                  <?php if ($error_width) { ?>
                  <div class="text-danger"><?php echo $error_width; ?></div>
                  <?php } ?>
                </div>
              </div>
              <h4>верхний</h4>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-banner"><?php echo $entry_banner; ?></label>
                <div class="col-sm-10">
                  <select name="banner1_id" id="input-banner1" class="form-control">
                    <?php foreach ($banners as $banner) { ?>
                    <?php if ($banner['banner_id'] == $banner1_id) { ?>
                    <option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-height1"><?php echo $entry_height; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="height1" value="<?php echo $height1; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height1" class="form-control" />
                  <?php if ($error_height) { ?>
                  <div class="text-danger"><?php echo $error_height; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-title1"><?php echo $entry_title; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="title1" value="<?php echo $title1; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title1" class="form-control" />
                </div>
              </div>
<?/*
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-text1"><?php echo $entry_text; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="text1" value="<?php echo $text1; ?>" placeholder="<?php echo $entry_text; ?>" id="input-text1" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-button1"><?php echo $entry_button; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="button1" value="<?php echo $button1; ?>" placeholder="<?php echo $entry_button; ?>" id="input-button1" class="form-control" />
                </div>
              </div>
*/?>
              <h4>нижний</h4>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-banner"><?php echo $entry_banner; ?></label>
                <div class="col-sm-10">
                  <select name="banner2_id" id="input2-banner" class="form-control">
                    <?php foreach ($banners as $banner) { ?>
                    <?php if ($banner['banner_id'] == $banner2_id) { ?>
                    <option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-height2"><?php echo $entry_height; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="height2" value="<?php echo $height2; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height2" class="form-control" />
                  <?php if ($error_height) { ?>
                  <div class="text-danger"><?php echo $error_height; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-title2"><?php echo $entry_title; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="title2" value="<?php echo $title2; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title2" class="form-control" />
                </div>
              </div>
<?/*
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-text2"><?php echo $entry_text; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="text2" value="<?php echo $text2; ?>" placeholder="<?php echo $entry_text; ?>" id="input-text2" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-button2"><?php echo $entry_button; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="button2" value="<?php echo $button2; ?>" placeholder="<?php echo $entry_button; ?>" id="input-button2" class="form-control" />
                </div>
              </div>
*/?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
