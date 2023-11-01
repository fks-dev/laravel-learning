<header>
    <nav class="navbar p-0 bg-primary  ">
        <div class="d-flex justify-content-between align-items-center container-fluid">
            <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learnig</h2>
            <ul class="nav me-2 text-white">
                @isset($user)
                <li class="nav-item border-end p-1">ようこそ{{ $user->username }}さん</li>
                <li id="setting" class="nav-item border-end p-1" ><a class="link-underline text-white" href="{{ route('users.password.index') }}">パスワード変更</a></li>
                <li class="nav-item p-1"><a class="link-underline text-white" href="{{ route('users.logout') }}">ログアウト</a></li>
                @endisset
            </ul>
        </div>
    </nav>
</header>
