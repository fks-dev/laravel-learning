<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>新規問い合わせ</title>
</head>
<body>
<div class="mt-3 container">
    <a href="{{ $backRoute }}?page={{ $currentPage }}">&lt;&lt;戻る</a>
    <div class="border">
        <div class="p-2 bg-secondary text-white">先生にメッセージを作成</div>

        <form action="{{ route('users.messages.store') }}" method="post">
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
                    <label class="col-sm-2 col-form-label fw-bold" for="admin_id">宛先</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="admin_id" id="admin_id">
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="text" id="text" rows="5"></textarea>
                        <button class="form-contorl btn btn-primary mt-3" type="submit" name="{{ App\Enums\ActionEnum::SEND->value }}">送信</button>
                        <button class="form-contorl btn btn-secondary mt-3" type="submit" name="{{ App\Enums\ActionEnum::DRAFT->value }}">下書き</button>
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
