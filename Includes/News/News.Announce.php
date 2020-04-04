<?php

$news = News::getNewsByNewsId(
                News::getNewsIDbyDepth(14));

$data = arrayToObject([
    'news' => $news,
        ]);
IncludeClass::Template('News.Announce', $data);
