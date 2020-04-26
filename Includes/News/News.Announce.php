<?php

$news = News::getNewsByNewsId(
                News::getNewsIDbyDepth(14));

$data = arrayToObject([
    'news' => $news,
    'newsExists' => sizeof($news) > 0
        ]);
IncludeClass::Template('News.Announce', $data);
