<!DOCTYPE html>
<html lang="ja" class="h-100" data-bs-theme="auto">

<head>
  @include('head')
  <link href="/css/welcome.css" rel="stylesheet">
</head>

<body class="d-flex h-100 text-center text-bg-dark">
  <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <main class="px-3">
      <h1>Cover your page.</h1>
      <p class="lead">Cover is a one-page template for building simple and beautiful home pages. Download, edit the text, and add your own fullscreen background photo to make it your own.</p>
      <p class="lead">
        <a href="{{ route('admin.login.index') }}" class="btn btn-lg btn-light fw-bold border-white bg-white">Learn more</a>
      </p>
    </main>
  </div>
  <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>