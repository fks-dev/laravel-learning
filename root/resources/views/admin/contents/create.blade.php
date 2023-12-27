<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>新規コンテンツ登録</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-3 container">
        <a href="{{ route('admin.contents.index', $course) }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規コンテンツ登録</div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- フォーム --}}
            <form action="{{ route('admin.contents.store', $course) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mx-5 px-5">
                    {{-- コンテンツ名 --}}
                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">コンテンツ名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" required>
                        </div>
                    </div>

                    {{-- コース選択 --}}
                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">所属コース
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control" name="course_id" id="course_id">
                                @foreach ($courses as $index)
                                    @if ($index->id == $course)
                                        <option value="{{ $index->id }}" selected>{{ $index->title }}</option>
                                    @else
                                        <option value="{{ $index->id }}">{{ $index->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- コンテンツ入力内容 --}}
                    @include('admin.contents.input')

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold">ステータス
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10 mt-2">
                            <div>
                                <input class="form-contorl btn btn-primary mt-3" type="submit" value="登録">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')

</body>
</html>