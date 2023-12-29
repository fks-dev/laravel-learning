<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>動画相性診断</title>
</head>

<body>
@include('users.header')
<p>回答ありがとうございます。診断結果が出ました。</p>

<p>あなたへのおすすめ動画は{{ $course->title }}です。</p>
<a href="{{ route('users.contents.index', $course) }}">おすすめ動画に進む</a>

{{-- footer --}}
@include('footer')
</body>
