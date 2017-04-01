@extends('layout')
@section('css_partial')
<style>
ul {
    list-style: none;
    padding: 0;
    overflow: hidden;
}
a {
    color: black;
}
a:hover {
    color: black;
}
</style>
@stop
@section('body')
<div class="article_detail_box">
    <div class="article_img">
        @include('parts.errormessage')
        <div class="article_detail_nickname_title">
            <div class="article_detail_nickname_title_left">
                {{$article->title}}
            </div>
            <div class="article_detail_nickname_title_right">
                <div class="article_detail_nickname_title_right_name">
                    {{$article->user->nickname}}
                </div>
                <div class="article_detail_nickname_title_right_img">
                    <img src="{{url('/show/user', $article->user_id)}}" alt="画像" />
                </div>
            </div>
        </div>
        @if (isset($article->oneImage->image))
            <div class="article_img_content">
                <img src="{{url($article->oneImage->image)}}" alt="" />
            </div>
        @else
            <div class="article_img_content" style="color: white;">
                画像はありません
            </div>
        @endif
        <div class="article_img_footer">
            @if (Auth::check())
                <div class="article_img_footer_good_field" id="good">
                    <span>いいね！</span>
                    <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                    <span id="good_count">{{count($article->goods)}}</span>
                </div>
                <div class="article_img_footer_good_field" id="go">
                    <span>いきたい！</span>
                    <i class="fa fa-car" aria-hidden="true"></i>
                    <span id="go_count">{{count($article->goes)}}</span>
                </div>
            @else
                <a href="{{url('/login?a_d='. $article->id)}}">
                    <div class="article_img_footer_good_field">
                        <span>いいね！</span>
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                        <span id="good_count">{{count($article->goods)}}</span>
                    </div>
                </a>
                <a href="{{url('/login?a_d='. $article->id)}}">
                    <div class="article_img_footer_good_field">
                        <span>いきたい！</span>
                        <i class="fa fa-car" aria-hidden="true"></i>
                        <span id="go_count">{{count($article->goes)}}</span>
                    </div>
                </a>
            @endif
            <div class="article_img_footer_comment_field">
                <span>コメント</span>
                <i class="fa fa-commenting-o" aria-hidden="true"></i>
                <span>{{count($article->comments)}}</span>
            </div>
        </div>
    </div>
    <div class="article_detail_description">
        <div class="article_detail_description_episode">
            <p>{!! nl2br(htmlspecialchars($article->episode)) !!}</p>
        </div>
    </div>
    <div class="article_detail_comment_tag_field">
        <div class="article_detail_tag_information_field">
            <div class="article_detail_tag">
                <div class="article_detail_tag_header">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                    <span>タグ</span>
                </div>
                <div class="article_detail_tag_content">
                    @if (count($article->postsTags) != 0)
                        <ul>
                            @foreach ($article->postsTags as $postTag)
                                <a href="{{url('/tag', $postTag->tag->id)}}"><li>{{$postTag->tag->name}}</li></a>
                            @endforeach
                        </ul>
                    @else
                        <p>タグ付けされていません</p>
                    @endif
                </div>
            </div>
            <div class="article_detail_information">
                <div class="article_detail_information_header">
                    <i class="fa fa-tags" aria-hidden="true"></i>
                    <span>情報</span>
                </div>
                <div class="article_detail_information_content">
                    <ul>
                        <li><span class="article_detail_information_content_title">撮影時の年代</span><span class="article_detail_information_content_content">{{AppUtil::photoAgeLabel()[$article->age]}}</span></li>
                        <li><span class="article_detail_information_content_title">思い出の種類</span><span class="article_detail_information_content_content">{{AppUtil::photoFeelingLabel()[$article->feeling]}}</span></li>
                        <li><span class="article_detail_information_content_title">撮影場所</span><span class="article_detail_information_content_content">{{AppUtil::postNumberRemove($article->address)}}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="article_detail_comment">
            <div class="article_detail_comment_header">
                <i class="fa fa-commenting-o" aria-hidden="true"></i>
                <span>コメント</span>
            </div>
            @if (count($article->comments))
                @foreach ($article->comments as $comment)
                    <div class="article_detail_comment_body">
                        <img src="{{url('/show/user', $comment->user_id)}}" alt="画像" />
                        <div class="article_detail_comment_body_content">
                            <p>{{$comment->user->nickname}}</p>
                            {!! nl2br(htmlspecialchars($comment->content)) !!}
                            <p class="article_detail_comment_body_date">{{$comment->created_at}}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="article_detail_comment_body">
                    <p>コメントはありません。</p>
                </div>
            @endif
            <div class="article_detail_comment_footer">
                @if (Auth::user())
                    <p>コメントする</p>
                    <form action="{{url('/post/comment')}}" method="post">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <input type="hidden" name="post_id" value="{{$article->id}}">
                        <input type="hidden" name="url" value="{{AppUtil::urlSlash(Request::url())}}">
                        <textarea name="comment" rows="8" cols="40" class="form-control" placeholder="5文字以上500文字以内で投稿してください。" maxlength="500">{{old('comment')}}</textarea>
                        <input type="submit" value="コメント投稿" class="btn btn-primary pull-right" onclick="return confirm_dialog(this, '投稿してもよろしいですか？');">
                    </form>
                @else
                    <div style="text-align: center;">
                        <a href="{{url('/login?a_d='. $article->id)}}"><input type="button" value="ログインしてコメントする" class="btn btn-warning"></a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
@section('js_partial')
<script type="text/javascript">
$.ajaxSetup({headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
$(function() {
    $("#good").on('click', function(){
        var post_id = {{$article->id}};
        $.ajax({
            type: "POST",
            url: "{{url('/plus_good')}}",
            data: {
                "post_id": post_id
            },
            success: function(res) {
                if (res.message == 'success') {
                    $("#good_count").text(res.count);
                }else if (res.message == 'error'){
                    alert('エラーが発生したためいいねできませんでした。');
                }else if (res.message == 'already'){
                    alert('すでにいいねしています。');
                }else {
                    alert('自分の投稿にいいねはできません。');
                }
            },
        })
    });

    $("#go").on('click', function(){
        var post_id = {{$article->id}};
        $.ajax({
            type: "POST",
            url: "{{url('/plus_go')}}",
            data: {
                "post_id": post_id
            },
            success: function(res) {
                if (res.message == 'success') {
                    $("#go_count").text(res.count);
                }else if (res.message == 'error'){
                    alert('エラーが発生したためいきたい!できませんでした。');
                }else if (res.message == 'already'){
                    alert('すでにいきたい!しています。');
                }else {
                    alert('自分の投稿にいきたい！はできません。');
                }
            },
        })
    });
});
</script>
@stop
