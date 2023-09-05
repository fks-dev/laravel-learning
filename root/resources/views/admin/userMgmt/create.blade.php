<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Choices.jsのCSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <link rel="stylesheet" href="/css/select.css">

    @include('admin.head')
    <title>ユーザー登録</title>

</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('userMgmt.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規ユーザー登録</div>

            <form action="{{ route('userMgmt.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="username">ユーザーID
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="username" id="username" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="password">パスワード
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="password" id="password" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="mail_address">メールアドレス
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="email" name="mail_address" id="mail_address" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="group">所属グループ</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="group" id="group">
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="course">所属コース</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="course[]" id="course" multiple>
                                <option disabled>コースを選んでください</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>

                            {{-- 登録ボタン --}}
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="登録">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    @include('admin.userMgmt.courseSelect')
</body>
</html>