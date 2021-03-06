<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="思い出の場所の写真とエピソードを投稿するサイトです。自分にとってはなんでもない風景でも、誰かにとっては特別な場所かもしれない。それを集めて地図にする。">
    <meta name="keywords" content="思い出,旅行,まっぷ,写真,食事,デート, おもいでまっぷ,思い出マップ">
    <meta property="og:url" content="{{Request::url()}}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="思い出マップ">
    <meta property="og:description" content="思い出の場所の写真とエピソードを投稿するサイトです。自分にとってはなんでもない風景でも、誰かにとっては特別な場所かもしれない。それを集めて地図にする。">
    <title>思い出マップ</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.css">
    <link rel="stylesheet" href="{{url('/css/vex.css')}}" />
    <link rel="stylesheet" href="{{url('/css/vex-theme-os.css')}}" />
    <link rel="stylesheet" href="{{url('/css/bootstrap.offcanvas.min.css')}}">
    <link rel="stylesheet" href="{{url('/css/style.css')}}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    @yield('css_partial')
    <style>
    @if (Auth::check())
        .header_right {
            width: 339px;
        }
        .header_search {
            width: calc(100% - 508px);
        }
    @else
        .header_right {
            width: 357px;
        }
        .header_search {
            width: calc(100% - 526px);
        }
    @endif
    @media all and (max-width: 768px) {
        .header_search {
            width: 80%;
        }
        .header_right {
            width: 50px;
        }
    }
    </style>
  </head>
  <body>
    <div class="wrapper">
      @include('parts.header')
      @yield('body')
      @include('parts.footer')
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js"></script>
    <script src="{{url('/js/vex.combined.min.js')}}"></script>
    <script src="{{url('/js/bootstrap.offcanvas.min.js')}}"></script>
    <script>vex.defaultOptions.className = 'vex-theme-os';</script>
    <script>
      function confirm_dialog(self, msg) {
          var form = $(self).closest('form');
          vex.dialog.confirm({
              message: msg,
              callback: function(value) {
                  if (value) {
                      form.off('submit');
                      form.submit();
                  }
              }
          });
          return false;
      }
    </script>
    @yield('js_partial')
  </body>
</html>
