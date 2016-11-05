@extends('layout')
@section('css_partial')
<style>
.top_wrapper {
	width: 90%;
	margin: 0 auto;
	overflow: hidden;
	margin-bottom: 50px;
}
.map-embed
{
	max-width: 100% ;
	margin: 0 ;
	padding: 0 0 56.25% ;

	overflow: hidden ;

	position: relative ;
	top: 0 ;
	left: 0 ;
}

.map-embed > div
{
	position: absolute ;
	top: 0 ;
	left: 0 ;

	width: 100%;
	height: 100%;

	margin: 0 ;
	padding: 0 ;
}

.map-embed img
{
	width: 100%;
    height: 100%;
}
ul {
	list-style: none;
	padding: 0;
}
h2 {
	margin: 0;
}
a {
	color: black;
	text-decoration: none;
}
a:hover {
	color: black;
}
.top_pagination {
	text-align: center;
	overflow: hidden;
}
.top_article_list li:hover {
	background-color: white;
	opacity: 0.7;
}
.marker {
	position: absolute;
	cursor: pointer;
	width: 40px;
	height: 40px;
	border-radius: 20px;
	border: 2px solid white;
	background-size: cover;
	background-repeat: no-repeat;
}
#next {
	float: right;
}
#prev {
	float: left;
}
.top_loading_img {
	text-align: center;
}
</style>
@stop
@section('body')
<div class="top_img">
	<img src="{{url('/top_img_1.jpg')}}" alt="" >
	<a href="{{url('/register')}}">
		<input type="button" value="思い出を投稿する" class="btn btn-warning">
	</a>
</div>
<div class="top_wrapper">
	<div class="top_map">
		<div class="top_map_search">
			<form action="{{url('/')}}" onsubmit="keywordSubmit(); return false;">
				<div class="input-group">
					<input type="text" class="form-control keyword_search" id="keyword" placeholder="場所やキーワードで検索">
					<span class="input-group-btn">
						<button class="btn btn-default" type="submit">
							<i class='glyphicon glyphicon-search'></i>
						</button>
					</span>
				</div>
			</form>
	    </div>
	    <div class="top_map_main">
	        <div class="map-embed">
	        	<div id="map-canvas"></div>
	        </div>
	    </div>
	</div>
	<article class="top_article_list">
		<h2>思い出</h2>
		<ul>
			<div class="top_loading_img">
				<img src="{{url('/loading.gif')}}">
			</div>
		</ul>
	</article>
</div>
@stop
@section('js_partial')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsACv6SiwiUKM1YnUg2_nIfrjSnYzFke0" type="text/javascript"></script>
<script type="text/javascript" src="{{url('/js/jquery.imagefit.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
// マーカーのデータ
var markerData = [
	@foreach ($posts as $post)
		{
			lat: {{$post->lat}},
			lng: {{$post->lng}},
			image: "{{url($post->oneImage->image)}}",
			title: "{{$post->title}}",
			good: "{{$post->goods->count()}}",
			comment: "{{$post->comments->count()}}",
			url: "{{url('/article/detail', $post->id)}}",
		},
	@endforeach
];

var marker = [];
var infoWindow = [];
var map;
var current_num = 0;
var res_articles = [];
var page_num = 1;
////////////////// カスタムマーカー
function CustomMarker(latlng, map, args, markerData) {
	this.latlng = latlng;
	this.args = args;
	this.setMap(map);
	this.markerData = markerData;
	this.map = map;
}

CustomMarker.prototype = new google.maps.OverlayView();

CustomMarker.prototype.draw = function() {

	var self = this;

	var div = this.div;
	var span = this.span;
	var markerData = this.markerData;
	var map = this.map;

	if (!div) {

		div = this.div = document.createElement('div');
		div.className = 'marker';
		div.style.backgroundImage = 'url(' + this.markerData.image + ')';

		if (typeof(self.args.marker_id) !== 'undefined') {
			div.dataset.marker_id = self.args.marker_id;
		}

		var uluru = {lat: markerData.lat, lng: markerData.lng};
		var infowindow = new google.maps.InfoWindow({
			content: '<div class="marker_img" style="background-image:url(' + markerData['image'] + ');"></div>'
					+ '<div class="marker_title">' + markerData['title'] + '</div>'
					+ '<div class="marker_good_field"><span>いいね!</span><i class="fa fa-thumbs-o-up" aria-hidden="true"></i><span>' + markerData['good'] + '</span>'
					+ '<span>コメント</span><i class="fa fa-commenting-o" aria-hidden="true"></i><span>' + markerData['comment'] + '</span></div>',
			position: uluru
		});

		google.maps.event.addDomListener(div, "click", function() {
			location.href = markerData.url;
		});

		google.maps.event.addDomListener(div, "mouseover", function() {
			infowindow.open(map);
		});

		google.maps.event.addDomListener(div, "mouseout", function() {
			infowindow.close();
		});

		var panes = this.getPanes();
		panes.overlayImage.appendChild(div);
		//panes.overlayImage.appendChild(span);
	}

	var point = this.getProjection().fromLatLngToDivPixel(this.latlng);

	if (point) {
		div.style.left = (point.x - 20) + 'px';
		div.style.top = (point.y - 10) + 'px';
		//span.style.left = (point.x + 15) + 'px';
		//span.style.top = (point.y + 5) + 'px';
	}
};

