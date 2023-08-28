<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>メッセージ一覧</title>
</head>

<body>
<div class="mt-5 container">
    <div class="d-flex justify-content-between">
        <h2 class="col">ゴミ箱</h2>
        <div class="col-auto">
            <a class="btn btn-primary" href="{{ route('admin.message.create')}}">&plus;追加</a>
            <a class="btn btn-info" href="{{ route('admin.message.index')}}">受信</a>
            <a class="btn btn-success" href="{{ route('admin.message.draft')}}">下書き</a>
            <a class="btn btn-secondary" href="{{ route('admin.message.sent')}}">送信済み</a>
        </div>
    </div>
{{-- 登録・削除　メッセージ --}}
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @elseif (session('danger'))
        <div class="alert alert-danger">
            {{ session('danger') }}
        </div>
    @elseif (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="col-3">件名</th>
                <th class="col-2">種別</th>
                <th class="col-5">本文</th>
                <th class="col-2 text-center">削除日時</th>
                <th class="col-2 text-center">Actions</th>
                </tr>
        </thead>

        <tbody>

            @foreach ($userMsg as $message)
                <tr data-id="{{ $message }}">
                    <td class="align-middle">
                        <a href="{{ route('admin.message.show', $message) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                    </td>

                    <td class="align-middle">受信</td>
                    <td class="align-middle">{{ Str::limit($message->text, $limit = 50, $end = '...') }}</td>
                    <td class="align-middle text-center">{{ $message->updated_at }}</td>

                    <td class="text-center">
                        <form action="{{ route('admin.message.hidden', $message) }}" method="post" class="d-inline">
                            @csrf
                            <input class="btn btn-danger" name='action' type="submit" value="復元"
                            onClick="return confirm('元に戻しますか？');">
                        </form>
                    </td>

                </tr>
            @endforeach

            @foreach ($messages as $message)
                <tr data-id="{{ $message }}">
                    <td class="align-middle">
                        <a href="{{ route('admin.message.show', $message) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                    </td>
                    <td class="align-middle">
                        {{ $message->text ? '送信済み' :  '下書き'}}
                    </td>

                    <td class="align-middle">
                        {{ $message->text ? Str::limit($message->text, $limit = 50, $end = '...') : $message->draft }}
                    </td>
                    <td class="align-middle text-center">{{ $message->deleted_at }}</td>

                    <td class="text-center">
                        <form action="{{ route('admin.message.restore', $message) }}" method="post" class="d-inline">
                            @csrf
                            <input class="btn btn-danger" type="submit" value="復元"
                            onClick="return confirm('元に戻しますか？');">
                        </form>
                    </td>

                </tr>
            @endforeach

        </tbody>
    </table>
</div>
</body>
</html>