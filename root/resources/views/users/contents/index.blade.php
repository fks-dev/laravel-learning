<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>{{ $course_title }}</title>
</head>

<body>
    @include('users.header')
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
                            <th>学習開始日</th>
                            <th>前回学習日</th>
                            <th>完了</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contents as $content)
                        <tr>
                            <td><a href="{{ route('users.contents.show', $content) }}">{{$content->title}}</a></td>
                            <td><p>????/??/??</p></td>
                            <td><p>????/??/??</p></td>
                            <td><p>☑</p></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <footer>

    </footer>
</body>

</html>