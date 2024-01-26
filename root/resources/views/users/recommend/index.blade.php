<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <link rel="stylesheet" href="/css/recommend.css">
    <link rel="stylesheet" href="/css/user_index.css">
    <title>動画相性診断</title>
</head>

<body>
@include('users.header')
<div class="container my-4">
    <div class="card bg-primary-subtitle">
        <div class="card-body text-center">
            <h3>{{ $q['q_order']}}問目</h3>
        </div>
    </div>
    <p>{{ $q['text'] }}</p>
    <div class="d-grid gap-2">
        <form method="get" action="{{ route('users.select-courses.index') }}" class="answer">
            <input type="hidden" value="{{ $q['q_id'] }}" name="q_id">
            <input type="hidden" value="yes" name="answer">
            <input type="submit" value="はい" class="btn btn-primary">
        </form>

        <form method="get" action="{{ route('users.select-courses.index') }}" class="answer">
            <input type="hidden" value="{{ $q['q_id'] }}" name="q_id">
            <input type="hidden" value="no" name="answer">
            <input type="submit" value="いいえ" class="btn btn-danger">
        </form>
    </div>
</div>
{{-- footer --}}
@include('footer')
</body>
