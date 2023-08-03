<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.head')
    <link rel="stylesheet" href="/css/mgmt.css">
    <title>ユーザー 一覧画面</title>
</head>
<body>
    <div class="mt-5 container">

        <div class="mb-5">
            {{-- ログアウトボタン --}}
            <form action="{{ route('admin.login.destroy')}}" method="POST">
                @method('DELETE')
                @csrf
                <button class="btn btn-secondary" type="submit">ログアウト</button>
            </form>
        </div>

        @include('admin.menu')

        <div class="d-flex justify-content-between">
            <h2 class="me-4">ユーザー 一覧</h2>
            <div class="me-auto">
                <a class="btn btn-info" href="{{ route('adminMgmt.index') }}">⇆ 管理者一覧</a>
            </div>
            <div class="col-auto">
                <form action="{{ route('userMgmt.csv') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">エクスポート</button>
                </form>
            </div>
            <div class="col-auto  ms-2">
                <a class="btn btn-primary" href="{{ route('userMgmt.import') }}">インポート</a>
            </div>

            <div class="col-auto  ms-2">
                <a class="btn btn-primary" href="{{ route('userMgmt.create')}}">&plus;追加</a>
            </div>

        </div>

            {{-- 検索 --}}
            <form id="searchForm" method="GET">
                @csrf
                <div class="d-flex justify-content-start">
                    <div class="p-2">
                        <label class="col-form-label" for="group">グループ:</label>
                    </div>
                    <div class="p-2">
                        <select class="form-control" name="group" id="group">
                            <option value="1">グループ１</option>
                        </select>
                    </div>
                    <div class="p-2">
                        <label class="col-form-label" for="name">管理者ID</label>
                    </div>
                    <div class="p-2">
                        <input class="form-control" type="text" name="name" id="name">
                    </div>
                    <div class="p-2">
                        <input class="form-control btn btn-info" type="submit" value="検索">
                    </div>
                </div>
            </form>

            <div id="searchResults"></div>

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

            <table class="table table-striped" id="sortable">
                <thead>
                    <tr>
                        <th class="col-1 sort" data-sort="asc">管理者ID</th>
                        <th class="col-2 sort" data-sort="asc">mail</th>
                        <th class="col-2">所属グループ</th>
                        <th class="col-2">所属コース</th>
                        <th class="col text-center sort" data-sort="asc">最終ログイン日時</th>
                        <th class="col text-center sort" data-sort="asc">作成日時</th>
                        <th class="col text-center">Actions</th>
                        </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr  data-id="{{ $user->id }}">
                            <td class="align-middle">{{ $user->username }}</td>
                            <td class="align-middle text-center">{{ $user->mail_address }}</td>
                            <td class="align-middle text-center"></td>
                            <td class="align-middle text-center">
                                @foreach ($user->courses as $course)
                                    {{ $course->title }}
                                    @unless($loop->last)
                                        ,
                                    @endunless
                                @endforeach
                            </td>
                            <td class="align-middle text-center">
                                @foreach ($logins as $login)
                                    @if ($user->id == $login->user_id)
                                        {{ $login->updated_at }}
                                        @break
                                    @endif
                                @endforeach
                            </td>

                            <td class="align-middle text-center">{{ $user->created_at }}</td>

                            <td class="text-center">
                                <a class="btn btn-success" href="{{ route('userMgmt.edit', $user->id) }}">編集</a>

                                <form action="{{ route('userMgmt.destroy', $user) }}" method="post" class="d-inline">
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
    @include('admin.sort')
    @include('admin.userMgmt.search')
</body>
</html>