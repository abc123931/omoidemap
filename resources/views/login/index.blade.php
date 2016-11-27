@extends('layout')
@section('css_partial')
<style>
    ul {
        padding: 0;
        list-style: none;
    }
</style>
@stop
@section('body')
<div class="login_box">
    <div class="login_box_title">
        ログイン
    </div>
    @include('parts.errormessage')
    <form action="{{url('/login')}}" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        @if (Input::get('e_id'))
            <input type="hidden" name="event_id" value="{{Input::get('e_id')}}">
        @endif
        <div class="form-group has-error">
            <div class="login_text">
                <input type="text" name="email" placeholder="(必須)メールアドレスを入力してください" class="form-control">
            </div>
            <div class="login_text">
                <input type="password" name="password" placeholder="(必須)パスワードを入力してください。" class="form-control">
            </div>
            <div class="login_text">
                <label>
                    <input type="checkbox" name="remember" value="{{AppUtil::FLG_ON}}">ログイン情報を記憶する
                </label>
            </div>
            <div class="login_btn">
                <input type="submit" value="ログイン" class="btn btn-success btn-lg btn-block">
            </div>
        </div>
    </form>
    <div class="password_reset">
        <a href="{{url('/reminder')}}">パスワードを忘れた方はこちら</a>
    </div>
</div>
@stop
