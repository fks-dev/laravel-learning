<!DOCTYPE html>
<html lang="ja">
<head>
    <title>お知らせ一覧</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <a href="{{ route('users.index') }}">HOME</a>
    <div class="border rounded m-2">
        <div class="bg-success rounded-top p-2">
            <p>お知らせ一覧</p>
        </div>
        <div class="m-2">
            <table class="table p-2 bg-prime">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>タイトル</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($informations as $information)
                    <tr>
                        <td>{{$information->created_at}}</td>
                        <td><a href="#">{{$information->title}}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                <p class="text-center">ページ 1/1</p>
            </div>
        </div>
    </div>


</body>
</html>