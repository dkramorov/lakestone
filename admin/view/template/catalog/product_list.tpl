<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default" onclick="$('#form-product').attr('action', '<?php echo $copy; ?>').submit()"><i class="fa fa-copy"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
          <div class="well-cover"><i class="fa-li fa fa-spinner fa-spin"></i></div>
        	<h4>Управление товарами в категориях</h4>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="action-category">Название категории</label>
                <input type="text" name="action_category" placeholder="нажмите чтобы выбрать" id="input-category" class="form-control" />
                <input type="hidden" name="action_category_id">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="action-selector">Диапазон товаров</label>
                <select name="action_selector" id="action-selector" class="form-control">
                  <option value="selected">только отмеченные на этой странице</option>
                  <option value="all">все товары</option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="action-type">Действие</label>
                <select name="action_type" id="action-type" class="form-control">
                  <option value="add">добавить в эту категорию</option>
                  <option value="del">убрать из этой категории</option>
                </select>
              </div>
              <div class="action_message"></div>
              <button type="button" id="button-action" class="btn btn-primary pull-right"><i class="fa fa-check"></i> Сделать!</button>
              <div class="pull-right">
                <div class="checkbox" style="margin-right: 20px;"><label><input type="checkbox" name="action_aprove"/> Я уверен!</label></div>
              </div>
            </div>
          </div>
        </div>
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-model"><?php echo $entry_model; ?></label>
                <input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-category">Категория</label>
                <input type="text" name="filter_category" value="<?php echo $filter_category; ?>" placeholder="нажмите чтобы выбрать" id="input-model" class="form-control" />
                <input type="hidden" name="filter_category_id" value="<?=$filter_category_id?>">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-price"><?php echo $entry_price; ?></label>
                <input type="text" name="filter_price" value="<?php echo $filter_price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
                <input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="filter-filters"><span data-toggle="tooltip" title="(Автозаполнение)">Фильтры</span></label>
                <input type="text" name="filter_filter" value="" placeholder="нажмите чтобы выбрать" id="filter-filter" class="form-control" />
                <div id="filter-filters" class="well well-sm" style="height: 150px; overflow: auto;">
                  <?php foreach ($filter_filters as $filter_filter) { ?>
                  <div id="product-filter<?php echo $filter_filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $filter_filter['name']; ?>
                    <input type="hidden" name="filter_filters[]" value="<?php echo $filter_filter['filter_id']; ?>" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-image"><?php echo $entry_image; ?></label>
                <select name="filter_image" id="input-image" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_image) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_image && !is_null($filter_image)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-center"><?php echo $column_image; ?></td>
                  <td class="text-left"><?php if ($sort == 'pd.name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'p.model') { ?>
                    <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'p.price') { ?>
                    <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'p.quantity') { ?>
                    <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'p.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-center"><?php if ($product['image']) { ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $product['name']; ?></td>
                  <td class="text-left"><?php echo $product['model']; ?></td>
                  <td class="text-right"><?php if ($product['special']) { ?>
                    <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
                    <div class="text-danger"><?php echo $product['special']; ?></div>
                    <?php } else { ?>
                    <?php echo $product['price']; ?>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($product['quantity'] <= 0) { ?>
                    <span class="label label-warning"><?php echo $product['quantity']; ?></span>
                    <?php } elseif ($product['quantity'] <= 5) { ?>
                    <span class="label label-danger"><?php echo $product['quantity']; ?></span>
                    <?php } else { ?>
                    <span class="label label-success"><?php echo $product['quantity']; ?></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $product['status']; ?></td>
                  <td class="text-right"><a href="<?php echo $product['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
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
  <script type="text/javascript"><!--
var serialize_filter = function() {
  var url = ''
  var filter_name = $('input[name=\'filter_name\']').val();
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	var filter_model = $('input[name=\'filter_model\']').val();
	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}
	var filter_price = $('input[name=\'filter_price\']').val();
	if (filter_price) {
		url += '&filter_price=' + encodeURIComponent(filter_price);
	}
	var filter_quantity = $('input[name=\'filter_quantity\']').val();
	if (filter_quantity) {
		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}
	var filter_status = $('select[name=\'filter_status\']').val();
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
  var filter_image = $('select[name=\'filter_image\']').val();
  if (filter_image != '*') {
    url += '&filter_image=' + encodeURIComponent(filter_image);
  }
  var filter_category_id = $('input[name=\'filter_category_id\']').val();
  if (filter_category_id) {
    url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
  }
  var filter_filters = [];
  $('input[name=\'filter_filters[]\']').each(function() {
    filter_filters.push($(this).val())
  })
  if (filter_filters.length > 0) {
    url += '&filter_filters=' + filter_filters.join();
  }
  return url
}
$('#button-filter').on('click', function() {
	var url = 'index.php?route=catalog/product&token=<?php echo $token; ?>';
  url += serialize_filter()
	location = url;
});
//--></script>
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
		$('input[name=\'filter_name\']').val(item['label']);
	}
});

