<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ユーザー画面</title>
</head>
<body>
    <h1>ユーザー画面</h1>
    <main>
        @auth('web')
            <p>あなたはユーザーです。</p>
            <p>ログイン中です。</p>
        @endauth
        <form action="{{ route('users.login.destroy') }}" method="POST">
            @method('DELETE')
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </main>
</body>
</html>