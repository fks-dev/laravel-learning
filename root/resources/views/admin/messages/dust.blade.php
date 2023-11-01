<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>ゴミ箱一覧</title>
</head>

<body>
<div class="mt-5 container">
    <div class="d-flex justify-content-between">
        <h2 class="col">ゴミ箱</h2>
        <div class="col-auto">
            <a class="btn btn-primary" href="{{ route('admin.message.create', ['source' => 'dust'])}}">&plus;追加</a>
            <a class="btn btn-info" href="{{ route('admin.message.index')}}">受信</a>
            <a class="btn btn-success" href="{{ route('admin.message.draft')}}">下書き</a>
            <a class="btn btn-secondary" href="{{ route('admin.message.sent')}}">送信済み</a>
        </div>
    </div>

    @include('alert')

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

            @foreach ($paginator as $message)
                <tr data-id="{{ $message }}">
                    <td class="align-middle">
                        <a href="{{ route('admin.message.show', ['message' => $message, 'source' => 'dust']) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                    </td>

                    <td class="align-middle">
                        @if ($message->is_hidden == 1)
                            受信
                        @elseif($message->action == $action[1])
                            送信済み
                        @else
                            下書き
                        @endif
                    </td>
                    <td class="align-middle">
                        {{ $message->text ? Str::limit($message->text, $limit = 50, $end = '...') : '' }}
                    </td>
                    <td class="align-middle text-center">{{ $message->updated_at }}</td>

                    <td class="text-center">
                        <form
                        action="{{ $message->is_hidden == 0 && $message->deleted_at != null ?
                        route('admin.message.restore', $message) :route('admin.message.hidden', $message) }}"
                        method="post" class="d-inline">
                            @csrf
                            <input class="btn btn-danger" name='action' type="submit" value="復元"
                            onClick="return confirm('元に戻しますか？');">
                        </form>
                    </td>

                </tr>
            @endforeach

        </tbody>
    </table>
    {{ $paginator->links('pagination::bootstrap-5') }}
</div>
</body>
</html>
