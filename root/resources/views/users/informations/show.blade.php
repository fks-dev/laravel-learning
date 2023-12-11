<!DOCTYPE html>
<html lang="ja">
<head>
    <title>お知らせ詳細</title>
    @include('head')
</head>

<body>
    @include('users.header')
    <div class="container-md mt-4">
        <div class="mb-4 p-3 border rounded" style="background-color: #f5f5f5;">
            <a class="text-decoration-none" href="{{ route('users.index') }}">HOME</a> /
            <a class="text-decoration-none" href="{{ route('users.informations.list') }}">お知らせ一覧</a>
        </div>
        <div class="border rounded">
            <div class="rounded-top p-2 card-header text-success shadow-sm" style="background-color: #cdeee0">
                <p>{{$information->title}}</p>
            </div>
            <div class="m-2">
                <p class="text-end">{{$information->created_at->format('Y/m/d')}}</p>
                <p>{!! nl2br(e($information->text)) !!}</p>
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>