CustomMarker.prototype.remove = function() {
	if (this.div) {
		this.div.parentNode.removeChild(this.div);
		this.div = null;
	}
};

CustomMarker.prototype.getPosition = function() {
	return this.latlng;
};
///////////////// カスタムマーカー終わり

init();

///////////googlemap
function init() {
    // キャンパスの要素を取得する
    var canvas = document.getElementById( 'map-canvas' ) ;

    // 中心の位置座標を指定する
    var latlng = new google.maps.LatLng( 37.6510589 , 139.72682550000002 );

    // 地図のオプションを設定する
    var mapOptions = {
        zoom: 5,
        center: latlng ,		// 中心座標 [latlng]
		scrollwheel: false,
    };

    // [canvas]に、[mapOptions]の内容の、地図のインスタンス([map])を作成する
    var map = new google.maps.Map( canvas , mapOptions ) ;

	google.maps.event.addListenerOnce(map, 'idle', function() {
		dispLatLang(map);
	});

	postLatLangZoom(map);

	for (var i = 0; i < markerData.length; i++) {
		markerLatLng = new google.maps.LatLng({lat: markerData[i]['lat'], lng: markerData[i]['lng']});
		var overlay = new CustomMarker(
			markerLatLng,
			map,
			{},
			markerData[i]
		);
	}

}


/////// function
// マーカーにクリックイベントを追加
function markerEvent(i) {
    marker[i].addListener('click', function() { // マーカーをクリックしたとき
        infoWindow[i].open(map, marker[i]); // 吹き出しの表示
    });
}

function setZoomLimit(map, mapTypeId){
  //マップタイプIDを管理するレジストリを取得
  var mapTypeRegistry = map.mapTypes;

  //レジストリから現在のマップタイプIDのMapTypeを取得する
  var mapType = mapTypeRegistry.get(mapTypeId);
  //ズームレベルを設定する
  if ($(window).width() <= 768) {
	  mapType.maxZoom = 15;  //SATELLITE・HYBRIDは機能しない
	  mapType.minZoom = 4;
  }else {
	  mapType.maxZoom = 15;  //SATELLITE・HYBRIDは機能しない
	  mapType.minZoom = 5;
  }
}

function postLatLangZoom(map) {
	google.maps.event.addListener(map, 'dragend', function() {
		dispLatLang(map);
	});

	google.maps.event.addListener(map, 'zoom_changed', function() {
		dispLatLang(map);
	});

	google.maps.event.addListenerOnce(map, "projection_changed", function(){
      map.setMapTypeId(google.maps.MapTypeId.HYBRID);  //一瞬だけマップタイプを変更
      setZoomLimit(map, google.maps.MapTypeId.ROADMAP);
      setZoomLimit(map, google.maps.MapTypeId.HYBRID);
      setZoomLimit(map, google.maps.MapTypeId.SATELLITE);
      setZoomLimit(map, google.maps.MapTypeId.TERRAIN);
      map.setMapTypeId(google.maps.MapTypeId.ROADMAP);  //もとに戻す
    });
}

