<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>お知らせ編集</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('admin.information.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">お知らせ編集</div>

            <form action="{{ route('admin.information.update', $information) }}" method="post">
                @csrf
                @method('patch')
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">タイトル
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title"  value="{{ $information->title }}" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="text" id="text" rows="5">{{ $information->text }}</textarea>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold">対象グループ</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="group" id="group">
                                <option value="1">グループ1</option>
                            </select>
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="登録">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
</html>