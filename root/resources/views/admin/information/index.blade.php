<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    @include('admin.sort')
    <title>お知らせ一覧</title>
</head>
<body>
    <div class="mt-5 container">
        <div class="d-flex justify-content-between">
            <h2 class="col">お知らせ一覧</h2>
            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('admin.information.create')}}">&plus;追加</a>
            </div>
        </div>
{{-- 登録・削除　メッセージ --}}
        <div id="message">
            @if (session('message'))
                <div class="alert alert-success">
                {{ session('message') }}
                </div>
            @elseif (session('danger'))
            <div class="alert alert-danger">
                {{ session('danger') }}
                </div>
            @endif
        </div>

        <table class="table table-striped" id="sortable">
            <thead>
                <tr>
                    <th class="col-3" data-sort="asc">タイトル</th>
                    <th class="col-3">対象グループ</th>
                    <th class="col-2 text-center" data-sort="asc">作成日時</th>
                    <th class="col-2 text-center" data-sort="asc">更新日時</th>
                    <th class="col-2 text-center">Actions</th>
                    </tr>
            </thead>

            <tbody>

                @foreach ($informations as $information)
                    <tr data-id="{{ $information->id }}">
                        <td class="align-middle">{{ $information->title }}</td>
                        <td class="align-middle text-center"></td>
                        <td class="align-middle text-center">{{ $information->created_at }}</td>
                        <td class="align-middle text-center">{{ $information->updated_at }}</td>

                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('admin.information.edit', $information) }}">編集</a>

                            <form action="{{ route('admin.information.destroy', $information) }}" method="post" class="d-inline">
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