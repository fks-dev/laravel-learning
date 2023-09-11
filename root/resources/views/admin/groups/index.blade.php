<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="/css/sort.css">
    <link rel="stylesheet" href="/css/indexTable.css">
    @include('admin.groups.head')
    <title>グループ登録</title>
</head>
<body>
    <div class="mt-5 container">
        <div class="d-flex justify-content-between">
            <h2 class="col">グループ一覧</h2>
            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('group.create')}}">&plus;追加</a>
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
        @endif

        <div class="table-container">
        <table class="table table-striped" id="sortable">
            <thead>
                <tr>
                    <th class="col-3 sort" data-sort="asc">グループ名</th>
                    <th class="col-3">受講コース</th>
                    <th class="col-2 text-center sort" data-sort="asc">作成日時</th>
                    <th class="col-2 text-center sort" data-sort="asc">更新日時</th>
                    <th class="col-1 text-center">Actions</th>
                    </tr>
            </thead>

            <tbody>

                @foreach ($groups as $group)
                    <tr data-id="{{ $group->id }}">
                        <td class="align-middle">
                            <div class="scrollable">
                                <a href="{{ route('group.show', $group) }}">{{ $group->group_name }}</a>
                            </div>
                        </td>
                        <td class="align-middle">
                            <div class="scrollable">
                            @foreach ($group->courses as $course)
                                {{ $course->title }}
                                @unless($loop->last)
                                    ,
                                @endunless
                            @endforeach
                            </div>
                        </td>
                        <td class="align-middle text-center"><div class="date-table">{{ $group->created_at }}<div></td>
                        <td class="align-middle text-center"><div class="date-table">{{ $group->updated_at }}<div></td>

                        <td class="text-center">
                            <div class="action-btn">
                            <a class="btn btn-success" href="{{ route('group.edit', $group->id) }}">編集</a>

                            <form action="{{ route('group.destroy', $group) }}" method="post" class="d-inline">
                                @csrf
                                @method('delete')
                                <input class="btn btn-danger" type="submit" value="削除"
                                onClick="return confirm('本当に削除しますか？');">
                            </form>
                            </div>
                        </td>
                    </tr>
                @endforeach

            </tbody>

        </table>
        </div>
    </div>

    @include('admin.groups.sort')
</body>
</html>