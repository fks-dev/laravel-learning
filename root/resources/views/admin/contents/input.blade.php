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
        @if (@isset($content->movie_file_path))
            <input class="form-control" type="file" name="movie_file_path" id="movie" accept="video/*">
            <div class="mt-3">
                <p>こちらの動画がすでに登録されています。</p>
                <video controls>
                    <source src="{{ asset('storage/' . $content->movie_file_path ) }}" type="video/mp4">
                </video>
            </div>
        @else
            <input class="form-control" type="file" name="movie_file_path" id="movie" accept="video/*">
        @endif
    </div>
</div>

{{-- URL --}}
<div class="row m-3" id="url" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="url">URL</label>
        <div class="col-sm-10">
            <div class="input-group">
                <span class="input-group-text">https://www.youtube.com/watch?v=</span>
                @if (@isset($content->youtube_video_id))
                    <input class="form-control" type="text" id="url" name="youtube_video_id" value="{{ $content->youtube_video_id }}">
                @else
                    <input class="form-control" type="text" id="url" name="youtube_video_id">
                @endif
            </div>
    </div>
</div>

{{-- ファイル --}}
<div class="row m-3" id="file" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="file">ファイル</label>
    <div class="col-sm-10">
        @if (@isset($content->document_file_path))
            <input class="form-control" type="file" name="document_file_path" id="file">
            <div class="mt-3">
                <p>こちらのファイルがすでに登録されています。</p>
                <p>{{ str_replace('handout/', '', $content->document_file_path) }}</p>
            </div>
        @else
            <input class="form-control" type="file" name="document_file_path" id="file">
        @endif
    </div>
</div>

{{-- テスト --}}
<div class="row m-3" id="test" style="display: none;">
    <label class="col-sm-2 col-form-label fw-bold" for="time">制限時間（1−100分）</label>
    <div class="col-sm-10">
        @if (@isset($content->time_limit_minutes))
            <input class="form-control" type="number" name="time_limit_minutes" id="time" min="1" max="100" value="{{ $content->time_limit_minutes }}">
        @else
            <input class="form-control" type="number" name="time_limit_minutes" id="time" min="1" max="100">
        @endif
    </div>
    <label class="col-sm-2 col-form-label fw-bold" for="rate">合格する得点率（1−100％）</label>
    <div class="col-sm-10">
        @if (@isset($content->passing_score_rate))
            <input class="form-control" type="number" name="passing_score_rate" id="rate" min="1" max="100" value="{{ $content->passing_score_rate }}">
        @else
            <input class="form-control" type="number" name="passing_score_rate" id="rate" min="1" max="100">
        @endif
    </div>
    <label class="col-sm-2 col-form-label fw-bold" for="amount">出題数（1−100問）</label>
    <div class="col-sm-10">
        @if (@isset($content->amount_questions))
            <input class="form-control" type="number" name="amount_questions" id="amount" min="1" max="100" value="{{ $content->amount_questions }}">
        @else
            <input class="form-control" type="number" name="amount_questions" id="amount" min="1" max="100">
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
