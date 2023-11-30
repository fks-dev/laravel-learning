<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Choices.jsのCSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <link rel="stylesheet" href="/css/select.css">


    @include('head')
    <title>ユーザー編集</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-3 container">
        <a href="{{ route('admin.user-management.index') }}">&lt;&lt;戻る</a>
        <div class="border">
            <div class="p-2 bg-secondary text-white">ユーザー編集</div>

            <form action="{{ route('admin.user-management.update', $user) }}" method="post">
                @csrf
                @method('patch')
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="username">ユーザーID
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control @error('username') is-invalid @enderror" type="text" name="username" id="username"
                            value="{{ $user->username }}" required>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="password">パスワード
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <a href="{{ route('admin.user-management.password', $user) }}" class="btn btn-success form-control">
                                パスワードの変更
                            </a>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="mail_address">メールアドレス
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="email" name="mail_address" id="mail_address"
                            value="{{ $user->mail_address }}" required>
                        </div>
                    </div>

                    <div class="row m-3">
                    <label class="col-sm-3 col-form-label fw-bold">所属グループ</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="groups[]" id="group" multiple>
                                <option disabled>グループを選んでください</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}"
                                       @if ($user->groups->contains($group->id)) selected @endif>
                                       {{ $group->group_name }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- 登録ボタン --}}
                            <input class="form-contorl btn btn-primary" type="submit" value="更新">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    @include('admin.user-management.courseSelect')
</body>
</html>