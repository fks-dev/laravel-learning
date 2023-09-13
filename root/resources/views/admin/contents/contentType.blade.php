@if ($content->content_type == 1)
    ラベル
@elseif($content->content_type == 2)
    YouTube
@elseif($content->content_type == 3)
    配布資料
@elseif($content->content_type == 4)
    リッチテキスト
@elseif($content->content_type == 5)
    動画
@elseif($content->content_type == 6)
    テスト
@endif