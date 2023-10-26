<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    @include('admin.head')
    <link rel="stylesheet" href="/css/user_index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>

<body>
    <header>
        <nav class="navbar p-0 bg-primary  ">
            <div class="d-flex justify-content-between align-items-center container-fluid">
                <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learnig</h2>
                <ul class="nav me-2 text-white">
                    <li class="nav-item border-end p-1">ようこそ{{ $user->username }}さん</li>
                    <li class="nav-item border-end p-1"><a class="link-underline text-white" href="#">設定</a></li>
                    <li class="nav-item p-1"><a class="link-underline text-white" href="#">ログアウト</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="container border py-2  my-2 rounded">
            @switch($content->content_type)
            @case(2)
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/{{ $content->youtube_video_id }}"></iframe>
            </div>
            @break
            @case(3)
            <div class="w-100 d-flex justify-content-center my-4">
                <a href="{{ route('users.content.handout', $content) }}" class="btn btn-success">資料をダウンロード</a>
            </div>
            @break
            @case(4)
            <div class="m-4">
                {!! $content->text !!}
            </div>
            @break
            @case(5)
            <div class="ratio ratio-16x9">
                <iframe src="{{ $content->movie_file_path }}"></iframe>
            </div>
            @break
            @default
            <p>[エラー]コンテンツがありません</p>
            @endswitch
        <div class="container">
    </main>
    <footer class="invisible">
        <div class="container d-flex justify-content-end align-items-center p-4">
            <div class="row">
                <form action="#" method="post" class="col">
                    <input type="hidden" id="log" name="log" value="1">
                    <button type="submit" class="btn btn-primary">終了</button>
                </form>
                <form action="#" method="post" class="col">
                    <input type="hidden" id="log" name="log" value="0">
                    <button type="submit" class="btn btn-danger">中断</button>
                </form>
            </div>
        </div>
    </footer>
</body>

</html>