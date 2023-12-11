<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>管理者編集</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-3 container">
        <a href="{{ route('admin.admin-management.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">管理者編集</div>

            <form action="{{ route('admin.admin-management.update', $adminUser) }}" method="post">
                @csrf
                @method('patch')
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="username">管理者ID
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control @error('username') is-invalid @enderror" type="text" name="username" id="username"
                                value="{{ $adminUser->username }}" required>
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
                            <a href="{{ route('admin.admin-management.password', $adminUser) }}" class="btn btn-success form-control">
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
                                value="{{ $adminUser->mail_address }}" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9">
                            {{-- 登録ボタン --}}
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="更新">
                        </div>
                    </div>

                </div>
            </form>
            <div class="mx-5 px-5">
                <div class="row m-3">
                    <div class="col-sm-3 col-form-label fw-bold" for="group"></div>
                    <div class="col-sm-9">
                        {{-- 削除ボタン --}}
                        <form action="{{ route('admin.admin-management.destroy', $adminUser) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <input class="btn btn-danger mt-3" type="submit" value="削除"
                            onClick="return confirm('本当に削除しますか？');">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>