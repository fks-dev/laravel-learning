<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>コンテンツ</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-5 container">
        <div class="mb-2">
            <a class="btn btn-secondary" href="{{ route('admin.contents.index', $content->course_id) }}">戻る</a>
        </div>
        <div class="mt-3">
            <h3 class="rounded-top p-2 card-header text-success shadow-sm" style="background-color: #cdeee0">{{ $content->title }}</h3>
            <div class="border">
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

                <div class="p-3">
                    <h4>備考</h4>
                    <div class="border">
                        <div class="p-2">
                            <p>{!! nl2br(htmlspecialchars($content->remarks)) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>
