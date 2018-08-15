<style>
.ui-btn-custom {
	padding-top: 0;
	padding-bottom: 0;
	line-height: normal !important;
	background-color: #ffffff !important;
	border-color: #ffffff !important;
	color: #333 !important;
	text-shadow: 0 1px 0 #f3f3f3 !important;
}
.p-10 {
	padding: 10px !important;
}
.t-center {
	text-align: center;
}
</style>

<script>
// ページ初期化処理の最初に発生
$(document).on('pagebeforecreate', function(event) {
	//console.log('pagebeforecreate');
});
// ページがDOMに生成されたときに発生
$(document).on('pagecreate', function(event) {
	//console.log('pagecreate');
	$('#send-action').on('click', function() {
		$.mobile.loading('show', {
			text: '反映中',
			textVisible: true,
			textonly: false
		});

		var commit = commitAsync(select_files);
		commit.success(function(response) {
			var release = releaseAsync();
			if (response == 1) {
				release.success(function(response) {
					$.mobile.loading('hide');
					if (response == 1) {
						location.reload();
					} else {
						$('#header-error-message').show();
						$('html,body').animate({
							scrollTop: 0
						}, 100);
					}
				});
				release.error(function() {
					$.mobile.loading('hide');
				});
				release.complete(function() {
					$.mobile.loading('hide');
				});
			} else {
				$('#header-error-message').show();
				$('html,body').animate({
					scrollTop: 0
				}, 100);
			}
		});
		commit.error(function() {
			$.mobile.loading('hide');
		});
		commit.complete(function() {
			$.mobile.loading('hide');
		});
	})
	$('#header-error-message').hide();

	$(document).tooltip({
		position: {
			my: 'left top+15',
			at: 'left bottom',
			collision: 'flipfit'
		}
	});
});
// スクロールを開始したときに発生
$(document).on('scrollstart', function(event) {
	//console.log('scrollstart');
});
// スクロールを停止したときに発生
$(document).on('scrollstop', function(event) {
	//console.log('scrollstop');
});
function getSync(){
	var result = $.ajax({
		type: 'GET',
		url: '/classes/get.php',
		async: false
	}).responseText;
	return result;
}
function commitAsync(data) {
	var result = $.ajax({
		type: 'POST',
		url: '/classes/commit.php',
		data: {
			"data": data
		},
		dataType: 'json',
	});
	return result;
}
function releaseAsync() {
	var result = $.ajax({
		type: 'POST',
		url: '/classes/release.php',
		dataType: 'json',
	});
	return result;
}

var selectable_files = $.parseJSON(getSync());
var select_files = {};

</script>

<p id="pagetop"></p>

<div data-role="page" id="top">

	<!-- header -->
	<header>
		<div class="row-header clearfix">
			<h1 id="nav-logo" class="fLeft"><a href="/"><img src="/assets/img/logo.png" alt="Hoge"></a></h1>
		</div>
	</header>

	<!-- main content -->
	<div role="main" class="main-content ui-content">
		<div id="header-error-message">
			<p>何も選択されていません。</p>
		</div>

		<table data-role="table" class="ui-responsive table-stroke">
			<thead>
				<tr>
					<th>No.【<a href="#" title="ファイルを表示するための順番です。">?</a>】</th>
					<th>ファイル名【<a href="#" title="更新されたファイル・追加されたファイルです。">?</a>】</th>
					<th>状態【<a href="#" title="M：変更されたファイルです A：新たに追加されたファイルです D：削除されたファイルです">?</a>】</th>
					<th>更新日時【<a href="#" title="最後に更新された日付（状態M）・追加された日付（状態A）です。">?</a>】</th>
				</tr>
			</thead>
			<tbody id="selectable">
				<script>
				$.each(selectable_files, function(key, value) {
					var no = key + 1;
					var path = value['path'];
					var status = value['status'];
					var update_at = (value['update_at']!='9999-01-01 00:00:00')?value['update_at']:'';

					var tag = '<tr>';
					tag += '<td><div class="ui-checkbox t-center">'+no+'</div></td>';
					tag += '<td><label for="item'+key+'" class="ui-btn-custom">'+path+'</label><input type="checkbox" name="item'+key+'" id="item'+key+'" value="'+path+'"></td>';
					tag += '<td><div class="ui-checkbox t-center">'+status+'</div></td>';
					tag += '<td><div class="ui-checkbox">'+update_at+'</div></td>';
					tag += '</tr>';
					$('#selectable').append(tag);

					$('#selectable #item'+key).on('change', function () {
						if ($(this).prop('checked')) {
							select_files[key] = path;
							$('#commit-items').append('<li id="key'+key+'"><div class="p-10">'+'【'+no+'】'+path+'</div></li>');
						} else {
							delete select_files[key];
							$('#commit-items li#key'+key).remove();
						}
					});
				});
				</script>
			</tbody>
		</table>

		<div class="button-area">
			<a href="#pop" data-rel="popup" data-position-to="window" data-transition="pop" id="button-confirm" class="button">本番反映確認 &raquo;</a></p>
		</div>
	</div>

	<!-- footer -->
	<footer>
		<div class="row top-footer-copyright">
			Copyright <s><script type="text/javascript">$y=2012;$ny=new Date().getFullYear();document.write($ny>$y?$y+'-'+$ny:$y);</script> </s>All Rights Reserved.
		</div>
	</footer>

	<!-- dialog -->
	<div role="main" class="ui-content">
		<div data-role="popup" id="pop" data-overlay-theme="b" data-dismissible="false" data-history="false">
			<div data-role="header" data-theme="b">
				<h1>反映確認</h1>
			</div>
			<p>下記のファイルを反映します。</p>
			<p>
				<span class="error-message">
					※再度ご確認ください。<br>
					※ファイルのバックアップはとりましたか？<br>
					※反映させるファイルに間違いはありませんか？<br>
					※間違えた場合に戻すまでには時間がかかります。
				</span>
			</p>
			<p><ul id="commit-items" data-role="listview" data-inset="true" data-divider-theme="b"></ul></p>
			<div class="button-area">
				<a href="#" id="send-action" data-rel="back" class="ui-btn ui-corner-all ui-btn-inline ui-mini">はい</a>
				<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-inline ui-mini">いいえ</a>
			</div>
		</div>
	</div>
</div>