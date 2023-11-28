<html lang="ja">
<head>
@include('head')
<title>お知らせ</title>
</head>
<body>
    @include('admin.header')
    <div class="container mt-3 border">
        <a href="{{ route('admin.informations.index') }}" >&lt;&lt;戻る</a>
        <h1 class="mt-3">{{ $information->title }}</h1>
        <div class="d-flex">
            <div class="p-3">
                <h2>本文</h2>
                <div>
                    <ul>
                        <li class="align-items-center">{{ $information->text }}</li>
                    </ul>
                </div>
            </div>
            <div class="p-3">
                <div class="mt-2">
                    <div class="fw-bold fs-5">作成日時</div>
                    <div>{{ $information->created_at }}</div>
                </div>
                <div class="mt-2">
                    <div class="fw-bold fs-5">更新日時</div>
                    <div>{{ $information->updated_at }}</div>
                </div>
                <div class="mt-2">
                <div class="fw-bold fs-5">Action</div>
                    <div class="action-btn">
                        <a class="btn btn-success" href="{{ route('admin.informations.edit', ['information' => $information->id]) }}">編集</a>
                        <form action="{{ route('admin.informations.destroy', $information) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <input class="btn btn-danger" type="submit" value="削除"
                            onClick="return confirm('本当に削除しますか？');">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>