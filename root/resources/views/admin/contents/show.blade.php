<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>コンテンツ</title>
</head>
<body>
    @include('admin.header')
    <div class="container">
        <a href="{{ route('admin.contents.index', $content->course_id) }}">&lt;&lt;戻る</a>

        <h2 class="m-3">{{ $content->title }}</h2>
        <p class="m-3">作成者:{{ $admin->username }}</p>

        <div class="ratio ratio-16x9 mx-auto p-2" style="width: 80%; height: 80%">
            <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/{{ $content->youtube_video_id }}"
            title="YouTube video player"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture;"
            allowfullscreen
        ></iframe>
        </div>

        <div class="mt-3">備考</div>
        <div class="border border-secondary">
            <p class="p-1">{{ $content->remarks }}</p>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>