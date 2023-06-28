<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者画面</title>
</head>
<body>
    <h1>管理者ホーム</h1>
    <main>
        @auth('admin')
            <p>あなたは管理者です。</p>
            <p>ログイン中です。</p>
        @endauth
        <form action="{{ route('admin.login.destroy')}}" method="POST">
            @method('DELETE')
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </main>
</body>
</html>