<header>
    <nav class="navbar p-0 bg-primary  ">
        <div class="d-flex justify-content-between align-items-center container-fluid">
            <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learnig</h2>
            <ul class="nav me-2 text-white">
                @isset($user)
                <li class="nav-item border-end p-1">ようこそ{{ $user->username }}さん</li>
                <li id="setting" class="nav-item border-end p-1 position-relative" ><a class="link-underline text-white" href="#">設定</a>
                        <ul id="setting-menu" class="fs-6 shadow badge position-absolute start-50 top-100 translate-middle-x" >
                            <li class="setting-item text-primary" ><a class="link-underline " href="#">パスワード変更<a></li>
                        </ul>
                    </li>
                <li class="nav-item p-1"><a class="link-underline text-white" href="{{ route('users.logout') }}">ログアウト</a></li>
                @endisset
            </ul>
        </div>
    </nav>
        @include('admin.passwordChange')

</header>