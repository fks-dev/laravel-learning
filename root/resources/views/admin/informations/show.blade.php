<html lang="ja">
<head>
    @include('head')
    <title>お知らせ</title>
</head>
<body>
    @include('admin.header')
    <div class="container mt-3 border">
        <div class="mb-2">
            <a class="btn btn-secondary" href="{{ route('admin.informations.index') }}">戻る</a>
        </div>
        <h3 class="rounded-top p-2 card-header text-success shadow-sm" style="background-color :#cdeee0">{{ $information->title }}</h3>
        <div class="d-flex flex-column">
            <div class="p-3">
                <h4>本文</h4>
                <div>
                    <ul style="padding-left:0px !important;">
                        <li class="list-unstyled">{{ $information->text }}</li>
                    </ul>
                </div>
            </div>
            <div class="d-flex flex-column">
                <div class="mt-2 p-2">
                    <div class="fw-bold fs-5">作成日時</div>
                    <div>{{ $information->created_at }}</div>
                </div>
                <div class="mt-2 p-2">
                    <div class="fw-bold fs-5">更新日時</div>
                    <div>{{ $information->updated_at }}</div>
                </div>
                <div class="mt-2 p-2">
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
    {{-- footer --}}
    @include('footer')
</body>
</html>