function dispLatLang(map) {
	$(".top_loading_img").css('display', '');
	current_num = 0;
	res_articles = [];
	var latlng = map.getBounds();
	$.ajax({
		type: "POST",
		url: "{{url('/ajax/article_list')}}",
		data: {
			'sw_lat': latlng.getSouthWest().lat(),
			'sw_lng': latlng.getSouthWest().lng(),
			'ne_lat': latlng.getNorthEast().lat(),
			'ne_lng': latlng.getNorthEast().lng()
		},
		success: function(res) {
			res_articles = res.articles;
			if (res_articles.length > 6) {
				var length = 6;
			}else {
				var length = res_articles.length;
			}
			$(".top_loading_img").css('display', 'none');
			$(".top_article_list").children('ul').children('a').remove();
			$(".top_article_list").children('ul').children('p').remove();
			$(".top_article_list").children('ul').children('input').remove();
			if (res_articles.length != 0) {
				for (var i = 0; i < length; i++) {
					current_num = articleListDesign(res_articles, i, current_num);
				}
				if (current_num == 6) {
					$(".top_article_list")
					.children('ul')
					.append('<input type="button" class="btn btn-primary" id="next" value="Next&nbsp;>">')
					.hide().fadeIn('normal');
				}
			}else {
				$(".top_article_list").children('ul').append('<p>該当するものはありません</p>');
			}
		}

	});
}
// 場所を検索
function addressFocus(address, map) {
	// キャンパスの要素を取得する
    var canvas = document.getElementById( 'map-canvas' );
	var geocoder = new google.maps.Geocoder();

	geocoder.geocode( { 'address': address}, function(results, status) {
		// ジオコーディングが成功した場合
		if (status == google.maps.GeocoderStatus.OK) {
			// 地図表示に関するオプション
			var mapOptions = {
				zoom: 15,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			// 地図を表示させるインスタンスを生成
			var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

			google.maps.event.addListenerOnce(map, 'center_changed', function() {
				dispLatLang(map);
			});

			postLatLangZoom(map);

			for (var i = 0; i < markerData.length; i++) {
				markerLatLng = new google.maps.LatLng({lat: markerData[i]['lat'], lng: markerData[i]['lng']});
				var overlay = new CustomMarker(
					markerLatLng,
					map,
					{},
					markerData[i]
				);
			}

			// google.maps.Map()コンストラクタに定義されているsetCenter()メソッドで
			// 変換した緯度・経度情報を地図の中心に表示
			map.setCenter(results[0].geometry.location);

		// ジオコーディングが成功しなかった場合
		} else {
			if (address == "") {
			}else {
				alert("その場所を検索することができませんでした");
			}
		}
	});
}
function keywordSubmit() {
	var address = $("#keyword").val();
	addressFocus(address, map);
}

$(document).on('click', '#next', function() {
	page_num += 1;
	NextPrev();
});

$(document).on('click', '#prev', function() {
	page_num -= 1;
	current_num = 6 * (page_num - 1);
	NextPrev();
});

// next, prev
function NextPrev() {
	$(".top_article_list").children('ul').children('a').remove();
	$(".top_article_list").children('ul').children('p').remove();
	$(".top_article_list").children('ul').children('input').remove();
	if (res_articles.length - current_num > 6) {
		var length = current_num + 6;
	}else {
		var length = current_num + (res_articles.length - current_num);
	}
	for (var i = current_num; i < length; i++) {
		current_num = articleListDesign(res_articles, i, current_num);
	}
	if (current_num < res_articles.length) {
		$(".top_article_list")
		.children('ul')
		.append('<input type="button" class="btn btn-primary" id="next" value="Next&nbsp;>">')
		.hide().fadeIn('normal');
	}
	if (current_num > 6) {
		$(".top_article_list")
		.children('ul')
		.append('<input type="button" class="btn btn-primary" id="prev" value="<&nbsp;Prev">')
		.hide().fadeIn('normal');
	}
}

// 記事リスト作成
function articleListDesign(res_articles, i, current_num) {
	$(".top_article_list")
	.children('ul')
	.append(
		'<a href="' + res_articles[i][0].url + '">'
		+	'<li>'
		+		'<div class="article_list_img" style="background-image: url(' + res_articles[i][0].image +')"></div>'
		+		'<div class="article_list_data">'
		+			'<p class="article_list_data_title">' + res_articles[i][0].title + '</p>'
		+			'<p class="article_list_data_address">' + res_articles[i][0].address + '</p>'
		+			'<span>いいね！</span>'
		+           '<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>'
		+           '<span>' + res_articles[i][0].goods + '</span>'
		+			'<span>コメント</span>'
		+           '<i class="fa fa-commenting-o" aria-hidden="true"></i>'
		+			'<span>' + res_articles[i][0].comments + '</span>'
		+		'</div>'
		+	'</li>'
		+'</a>'
	).hide().fadeIn('normal');
	return current_num += 1;
}


////////////// googlemap 終わり

$(function(){
	$(window).load(function() {
        $('.top_article_list_img').imagefit({
        	mode: 'outside',
        	force : 'false',
        	halign : 'center',
        	valign : 'middle',
            onStart: function (index, container, imagecontainer) {
                /* Some code */
            },
            onLoad: function() {

            },
        });
    });

	// $("#regions").on('change', function() {
	// 	var region_id = $(this).val();
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "{{url('/get_prefectures')}}",
	// 		data: {
	// 			"region_id": region_id
	// 		},
	// 		success: function(res) {
	// 			for(var i = 0; i < res.prefectures.length; i ++) {
	// 				$("#prefectures").append("<option value='" + res.prefectures[i].id + "'>" + res.prefectures[i].name + "</option>");
	// 			}
	// 		}
	// 	});
	// });
});
</script>
@stop
