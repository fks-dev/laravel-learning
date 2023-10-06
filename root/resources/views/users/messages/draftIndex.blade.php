<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>メッセージ一覧</title>
</head>

<body>
<div class="mt-5 container">
    <div class="d-flex justify-content-between">
        <h2 class="col">下書き一覧</h2>
        <div class="col-auto">
            <a class="btn btn-primary" href="{{ route('users.message.create', ['source' => 'draft'])}}">&plus;追加</a>
            <a class="btn btn-info" href="{{ route('users.message.index')}}">受信</a>
            <a class="btn btn-secondary" href="{{ route('users.message.sent')}}">送信済み</a>
            <a class="btn btn-danger" href="{{ route('users.message.dust')}}">ゴミ箱</a>
        </div>
    </div>

    @include('alert')

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="col-3">件名</th>
                <th class="col-2">宛先</th>
                <th class="col-5">本文</th>
                <th class="col-2 text-center">保存日時</th>
                <th class="col-2 text-center">Actions</th>
                </tr>
        </thead>

        <tbody>

            @foreach ($messages as $message)
                <tr data-id="{{ $message->id }}">
                    <td class="align-middle">
                        <a href="{{ route('users.message.edit', $message) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                    </td>

                    <td class="align-middle">
                        @foreach ($admins as $admin)
                            {{ $message->admin_id == $admin->id ? $admin->username : ''}}
                        @endforeach
                    </td>
                    <td class="align-middle">{{ Str::limit($message->text, $limit = 50, $end = '...') }}</td>
                    <td class="align-middle text-center">{{ $message->updated_at }}</td>

                    <td class="text-center">
                        <form action="{{ route('users.message.destroy', $message) }}" method="post" class="d-inline">
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
    {{ $messages->links('pagination::bootstrap-5') }}
</div>
</body>
</html>