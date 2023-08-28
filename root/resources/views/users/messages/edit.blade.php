<!DOCTYPE html>
<html lang="ja">
<head>
    @include('admin.courses.head')
    <title>送信</title>
</head>
<body>
<div class="mt-3 container">
    <a href="{{ route('user.message.draft') }}">&lt;&lt;戻る</a>
    <div class="border">
        <div class="p-2 bg-secondary text-white">先生にメッセージを作成</div>

        <form action="{{ route('user.message.update', $message) }}" method="post">
            @csrf
            @method('patch')
            <div class="mx-5 px-5">

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="title">件名
                        <span class="text-danger fw-bold">＊</span>
                    </label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="title" id="title" value="{{ $message->title }}" required>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="admin_id">宛先</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="admin_id" id="admin_id">
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}"
                                    @if ($message->admin_id == $admin->id) selected @endif
                                    >{{ $admin->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="text" id="text" rows="5">{{ $message->draft }}</textarea>
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