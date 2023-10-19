<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    @include('admin.head')
    <link rel="stylesheet" href="/css/user_index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course_title }}</title>
</head>

<body>
    <header>
        <nav class="navbar p-0 bg-primary  ">
            <div class="d-flex justify-content-between align-items-center container-fluid">
                <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learnig</h2>
                <ul class="nav me-2 text-white">
                    <li class="nav-item border-end p-1">ようこそ{{ $user->username }}さん</li>
                    <li class="nav-item border-end p-1"><a class="link-underline text-white" href="#">設定</a></li>
                    <li class="nav-item p-1"><a class="link-underline text-white" href="#">ログアウト</a></li>
                </ul>
            </div>
        </nav>

    </header>
    <main>
        <div class="border rounded m-2">
            <div class="rounded-top p-2">
                <p>{{ $course_title }}</p>
            </div>
            <div class="m-2">
                <table class="table p-2 bg-prime">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>種別</th>
                            <th>学習開始日</th>
                            <th>前回学習日</th>
                            <th>学習時間</th>
                            <th>学習回数</th>
                            <th>理解度</th>
                            <th>完了</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contents as $content)
                        <tr>
                            <td><a href="{{ route('users.content.show', $content) }}">{{$content->title}}</a></td>
                            <td><p>{{$content->content_type}}</p></td>
                            <td><p>????/??/??</p></td>
                            <td><p>????/??/??</p></td>
                            <td><p>??:??:??</p></td>
                            <td><p>?</p></td>
                            <td><p>?</p></td>
                            <td><p>☑</p></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    <p class="text-center">ページ 1/1</p>
                </div>
            </div>
        </div>
    </main>
    <footer>

    </footer>
</body>

</html>