<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
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
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/{{ $content->youtube_video_id }}"></iframe>
            </div>
        </div>
    </main>
    <footer>
        <div class="container d-flex justify-content-end align-items-center p-4">
            <div class="row">
                <form action="{{route('users.content.record',$content)}}" method="post" class="col">
                    @csrf
                    <input type="hidden" id="log" name="log" value="1">
                    <button type="submit" class="btn btn-primary">終了</button>
                </form>
                <form action="{{route('users.content.record',$content)}}" method="post" class="col">
                    @csrf
                    <input type="hidden" id="log" name="log" value="0">
                    <button type="submit" class="btn btn-danger">中断</button>
                </form>
            </div>
        </div>
    </footer>
</body>

</html>