<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.head')
    <title>管理者編集</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('admin.admin-management.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">管理者編集</div>

            <form action="{{ route('admin.admin-management.update', $admin) }}" method="post">
                @csrf
                @method('patch')
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="username">管理者ID
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control @error('username') is-invalid @enderror" type="text" name="username" id="username"
                                value="{{ $admin->username }}" required>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="password">パスワード
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <a href="{{ route('admin.admin-management.password', $admin) }}" class="btn btn-success form-control">
                                パスワードの変更
                            </a>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="mail_address">メールアドレス
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="email" name="mail_address" id="mail_address"
                                value="{{ $admin->mail_address }}" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="group">所属グループ</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="email" name="group" id="group">
                            {{-- 登録ボタン --}}
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="更新">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
</html>