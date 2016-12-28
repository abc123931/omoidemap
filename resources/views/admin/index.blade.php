@extends('admin.layout')
@section('body')
    <h2>テーブル</h2>
    <ul class="top_ul">
        <a href="{{url('/admin/user')}}"><li>ユーザー</li></a>
        <a href="{{url('/admin/posts')}}"><li>記事</li></a>
    </ul>
    <h2>イベント</h2>
    <ul class="top_ul_event">
        <a href="{{url('/admin/event')}}"><li>イベント一覧</li></a>
        <a href="{{url('/admin/event/create')}}"><li>イベント作成</li></a>
    </ul>
@stop
