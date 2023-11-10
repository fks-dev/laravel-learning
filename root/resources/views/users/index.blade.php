<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    @include('head')
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/css/user_index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー画面</title>
</head>

<body>
    @include('header')
    <main >
        <div class="container-md mt-4">
            <div class="card ">
                <div class="card-header bg-success-subtitle text-success shadow-sm ">
                    お知らせ
                </div>
                <div class="card-body">
                    <div class="alert bg-dark-subtitle shadow-sm">
                        全体のお知らせを表示します。<br>
                        このお知らせ管理機能の「システム設定」にて変更可能です。
                    </div>
                    <table class="table table-striped table-hover">
                        <tbody>
                            @foreach($informations as $information)
                            <tr>
                                <th>
                                    {{ date_format($information->created_at, 'Y/m/d') }} &ensp; <a class="none-underline" href="{{ route('users.information.show', $information) }}">{{ $information->title}}</a>
                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-end pe-3 pb-1">
                    <a class="none-underline" href="{{ route('users.information.list')}}">一覧を表示</a>
                </div>
            </div>
        </div>
        <div class="container-md my-4">
            <div class="card ">
                <div class="card-header text-primary bg-primary-subtitle shadow-sm ">
                    コース一覧
                </div>
                <div class="card-body">
                    <div class="card ">
                        <div class="card-body p-0">
                            <ul class="list-group">
                                <li
                                    class="list-group-item list-group-item-action">
                                    <a class="course-link course-link-hover d-flex justify-content-between align-items-center container-fluid" href="#">
                                    <div>
                                        <h6>おすすめ動画診断</h6>
                                        質問項目に回答いただくとおすすめ動画を提案させていただきます。
                                    </div>
                                    <div class="badge bg-primary fs-6">
                                        全３-５問
                                    </div>
                                    </a>
                                </li>
                                @foreach($courses as $course)
                                <li
                                    class="list-group-item list-group-item-action">
                                    <a class="course-link course-link-hover d-flex justify-content-between align-items-center container-fluid" href="{{ route('users.content.index', $course) }}" >
                                    <div>
                                        <h5>{{ $course->title }}</h5>
                                        学習開始日：2023/02/17　前回学習日：2023/10/06
                                    </div>
                                    <div class="badge bg-danger fs-5">
                                        残り <span class="bg-white text-danger  rounded-pill px-2 fs-6">00</span>
                                    </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>

    </footer>
</body>

</html>
