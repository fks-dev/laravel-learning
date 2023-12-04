<!DOCTYPE html>
<html lang="ja" class="h-100" data-bs-theme="auto">

<head>
  @include('head')
  <link href="/css/welcome.css" rel="stylesheet">
</head>

<body class="d-flex h-100 text-center text-bg-light">
  <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <main class="px-3">
      <h1>Laravel-learing</h1>
      <p class="lead">Laravel-learningのデフォルトページです。</p>
      <p class="lead">
        <a href="{{ route('admin.login.index') }}" class="btn btn-primary">ログイン画面へ</a>
      </p>
    </main>
  </div>
  <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>