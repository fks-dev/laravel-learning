<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ユーザーログイン画面</title>
</head>
<body>
    <h2>ユーザーログイン画面</h2>
    <form action="{{ route('users.login.store') }}" method="POST">
        @csrf
        <div>
            <label for="username">ユーザー名：</label>
            <input type="text" name="username" id="username" required />
        </div>
        <div>
            <label for="password">パスワード：</label>
            <input type="password" name="password" id="password" required />
        </div>
        <div>
            @error('failed')
                <p style="color:red">{{ $message }}</p>
            @enderror
            <button type="submit">ログイン</button>
        </div>
    </form>
</html>