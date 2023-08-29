<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>メッセージ一覧</title>
</head>

<body>
<div class="mt-5 container">
    <div class="d-flex justify-content-between">
        <h2 class="col">受信一覧</h2>
        <div class="col-auto">
            <a class="btn btn-primary" href="{{ route('admin.message.create')}}">&plus;追加</a>
            <a class="btn btn-success" href="{{ route('admin.message.draft')}}">下書き</a>
            <a class="btn btn-secondary" href="{{ route('admin.message.sent')}}">送信済み</a>
            <a class="btn btn-danger" href="{{ route('admin.message.dust')}}">ゴミ箱</a>
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
                <th class="col-2">差出人</th>
                <th class="col-5">本文</th>
                <th class="col-2 text-center">受信日時</th>
                <th class="col-2 text-center">Actions</th>
                </tr>
        </thead>

        <tbody>

            @foreach ($messages as $message)
                <tr data-id="{{ $message->id }}">
                    <td class="align-middle">
                        <a href="{{ route('admin.message.show', $message) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                        @if ($message->is_replied == 1) &#9166; @endif
                    </td>
                    <td class="align-middle">
                        @foreach ($users as $user)
                            {{ $message->user_id == $user->id ? $user->username : ''}}
                        @endforeach
                    </td>
                    <td class="align-middle">{{ Str::limit($message->text, $limit = 50, $end = '...') }}</td>
                    <td class="align-middle text-center">{{ $message->created_at }}</td>

                    <td class="text-center">
                        <form action="{{ route('admin.message.hidden', $message) }}" method="post" class="d-inline">
                            @csrf
                            <input class="btn btn-danger" name="action" type="submit" value="削除"
                            onClick="return confirm('本当に削除しますか？');">
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $messages->links('pagination::bootstrap-5') }}
</div>
</body>
</html>