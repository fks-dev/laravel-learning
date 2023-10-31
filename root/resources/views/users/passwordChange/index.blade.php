<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    @include('admin.head')
    <link rel="stylesheet" href="/css/user_index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー画面</title>
</head>

<body>
    @include('header')
    <main>
        <a class="ms-3 mt-3" href="{{ route('users.index') }}">&lt;&lt;戻る</a>
        <div class="container-md mt-4">
            <div class="card">
                <div class="card-header">
                    設定
                </div>
                <div class="card-body">
                        @if (session('error_message'))
                            <div class="alert alert-danger">
                                {{ session('error_message') }}
                            </div>
                        @elseif ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('users.password.change', $user) }}" method="post">
                            @csrf
                            <div class="mx-5 px-5">

                                <div class="row m-3">
                                    <label class="col-sm-3 col-form-label fw-bold" for="password">現在のパスワード
                                        <span class="text-danger fw-bold">＊</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="password" name="password" id="password"
                                            required>
                                    </div>
                                </div>

                                <div class="row m-3">
                                    <label class="col-sm-3 col-form-label fw-bold" for="new_password">新しいパスワード
                                        <span class="text-danger fw-bold">＊</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="password" name="new_password"
                                            id="new_password" required>
                                    </div>
                                </div>

                                <div class="row m-3">
                                    <label class="col-sm-3 col-form-label fw-bold"
                                        for="new_password_confirmation">パスワードを確認
                                        <span class="text-danger fw-bold">＊</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="password" name="new_password_confirmation"
                                            id="new_password_confirmation" required>
                                        {{-- 登録ボタン --}}
                                        <input class="form-contorl btn btn-primary mt-3" type="submit"
                                            value="パスワードを変更">
                                    </div>
                                </div>
                            </div>
                        </form>

                </div>
            </div>
        </div>
    </main>
    <footer>

    </footer>
</body>

</html>
