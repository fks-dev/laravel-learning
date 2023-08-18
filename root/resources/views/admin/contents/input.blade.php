{{-- リッチテキスト --}}
<div class="row m-3" id="rich" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="rich">リッチテキスト</label>
    <div class="col-sm-10">
        @if (@isset($content->text))
            <div id="editor" style="height: 100px; overflow: auto;">{!! $content->text !!}</div>
            <input type="hidden" name="text" id="text">
        @else
            <div id="editor" style="height: 100px; overflow: auto;"></div>
            <input type="hidden" name="text" id="text">
        @endif
    </div>
</div>

{{-- 動画 --}}
<div class="row m-3" id="movie" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="movie">動画</label>
    <div class="col-sm-10">
        @if (@isset($content->movie))
            <input class="form-control" type="file" name="movie" id="movie" accept="video/*">
            <div class="mt-3">
                <p>こちらの動画がすでに登録されています。</p>
                <video controls>
                    <source src="{{ asset('storage/' . $content->movie ) }}" type="video/mp4">
                </video>
            </div>
        @else
            <input class="form-control" type="file" name="movie" id="movie" accept="video/*">
        @endif
    </div>
</div>

{{-- URL --}}
<div class="row m-3" id="url" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="url">URL</label>
    <div class="col-sm-10">
        @if (@isset($content->url))
            <input class="form-control" type="url" name="url" id="url" value="https://youtu.be/{{ $content->url }}">
        @else
            <input class="form-control" type="url" name="url" id="url">
        @endif
    </div>
</div>

{{-- ファイル --}}
<div class="row m-3" id="file" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="file">ファイル</label>
    <div class="col-sm-10">
        @if (@isset($content->file))
            <input class="form-control" type="file" name="file" id="file">
            <div class="mt-3">
                <p>こちらのファイルがすでに登録されています。</p>
                <p>{{ str_replace('handout/', '', $content->file) }}</p>
            </div>
        @else
            <input class="form-control" type="file" name="file" id="file">
        @endif
    </div>
</div>

{{-- テスト --}}
<div class="row m-3" id="test" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="test">制限時間（1−100分）</label>
    <div class="col-sm-10">
        @if (@isset($content->testTime))
            <input class="form-control" type="number" name="testTime" id="testTime" value="{{ $content->testTime }}">
        @else
            <input class="form-control" type="number" name="testTime" id="testTime">
        @endif
    </div>
    <label class="col-sm-2 col-form-label fw-bold" for="test">合格する得点率（1−100％）</label>
    <div class="col-sm-10">
        @if (@isset($content->testPer))
            <input class="form-control" type="number" name="testPer" id="testPer" value="{{ $content->testPer }}">
        @else
            <input class="form-control" type="number" name="testPer" id="testPer">
        @endif
    </div>
    <label class="col-sm-2 col-form-label fw-bold" for="test">出題数（1−100問）</label>
    <div class="col-sm-10">
        @if (@isset($content->testVol))
            <input class="form-control" type="number" name="testVol" id="testVol" value="{{ $content->testVol }}">
        @else
            <input class="form-control" type="number" name="testVol" id="testVol">
        @endif
    </div>
</div>

{{-- 備考 --}}
<div class="row m-3" id="remarks" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="remarks">備考</label>
    <div class="col-sm-10">
        @if (@isset($content))
            <textarea class="form-control" name="remarks" rows="5">{{ $content->remarks }}</textarea>
        @else
            <textarea class="form-control" name="remarks" rows="5"></textarea>
        @endif
    </div>
</div>
