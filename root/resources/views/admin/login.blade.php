<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.head')
    <title>管理者ログイン画面</title>
</head>
<body>
    <div class="mt-5 container">
        <h2>管理者ログイン画面</h2>
        <form action="{{ route('admin.login') }}" method="POST">
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
    </div>
</body>
</html>