$('input[name=\'filter_model\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['model'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_model\']').val(item['label']);
	}
});
// Filter
$('input[name=\'filter_filter\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/filter/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['filter_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_filter\']').val('');
		$('#filter-filter' + item['value']).remove();
		$('#filter-filters').append('<div id="filter-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="filter_filters[]" value="' + item['value'] + '" /></div>');
    showFilterWarning()
	}
});
$('#filter-filters').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
  showFilterWarning()
});
// Category
$('input[name=\'filter_category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_category\']').val(item['label']);
    $('input[name=\'filter_category_id\']').val(item['value']);
	}
});
$('input[name=\'action_category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'action_category\']').val(item['label']);
    $('input[name=\'action_category_id\']').val(item['value']);
	}
});
$('select[name^="filter_"], input[name^="filter_"]').on('change', showFilterWarning)
var showFilterWarning = function() {
  $('.action_message').text('Внимание! Параметры фильтрации товаров были изменены. Текущие установки фильтрации и список товаров могут не совпадать.')
}
$('#button-action').on('click', function() {
  var $action = $('select[name=\'action_type\']'),
      $selector = $('select[name=\'action_selector\']'),
      $category_id = $('input[name=\'action_category_id\']'),
      $aprove = $('input[name="action_aprove"]'),
      action_error = false,
      selector_value
  if (!$aprove.is(':checked')) {
    $aprove.parent().parent().addClass('has-error')
    action_error = true
  }
  if (!$category_id.val()) {
    $category_id.parent().addClass('has-error')
    action_error = true
  }
  if ($selector.val() === 'selected' &&
    $('#form-product input[name="selected[]"]:checked').length == 0
  ) {
    $selector.parent().addClass('has-error')
    action_error = true
  }
  if (action_error)
    return
  if ($selector.val() === 'selected') {
    selector_value = ''
    $('#form-product input[name="selected[]"]:checked').each(function() {
      selector_value += $(this).val() + ','
    })
  } else {
    selector_value = serialize_filter()
  }
  console.log('action: ', $category_id.val(), $selector.val(), $action.val(), selector_value)
  var $b = $('#button-action'),
      bt = $b.text(),
      $c = $('.well-cover')
  $b.prop('disabled', true)
  $b.text('делаем...')
  $c.css('display', 'block')
  $.ajax({
    url: 'index.php?route=catalog/product/action&token=<?php echo $token; ?>',
    method: 'post',
    data: {
      'category_id': $category_id.val(),
      'selector'	 : $selector.val(),
      'selector_data': selector_value,
      'action'		 : $action.val(),
    },
    success: function(d) {
      console.log(d)
      if (d.status === 'OK') {
        location.reload()
      } else {
        alert('Похоже, что-то случилось или одно из двух, извините... (2)')
      }
    },
    complete: function() {
      console.log('done')
      $b.prop('disabled', false)
      $b.text(bt)
      $c.css('display', 'none')
    },
    error: function(e) {
      console.error(e)
      alert('Похоже, что-то случилось или одно из двух, извините... (1)')
    }
  })
})
//--></script></div>
<style>
.well {
  position: relative;
}
.well-cover {
  display: none;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0,0,0,0.4);
  z-index: 10;
}
.well-cover .fa {
  top: 30%;
  left: 40%;
  color: black;
  font-size: 70px;
}
</style>
<?php echo $footer; ?>
