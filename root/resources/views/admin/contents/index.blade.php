<!DOCTYPE html>
<html lang="ja">
<head>
    {{-- CSRFトークン --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- CSS --}}
    <link rel="stylesheet" href="/css/course.css">
    @include('admin.courses.head')
    <title>コンテンツ</title>
</head>
<body>
    <div class="mt-5 container">
        <div class="d-flex justify-content-between">
            <h2 class="col">コース名『{{ $courseTitle[0]['title'] }}』のコンテンツ</h2>
            <div class="col-auto me-2">
                <a class="btn btn-secondary" href="{{ route('course.index')}}">戻る</a>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('content.create', $course)}}">&plus;追加</a>
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

        <div class="alert alert-warning">
            ドラッグ＆ドロップでコースの並び順が変更できます。
            <button class="btn btn-primary me-md-2" id="saveBtn" disabled>変更確定</button>
            <button class="btn btn-secondary me-md-2" id="backBtn" disabled>元に戻す</button>
        </div>

        <table class="table table-striped" id="sortable">
            <thead>
                <tr>
                    <th class="col-3">コンテンツ名</th>
                    <th class="col-2 text-center">コンテンツ種別</th>
                    <th class="col-1 text-center">ステータス</th>
                    <th class="col-2 text-center">作成日時</th>
                    <th class="col-2 text-center">更新日時</th>
                    <th class="col-2 text-center">Actions</th>
                    </tr>
            </thead>

            <tbody id="tableBody">
                @foreach ($contents as $content)
                    <tr data-id="{{ $content->id }}">
                        <td class="align-middle">
                            <a href="{{ route('content.show', $content->id) }}">{{ $content->title }}</a>
                        </td>
                        <td class="align-middle text-center">{{ $content->kind }}</td>
                        <td class="align-middle text-center">{{ $content->public == 1 ? '公開' : '非公開' }}</td>
                        <td class="align-middle text-center">{{ $content->created_at }}</td>
                        <td class="align-middle text-center">{{ $content->updated_at }}</td>

                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('content.edit', $content->id) }}">編集</a>

                            <form action="{{ route('content.duplicate', $content) }}" method="post" class="d-inline">
                                @csrf
                                <input class="btn btn-info text-white" type="submit" value="複製">
                            </form>

                            <form action="{{ route('content.destroy', $content) }}" method="post" class="d-inline">
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

    @include('admin.contents.sort')
</body>
</html>