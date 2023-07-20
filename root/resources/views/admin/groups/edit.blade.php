<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.groups.head')
    <title>グループ編集</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('group.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">グループ編集</div>

            <form action="{{ route('group.update', $group) }}" method="post">
                @csrf
                @method('patch')
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="group_name">グループ名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="group_name" id="group_name" value="{{ $group->group_name }}" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="introduction">受講コース</label>
                        <div class="col-sm-10">
                            <select name="course" id="course" class="form-control"></select>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="remarks">備考</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="remarks" id="remarks" rows="5">{{ $group->remarks }}</textarea>
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="更新">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
</html>