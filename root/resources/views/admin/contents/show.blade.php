<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>コンテンツ</title>
</head>
<body>
    <div class="container">
        <a href="{{ route('content.index', $content->course_id) }}">&lt;&lt;戻る</a>

        <h2 class="m-3">{{ $content->title }}</h2>

        @if ($content->url != null)
        <div class="ratio ratio-16x9 mx-auto p-2" style="width: 80%; height: 80%">
            <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/{{ $content->url }}"
            title="YouTube video player"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture;"
            allowfullscreen
        ></iframe>
        </div>

        @elseif ($content->text != null)
            {!! $content->text !!}

        @elseif ($content->movie != null)
        <video controls>
            <source src="{{ asset('storage/' . $content->movie ) }}" type="video/mp4">
        </video>

        @elseif ($content->file != null)
            <a href="{{ route('content.download', $content) }}" class="btn btn-primary">ダウンロード開始</a>

        @elseif ($content->kind == 'テスト')
            <p>制限時間　　　：　{{ $content->testTime }}分</p>
            <p>合格する得点率：　{{ $content->testPer }}％</p>
            <p>出題数　　　　：　{{ $content->testVol }}問</p>
        @endif

        <div class="mt-3">備考</div>
        <div class="border border-secondary">
            <p class="p-1">{{ $content->remarks }}</p>
        </div>
    </div>
</body>
</html>