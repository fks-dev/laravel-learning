<!DOCTYPE html>
<html lang="ja">
<head>
    <title>お知らせ詳細</title>
    @include('head')
</head>

<body>
    <a href="{{ route('users.index') }}">HOME</a>
    <div class="border rounded m-2">
        <div class="bg-success rounded-top p-2">
            <p>お知らせ詳細</p>
        </div>
        <div class="m-2">
            <p>{{$information->title}}</p>
            <p>{!! nl2br(e($information->text)) !!}</p>
        </div>
    </div>


</body>
</html>