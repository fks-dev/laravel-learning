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
        <div class="container">
    @switch($content->content_type)
        @case(2)
        <div class="ratio ratio-16x9">
            <iframe src="https://www.youtube.com/embed/CN-Ja6jCweA"></iframe>
        </div>
            @break
        @case(3)
            <p>資料{{ $content->document_file_path }}をダウンロード</p>
            @break
        @case(4)
            <p>{{$content->text}}</p>
            @break
        @case(5)
            <p>サイト内動画を再生</p>
            @break
        @case(6)
            <p>テストを開始</p>
            @break
        @default
            <p>[エラー]コンテンツがありません</p>
    @endswitch
    <div class="container">
    </main>
    <footer class="">
        <div class="container d-flex justify-content-between align-items-center p-4">
            <form action="{{ route('users.content.record', $content) }}" method="post" class="col">
                <input type="hidden" id="score" name="score" value="1">
                <button type="submit" class="btn btn-primary">よく理解できた</button>
            </form>
            <form action="{{ route('users.content.record', $content) }}" method="post" class="col">
                <input type="hidden" id="score" name="score" value="1">
                <button type="submit" class="btn btn-primary">まあまあ理解できた</button>
            </form>
            <form action="{{ route('users.content.record', $content) }}" method="post" class="col">
                <input type="hidden" id="score" name="score" value="1">
                <button type="submit" class="btn btn-primary">あまり理解できなかった</button>
            </form>
            <form action="{{ route('users.content.record', $content) }}" method="post" class="col">
                <input type="hidden" id="score" name="score" value="1">
                <button type="submit" class="btn btn-primary">理解できなかった</button>
            </form>
            <form action="{{ route('users.content.record', $content) }}" method="post" class="col">
                <input type="hidden" id="score" name="score" value="1">
                <button type="submit" class="btn btn-danger">中断</button>
            </form>
            <a href="{{ route('users.content.index', $content->course) }}" class="btn btn-success col">戻る</a>
        </div>
    </footer>
</body>

</html>