<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Choices.jsのCSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    {{-- CSS --}}
    <link rel="stylesheet" href="/css/select.css">
    @include('admin.groups.head')
    <title>グループ登録</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('admin.groups.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規グループ登録</div>

            <form action="{{ route('admin.groups.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="group_name">グループ名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="group_name" id="group_name" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold">所属コース</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="course[]" id="course" multiple>
                                <option disabled>コースを選んでください</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold">所属ユーザー</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="user[]" id="user" multiple>
                                <option disabled>ユーザーを選択してください</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
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

@include('admin.groups.courseSelect')
@include('admin.groups.userSelect')
</body>
</html>