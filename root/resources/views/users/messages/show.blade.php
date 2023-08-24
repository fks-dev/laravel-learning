<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>メッセージ内容</title>
</head>
<body>
    <div class="mt-5 container">
        <div class="mb-2">
            <a class="btn btn-secondary" href="{{ isset($user) ? route('user.message.sent') : route('user.message.index') }}">戻る</a>
        </div>
        <div class="mb-2">
            @if ( empty($user) )
                <a class="btn btn-success" href="{{ route('user.message.reply', $message) }}">返信</a>
            @endif
        </div>

        <div>
            <p>件名：{{ $message->title }}</p>
        </div>
        <div>
            <p>@if (isset($user)) 宛先 @else 差出人 @endif：
                @foreach ($admins as $admin)
                    {{ $message->admin_id == $admin->id ? $admin->username : '' }}
                @endforeach
            </p>
        </div>
        <div>
            <p>本文</p>
            {!! nl2br(htmlspecialchars($message->text)) !!}
        </div>
    </div>
</body>
</html>