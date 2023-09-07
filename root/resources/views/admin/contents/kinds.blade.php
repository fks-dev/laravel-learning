<div class="row m-3">
    <label class="col-sm-2 col-form-label fw-bold" for="introduction">コンテンツ種別
        <span class="text-danger fw-bold">＊</span>
    </label>

    <div class="col-sm-10 mt-2">
        {{-- ラベル --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="content_type" id="ContentLabel" value="1"
                @if ($content->content_type == 1) checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="content_type" id="ContentLabel" value="1"
                onchange="showText()" checked>
            @endif
            <label class="form-check-label" for="ContentLabel">
                <span class="fw-bold">ラベル</span>（実際の学習項目とならない章名の表示などに使用します。）
            </label>
        </div>

        {{-- URL埋め込み --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="content_type" id="ContentURL" value="2"
                @if ($content->content_type == 2) checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="content_type" id="ContentURL" value="2" onchange="showText()">
            @endif

            <label class="form-check-label" for="ContentURL">
                <span class="fw-bold">URL</span>（YouTubeのURL末尾のIDを挿入してください。）
            </label>
        </div>

        {{-- 資料配布 --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="content_type" id="ContentFile" value="3"
                @if ($content->content_type == 3) checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="content_type" id="ContentFile" value="3" onchange="showText()">
            @endif

            <label class="form-check-label" for="ContentFile">
                <span class="fw-bold">配布資料</span>（配布したいファイルをアップロードします。）
            </label>
        </div>

        {{-- リッチテキスト --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="content_type" id="ContentHTML" value="4"
                @if ($content->content_type == 4) checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="content_type" id="ContentHTML" value="4" onchange="showText()">
            @endif
            <label class="form-check-label" for="ContentHTML">
                <span class="fw-bold">リッチテキスト</span>（HTML形式で学習項目を作成します。YouTubeなどの動画の埋め込みなどにも使用可能です。）
            </label>
        </div>

        {{-- 動画 --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="content_type" id="ContentMovie" value="5"
                @if ($content->content_type == 5) checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="content_type" id="ContentMovie" value="5" onchange="showText()">
            @endif

            <label class="form-check-label" for="ContentMovie">
                <span class="fw-bold">動画</span>（動画をアップロードします。HTML5のVIDEOタグで再生できるものに限られます。）
            </label>
        </div>

        {{-- テスト --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="content_type" id="ContentTest" value="6"
                @if ($content->content_type == 6) checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="content_type" id="ContentTest" value="6" onchange="showText()">
            @endif
            <label class="form-check-label" for="ContentTest">
                <span class="fw-bold">テスト</span>（テストを作成します。問題はテスト作成後、別画面にて追加します。）
            </label>
        </div>
    </div>
</div>
