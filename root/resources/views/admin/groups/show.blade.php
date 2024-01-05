<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>コース</title>
</head>
<body>
    @include('admin.header')
    <div class="container mt-3 border">
        <div class="mb-4 p-3 border rounded" style="background-color: #f5f5f5;">
            <a class="text-decoration-none" href="{{ route('admin.groups.index') }}">グループ一覧</a>
        </div>
        <h3 class="rounded-top p-2 card-header text-success shadow-sm" style="background-color: #cdeee0">{{ $group->group_name }}</h3>
        <div class="d-flex">
            <div class="p-3">
                <h4>所属コース</h4>
                <div>
                    <ul>
                        @foreach ($group->courses as $course)
                            <li class="align-items-center list-unstyled">{{ $course->title }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="p-3">
                <h4>所属ユーザー</h4>
                <div>
                    <ul>
                        @foreach ($group->users as $user)
                            <li class="align-items-center list-unstyled">{{ $user->username }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="d-flex">
                <div class="mt-2 p-3">
                    <div class="fw-bold fs-5">作成日時</div>
                    <div>{{ $group->created_at }}</div>
                </div>
                <div class="mt-2 p-3">
                    <div class="fw-bold fs-5">更新日時</div>
                    <div>{{ $group->updated_at }}</div>
                </div>
                <div class="mt-2 p-3">
                    <div class="fw-bold fs-5">Action</div>
                    <div class="action-btn">
                        <a class="btn btn-success" href="{{ route('admin.groups.edit', ['group' => $group, 'show' => 'show']) }}">編集</a>

                        <form action="{{ route('admin.groups.destroy', $group) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <input class="btn btn-danger" type="submit" value="削除"
                            onClick="return confirm('本当に削除しますか？');">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3">
            <h3>備考</h3>
            <div class="border">
                <div class="p-2">
                    <p>{!! nl2br(htmlspecialchars($group->remarks)) !!}</p>
                </div>
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>
