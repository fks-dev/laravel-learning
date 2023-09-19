<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.groups.head')
    <title>コース</title>
</head>
<body>
    <div class="container mt-3 border">
        <a href="{{ route('admin.group.index') }}">&lt;&lt;戻る</a>
        <h2 class="mt-3">{{ $group->group_name }}</h2>
        <div class="d-flex">
            <div class="p-3">
                <h3>所属コース</h3>
                <div>
                    <ul>
                        @foreach ($group->courses as $course)
                            <li class="align-items-center">{{ $course->title }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="p-3">
                <h3>所属ユーザー</h3>
                <div>
                    <ul>
                        @foreach ($group->users as $user)
                            <li class="align-items-center">{{ $user->username }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="p-3">
                <div class="mt-2">
                    <div class="fw-bold fs-5">作成日時</div>
                    <div>{{ $group->created_at }}</div>
                </div>
                <div class="mt-2">
                    <div class="fw-bold fs-5">更新日時</div>
                    <div>{{ $group->updated_at }}</div>
                </div>
                <div class="mt-2">
                    <div class="fw-bold fs-5">Action</div>
                    <div class="action-btn">
                        <a class="btn btn-success" href="{{ route('admin.group.edit', ['group' => $group, 'show' => 'show']) }}">編集</a>

                        <form action="{{ route('admin.group.destroy', $group) }}" method="post" class="d-inline">
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
</body>
</html>