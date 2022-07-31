<section class="mosaic size2-3">
	<table>
		<tr>
			<td rowspan="2">
				<img style="" class="img-responsive" src="/image/demo-1.jpg" alt="demo-1">
				<span class="over_icon">
					<a class="tr2orange" data-target="#youtube" data-toggle="modal" href="javascript:void(0);"><i class="fa fa-play-circle-o"></i></a>
				</span>
			</td>
			<td class="text bg_orange">
				<p class="product-cat">Colston Black</p>
				<span class="ersatz_head3"><a href="/collection">Превосходный<br>образ</a></span>
				<p><a href="/collection">Перейти в коллекцию</a></p>
			</td>
			<td>
				<img class="img-responsive" src="/image/demo-2.jpg" alt="demo-2">
			</td>
		</tr>
		<tr>
			<td>
				<img class="img-responsive" src="/image/demo-3.jpg" alt="demo-3">
			</td>
			<td class="text bg_black">
				<p class="product-cat">Caroline Brown</p>
				<span class="ersatz_head3"><a href="/collection">Совершенство<br>линий</a></span>
				<p><a href="/collection">Перейти в коллекцию</a></p>
			</td>
		</tr>
	</table>
</section>
<div class="modal fade form-wrapper" id="youtube" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<span class="ersatz_head3">Восхитительная минималистичность</span>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>
<script>
	DocumentReady.push(function(){
		var m = $('#youtube .modal-body')
		$('#youtube').on('shown.bs.modal', function(){
			var h = m.width()
			if ( h >= $(window).height() ) h = $(window).height() * 0.9
			m.find('iframe').width(m.width()).height(h)
		})
		$('#youtube .modal-body').append('<iframe src="//www.youtube.com/embed/CzXAOSBoL6Y?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>')
	})
</script>