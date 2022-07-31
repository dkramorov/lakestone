<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-information_sidebar" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-information_sidebar" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">Основное</a></li>
            <li><a href="#tab-bar" data-toggle="tab">Рубрикатор</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="information_sidebar_status" id="input-status" class="form-control">
                    <?php if ($information_sidebar_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-bar">
              <div class="table-responsive">
                <table id="sidebar" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-right">Название</td>
                      <td class="text-right">Статья</td>
                      <td class="text-right">Порядок сортировки</td>
                      <td class="text-right">Тип</td>
                      <td class="text-right">Статус</td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $bar_row = 0; ?>
                    <?php foreach ($sidebar as $item) { ?>
                    <tr id="bar-row<?php echo $bar_row; ?>">
                      <td class="text-right"><input type="text" name="sidebar[<?php echo $bar_row; ?>][name]" value="<?php echo $item['name']; ?>" placeholder="название" class="form-control" /></td>
                      <td class="text-right">
                        <input type="text" name="sidebar[<?php echo $bar_row; ?>][title]" value="<?php echo $item['title']; ?>" placeholder="название статьи" class="form-control information_autocomplete" />
                        <input type="hidden" name="sidebar[<?php echo $bar_row; ?>][information_id]" value="<?php echo $item['information_id']; ?>" />
                      </td>
                      <td class="text-right"><input type="text" name="sidebar[<?php echo $bar_row; ?>][position]" value="<?php echo $item['position']; ?>" placeholder="0" class="form-control" /></td>
                      <td class="text-right">
                        <select onChange="setType(this)" name="sidebar[<?php echo $bar_row; ?>][type]" class="form-control">
                          <option value="1" <?=($item['type'] == 1)?'selected="selected"':''?>>статья</option>
                          <option value="2" <?=($item['type'] == 2)?'selected="selected"':''?>>заголовок</option>
                        </select>
                      </td>
                      <td class="text-right">
                        <select name="sidebar[<?php echo $bar_row; ?>][status]" class="form-control">
                          <option value="0" <?=($item['status'] == 0)?'selected="selected"':''?>>выключен</option>
                          <option value="1" <?=($item['status'] == 1)?'selected="selected"':''?>>включен</option>
                        </select>
                      </td>
                      <td class="text-left"><button type="button" onclick="$('#bar-row<?php echo $bar_row; ?>').remove();" data-toggle="tooltip" title="удалить" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $bar_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="5"></td>
                      <td class="text-left"><button type="button" onclick="addBar();" data-toggle="tooltip" title="добавить" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
function sidebarcomplete(bar_row) {
	$('input[name=\'sidebar[' + bar_row + '][title]\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/information/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item.title,
							value: item.information_id
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'sidebar[' + bar_row + '][title]\']').val(item['label']);
			$('input[name=\'sidebar[' + bar_row + '][information_id]\']').val(item['value']);
		}
	});
}

var bar_row = <?=$bar_row?>;
for (var i=0;i<bar_row;i++) {
  sidebarcomplete(i)
}

function setType(t) {
  var tr = $(t).parents('tr'),
      v = $(t).find('option:selected').val(),
      a = tr.find('input[name$="[title]"]')
  if (v == 2)
    a.prop('disabled', true)
  else
    a.prop('disabled', false)
}

$('#sidebar select[name$="[type]"]').each(function(){
  setType(this)
})

function addBar() {
  html  = '<tr id="side-row' + bar_row + '">';
  html += '<td class="text-right"><input type="text" name="sidebar[' + bar_row + '][name]" placeholder="название" class="form-control" /></td>'
  html += '<td class="text-right">'
  html += '<input type="text" name="sidebar[' + bar_row + '][title]" placeholder="название статьи" class="form-control" />'
  html += '<input type="hidden" name="sidebar[' + bar_row + '][information_id]" />'
  html += '</td>'
  html += '<td class="text-right"><input type="text" name="sidebar[' + bar_row + '][position]" placeholder="0" class="form-control" /></td>'
  html += '<td class="text-right"><select onChange="setType(this)" name="sidebar[' + bar_row + '][type]" class="form-control">'
  html += '<option value="1">статья</option><option value="2">заголовок</option>'
  html += '</select></td>'
  html += '<td class="text-right"><select name="sidebar[' + bar_row + '][status]" class="form-control">'
  html += '<option value="0">выключен</option><option value="1">включен</option>'
  html += '</select></td>'
  html += '<td class="text-left"><button type="button" onclick="$("#bar-row' + bar_row + '").remove();" data-toggle="tooltip" title="удалить" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>'
  html += '</tr>';

	$('#sidebar tbody').append(html);

	sidebarcomplete(bar_row);

	bar_row++;
}

</script>
<?php echo $footer; ?>
