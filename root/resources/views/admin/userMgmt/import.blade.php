<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.head')
    <title>ユーザーインポート</title>
</head>
<body>
    <div class="mt-3 container">
        <a href="{{ route('userMgmt.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">ユーザーインポート</div>
            <div class="m-3">
                <ul>
                    <li class="mb-2">ユーザ情報が格納されたCSVファイルを選択し、インポートを行って下さい。</li>
                    <li class="mb-2">CSVファイルの文字コードは「UTF-8」を使用してください。</li>
                    <li class="mb-2">1行目はヘッダー行として扱われます。</li>
                    <li class="mb-2">パスワードの指定は任意です。指定されていない場合は、既存のパスワードが保持されます。</li>
                    <li>インポート処理がタイムアウトする場合は、CSVファイルを分割してインポートしてください。</li>
                </ul>

                <div class="ms-3">
                    <span>CSVの形式</span>
                </div>
                <div class="d-flex justify-content-start ms-3 mb-3">
                    <div class="p-2 border border-dark">ID</div>
                    <div class="p-2 border border-dark border-start-0">ユーザー名</div>
                    <div class="p-2 border border-dark border-start-0">パスワード</div>
                    <div class="p-2 border border-dark border-start-0">メールアドレス</div>
                </div>

                <form action="{{ route('userMgmt.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="ms-3">
                        <input type="file" name="csv_file">
                    </div>
                    <div class="ms-3 my-3">
                        <button type="submit" class="btn btn-primary">インポート</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>