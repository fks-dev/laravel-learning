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

        @if ($content->youtube_video_id != null)
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

        @elseif ($content->text != null)
            {!! $content->text !!}

        @elseif ($content->movie_file_path != null)
        <video controls>
            <source src="{{ asset('storage/' . $content->movie_file_path ) }}" type="video/mp4">
        </video>

        @elseif ($content->document_file_path != null)
            <a href="{{ route('admin.contents.download', $content) }}" class="btn btn-primary">ダウンロード開始</a>

        @elseif ($content->content_type == 6)
            <p>制限時間　　　：　{{ $content->time_limit_minutes }}分</p>
            <p>合格する得点率：　{{ $content->passing_score_rate }}％</p>
            <p>出題数　　　　：　{{ $content->amount_questions }}問</p>
        @endif

        <div class="mt-3">備考</div>
        <div class="border border-secondary">
            <p class="p-1">{{ $content->remarks }}</p>
        </div>
    </div>
</body>
</html>