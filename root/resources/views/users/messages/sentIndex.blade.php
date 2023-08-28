<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>メッセージ一覧</title>
</head>

<body>
<div class="mt-5 container">
    <div class="d-flex justify-content-between">
        <h2 class="col">送信済み一覧</h2>
        <div class="col-auto">
            <a class="btn btn-info" href="{{ route('user.message.index')}}">受信</a>
            <a class="btn btn-success" href="{{ route('user.message.draft')}}">下書き</a>
            <a class="btn btn-primary" href="{{ route('user.message.create')}}">&plus;追加</a>
        </div>
    </div>
{{-- 登録・削除　メッセージ --}}
    <div id="message"></div>
    @if (session('message'))
        <div class="alert alert-success">
        {{ session('message') }}
        </div>
    @elseif (session('danger'))
    <div class="alert alert-danger">
        {{ session('danger') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="col-3">件名</th>
                <th class="col-2">宛先</th>
                <th class="col-5">本文</th>
                <th class="col-2 text-center">送信日時</th>
                <th class="col-2 text-center">Actions</th>
                </tr>
        </thead>

        <tbody>

            @foreach ($messages as $message)
                <tr data-id="{{ $message->id }}">
                    <td class="align-middle">
                        <a href="{{ route('user.message.sent.show', $message) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                    </td>

                    <td class="align-middle">
                        @foreach ($admins as $admin)
                            {{ $message->admin_id == $admin->id ? $admin->username : ''}}
                        @endforeach
                    </td>
                    <td class="align-middle">{{ Str::limit($message->text, $limit = 50, $end = '...') }}</td>
                    <td class="align-middle text-center">{{ $message->created_at }}</td>

                    <td class="text-center">
                        <form action="{{ route('user.message.destroy', $message) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <input class="btn btn-danger" type="submit" value="削除"
                            onClick="return confirm('本当に削除しますか？');">
                        </form>
                    </td>

                </tr>
            @endforeach

        </tbody>
    </table>
</div>
</body>
</html>