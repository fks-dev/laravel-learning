<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ユーザーログイン画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    {{-- header --}}
    <header>
        <nav class="navbar p-0 bg-primary  ">
            <div class="d-flex justify-content-between align-items-center container-fluid">
                <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learnig</h2>
                <ul class="nav me-2 text-white">
                </ul>
            </div>
        </nav>

    </header>

    {{-- body --}}
    <body>
        <div id="container">
            <div id="content" class="row">
                <div class="users-login">
                    <div class="panel panel-info form-signin">
                        <div class="panel-heading text-bg-info p-3">ユーザーログイン画面</div>
                        <div class="panel-body text-bg-light p-3">
                            <form action="{{ route('users.login') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <div class="required">
                                        <label for="username">
                                            ユーザー名
                                        </label>
                                        <div class="input text required">
                                            <input type="text" name="username" id="username" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="required">
                                        <label for="password">
                                            パスワード
                                        </label>
                                        <div class="input password required">
                                            <input type="password" name="password" id="password" class="form-control" required />
                                        </div>
                                    </div>
                                    <input type="checkbox" id="remember_me" value="1" checked="checked">
                                    ログイン状態を保持
                                </div>
                                <div class="submit">
                                    @error('failed')
                                    <p style="color:red">{{ $message }}</p>
                                    @enderror
                                    <button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>

</html>