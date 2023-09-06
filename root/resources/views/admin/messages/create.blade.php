<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>新規メッセージ</title>
</head>
<body>
<div class="mt-3 container">
    <a href="{{ $backRoute }}?page={{ $currentPage }}">&lt;&lt;戻る</a>
    <div class="border">
        <div class="p-2 bg-secondary text-white">受講者にメッセージを作成</div>

        <form action="{{ route('admin.message.store') }}" method="post">
            @csrf
            <div class="mx-5 px-5">

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="title">件名
                        <span class="text-danger fw-bold">＊</span>
                    </label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="title" id="title" required>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="user_id">宛先</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="user_id" id="user_id">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="text" id="text" rows="5"></textarea>
                        <input class="form-contorl btn btn-primary mt-3" type="submit" name="action" value="送信">
                        <input class="form-contorl btn btn-secondary mt-3" type="submit" name="action" value="下書き">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>