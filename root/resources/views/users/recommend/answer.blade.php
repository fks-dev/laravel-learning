<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>動画相性診断</title>
</head>

<body>
@include('users.header')
<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
          <li class="breadcrumb-item active" aria-current="page">おすすめ動画コース診断</li>
        </ol>
</nav>
<p>回答ありがとうございます。診断結果が出ました。</p>

<p>あなたへのおすすめ動画コースは{{ $course->title }}です。</p>
<a href="{{ route('users.contents.index', $course) }}">おすすめ動画コースに進む</a>

{{-- footer --}}
@include('footer')
</body>
