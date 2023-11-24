<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>新規コース登録</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-3 container">
        <a href="{{ route('admin.courses.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">コース編集</div>

            <form action="{{ route('admin.courses.update', $course) }}" method="post">
                @csrf
                @method('patch')
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">コース名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" value="{{ $course->title }}" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="introduction">コース紹介</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="introduction" id="introduction" rows="5">{{ $course->introduction }}</textarea>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="remarks">備考</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="remarks" id="remarks" rows="5">{{ $course->remarks }}</textarea>
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="更新">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
</html>