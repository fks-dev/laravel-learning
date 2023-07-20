<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.groups.head')
    <title>グループ登録</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('group.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規グループ登録</div>

            <form action="{{ route('group.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="group_name">コース名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="group_name" id="group_name" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="course">受講コース</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="course" id="course">
                                <option hidden>受講するコースを選択してください。（複数選択可）</option>
                                <option value="1">1</option>
                                <option value="1">2</option>
                                <option value="1">3</option>
                            </select>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="remarks">備考</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="remarks" id="remarks" rows="5"></textarea>
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="登録">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>