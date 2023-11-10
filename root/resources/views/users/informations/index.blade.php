<!DOCTYPE html>
<html lang="ja">
<head>
    <title>お知らせ一覧</title>
    @include('head')
</head>

<body>
    @include('header')
    <div class="container-md mt-4">
        <div class="mb-4 p-3 border rounded" style="background-color: #f5f5f5;">
            <a class="text-decoration-none" href="{{ route('users.index') }}">HOME</a>
        </div>
        <div class="border rounded">
            <div class="rounded-top p-2 card-header text-success shadow-sm" style="background-color: #cdeee0">
                <p>お知らせ一覧</p>
            </div>
            <div class="m-2">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>タイトル</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold">
                        @foreach($informations as $information)
                        <tr>
                            <td>{{$information->created_at->format('Y/m/d')}}</td>
                            <td><a class="none-underline" href="{{ route('users.information.show', $information) }}">{{$information->title}}</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    <p class="text-center">ページ 1/1</p>
                </div>
            </div>
        </div>
    </div>


</body>
</html>