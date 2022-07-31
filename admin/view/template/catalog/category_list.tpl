<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a> <a href="<?php echo $repair; ?>" data-toggle="tooltip" title="<?php echo $button_rebuild; ?>" class="btn btn-default"><i class="fa fa-refresh"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-category').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4 col-sm-offset-8">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-category">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'sort_order') { ?>
                    <a href="<?php echo $sort_sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_sort_order; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_sort_order; ?>"><?php echo $column_sort_order; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($categories) { ?>
                <?php foreach ($categories as $category) { ?>
                <tr<?echo (($grouping and $category['parent_id'] != $root_id) ? ' style="display:none"' : '') ?>>
                  <td class="text-center"><?php if (in_array($category['category_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" />
                    <?php } ?></td>
                  <? if ($category['children']) { ?>
                  <td class="text-left catname" data-children="<?=$category['children']?>" data-parent_id="<?=$category['parent_id']?>" data-category_id="<?=$category['category_id']?>">
                    <a title="уйти в категорию" href="<?=$category['href']?>"><?php echo $category['name']; ?></a>
                    <? if ($grouping) { ?>
                    <span class="glyphicon glyphicon-plus collapse-toggle" aria-hidden="true" onclick="collapse_toggle(this)"></span>
                    <? } ?>
                  </td>
                  <? } else { ?>
                  <td class="text-left catname" data-children="<?=$category['children']?>" data-parent_id="<?=$category['parent_id']?>" data-category_id="<?=$category['category_id']?>"><?php echo $category['name']; ?></td>
                  <? } ?>
                  <td class="text-right"><?php echo $category['sort_order']; ?></td>
                  <td class="text-right"><a href="<?php echo $category['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
//   $('tbody tr td.catname[data-parent_id!=0]').each(function() {
//     $(this).parents('tr').fadeOut(0)
//   })
//   $('tbody tr td.catname[data-children!=0]').each(function() {
//     var $t = $(this),
//         $tr = $t.parents('tr'),
//         pid = $t.attr('data-parent_id'),
//         cid = $t.attr('data-category_id'),
//         name = $t.text()
//     $t.html(name + ' <span class="glyphicon glyphicon-plus collapse-toggle" aria-hidden="true" onclick="collapse_toggle(this)"></span>')
//   })
})
var collapse_toggle = function(c) {
  var $c = $(c),
      $t = $c.parent(),
      pid = $t.attr('data-parent_id'),
      cid = $t.attr('data-category_id')
  if ($c.hasClass('glyphicon-plus')) {
    $c.removeClass('glyphicon-plus')
    $c.addClass('glyphicon-minus')
    $('tbody tr td.catname[data-parent_id="' + cid + '"]').parents('tr').fadeIn(50)
  } else {
    $c.removeClass('glyphicon-minus')
    $c.addClass('glyphicon-plus')
    $('tbody tr td.catname[data-parent_id="' + cid + '"]').parents('tr').fadeOut(50)
  }
}
$('#button-filter').on('click', function() {
	var url = 'index.php?route=catalog/category&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	location = url;
});
</script>
<style>
.glyphicon {
  border: solid thin #999;
  border-radius: 50%;
  padding: 2px 3px 2px 3px;
  margin-left: 5px;
  color: white;
  background-color: #ccc;
}
</style>
<?php echo $footer; ?>
