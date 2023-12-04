<!DOCTYPE html>
<html lang="ja">

<head>
  @include('head')
</head>

<body class="d-flex text-center text-bg-light">
  <div class="p-3 mx-auto">
    <main>
      <h1>Laravel-learing</h1>
      <p class="lead">Laravel-learningのデフォルトページです。</p>
      <p class="lead">
        <a href="{{ route('admin.login.index') }}" class="btn btn-primary">ログイン画面へ</a>
      </p>
    </main>
  </div>
</body>

</html>