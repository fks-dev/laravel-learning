<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>メッセージ内容</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-5 container">
        <div class="mb-2">
            <a class="btn btn-secondary" href="{{ $backRoute }}?page={{$currentPage}}">戻る</a>
        </div>
        <div class="mb-2">
            @if ( $source == false )
                <a class="btn btn-success" href="{{ route('admin.messages.reply', $message) }}">返信</a>
            @endif
        </div>

        <div>
            <p>件名：{{ $message->title }}</p>
        </div>
        <div>
            <p>@if ( $source == true ) 宛先 @else 差出人 @endif：
                @foreach ($users as $user)
                    {{ $message->user_id == $user->id ? $user->username : '' }}
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
