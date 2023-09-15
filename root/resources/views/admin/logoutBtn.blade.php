<div class="mb-5">
    {{-- ログアウトボタン --}}
    <form action="{{ route('admin.login.destroy')}}" method="POST">
        @method('DELETE')
        @csrf
        <button class="btn btn-secondary" type="submit">ログアウト</button>
    </form>
</div>
