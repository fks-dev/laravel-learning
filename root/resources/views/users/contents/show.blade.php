<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>{{ $title }}</title>
</head>

<body>
    @include('users.header')
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
                <form action="{{route('users.contents.record',$content)}}" method="post" class="col">
                    @csrf
                    <input type="hidden" id="log" name="log" value="1">
                    <button type="submit" class="btn btn-primary">終了</button>
                </form>
                <form action="{{route('users.contents.record',$content)}}" method="post" class="col">
                    @csrf
                    <input type="hidden" id="log" name="log" value="0">
                    <button type="submit" class="btn btn-danger">中断</button>
                </form>
            </div>
        </div>
    </footer>
</body>

</html>