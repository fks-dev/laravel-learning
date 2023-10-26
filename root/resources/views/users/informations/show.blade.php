<!DOCTYPE html>
<html lang="ja">
<head>
    <title>お知らせ詳細</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
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