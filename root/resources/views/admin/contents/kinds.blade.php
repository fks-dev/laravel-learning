<div class="row m-3">
    <label class="col-sm-2 col-form-label fw-bold" for="introduction">コンテンツ種別
        <span class="text-danger fw-bold">＊</span>
    </label>

    <div class="col-sm-10 mt-2">
        {{-- ラベル --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="kind" id="ContentLabel" value="ラベル"
                @if ($content->kind == 'ラベル') checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="kind" id="ContentLabel" value="ラベル"
                onchange="showText()" checked>
            @endif
            <label class="form-check-label" for="ContentLabel">
                <span class="fw-bold">ラベル</span>（実際の学習項目とならない章名の表示などに使用します。）
            </label>
        </div>

        {{-- リッチテキスト --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="kind" id="ContentHTML" value="リッチテキスト"
                @if ($content->kind == 'リッチテキスト') checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="kind" id="ContentHTML" value="リッチテキスト" onchange="showText()">
            @endif
            <label class="form-check-label" for="ContentHTML">
                <span class="fw-bold">リッチテキスト</span>（HTML形式で学習項目を作成します。YouTubeなどの動画の埋め込みなどにも使用可能です。）
            </label>
        </div>

        {{-- 動画 --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="kind" id="ContentMovie" value="動画"
                @if ($content->kind == '動画') checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="kind" id="ContentMovie" value="動画" onchange="showText()">
            @endif

            <label class="form-check-label" for="ContentMovie">
                <span class="fw-bold">動画</span>（動画をアップロードします。HTML5のVIDEOタグで再生できるものに限られます。）
            </label>
        </div>

        {{-- URL埋め込み --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="kind" id="ContentURL" value="URL"
                @if ($content->kind == 'URL') checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="kind" id="ContentURL" value="URL" onchange="showText()">
            @endif

            <label class="form-check-label" for="ContentURL">
                <span class="fw-bold">URL</span>（外部のWebページを学習項目として追加します。）
            </label>
        </div>

        {{-- 資料配布 --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="kind" id="ContentFile" value="資料"
                @if ($content->kind == '資料') checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="kind" id="ContentFile" value="資料" onchange="showText()">
            @endif

            <label class="form-check-label" for="ContentFile">
                <span class="fw-bold">配布資料</span>（配布したいファイルをアップロードします。）
            </label>
        </div>

        {{-- テスト --}}
        <div class="form-check">
            @if (isset($content))
                <input class="form-check-input" type="radio" name="kind" id="ContentTest" value="テスト"
                @if ($content->kind == 'テスト') checked @endif onchange="showText()">
            @else
                <input class="form-check-input" type="radio" name="kind" id="ContentTest" value="テスト" onchange="showText()">
            @endif
            <label class="form-check-label" for="ContentTest">
                <span class="fw-bold">テスト</span>（テストを作成します。問題はテスト作成後、別画面にて追加します。）
            </label>
        </div>
    </div>
</div>
