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
    <header>
        <nav class="navbar p-0 bg-primary  ">
            <div class="d-flex justify-content-between align-items-center container-fluid">
                <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learnig</h2>
                <ul class="nav me-2 text-white">
                    <li class="nav-item border-end p-1">ようこそ{{ $user->username }}さん</li>
                    <li class="nav-item border-end p-1"><a class="link-underline text-white" href="#">設定</a></li>
                    <li class="nav-item p-1"><a class="link-underline text-white"
                            href="{{ route('users.logout') }}">ログアウト</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="container-md mt-4">
            <div class="card">
                <div class="card-header">
                    設定
                </div>
                <div class="card-body">
                    <form action=" {{ route('users.password.update') }} " method="POST">
                        @csrf
                        <div class="container">
                            @error('pass')
                            <div class="text-danger"> {{ $message }} </div>

                            @enderror

                            <div class="row">
                                <label for="pass" class="form-label col-3 fs-5 fw-bold">新しいパスワード</label>
                                <input type="password" id="pass" name="pass" class="form-control col">
                            </div>
                            <div class="row mt-4">
                                <label for="passconf" class="form-label col-3 fs-5 fw-bold">新しいパスワード (確認用)</label>
                                <input type="password" id="passconf" name="passconf" class="form-control col">
                            </div>
                            <div class="row mt-4">
                                <button type="submit" class="offset-3 col-1 btn btn-primary">保存</button>
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
