<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.head')
    <title>管理者登録</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('admin.adminMgmt.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規管理者登録</div>

            <form action="{{ route('admin.adminMgmt.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="username">管理者ID
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" name="username" id="username" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="password">パスワード
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" name="password" id="password" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="mail_address">メールアドレス
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="email" name="mail_address" id="mail_address" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="group">所属グループ</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" name="group" id="group">

                            {{-- 登録ボタン --}}
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="登録">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
</html>