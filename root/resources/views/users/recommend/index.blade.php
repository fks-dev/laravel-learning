<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>動画相性診断</title>
</head>

<body>
<p>{{ $q['q_order']}}問目</p>
<p>{{ $q['text'] }}</p>

<form method="get" action="{{ route('users.select-courses.index') }}">
    <input type="hidden" value="{{ $q['q_id'] }}" name="q_id">
    <input type="hidden" value="yes" name="answer">
    <input type="submit" value="はい" class="btn btn-primary">
</form>

<form method="get" action="{{ route('users.select-courses.index') }}">
    <input type="hidden" value="{{ $q['q_id'] }}" name="q_id">
    <input type="hidden" value="no" name="answer">
    <input type="submit" value="いいえ" class="btn btn-danger">
</form>
</body>
