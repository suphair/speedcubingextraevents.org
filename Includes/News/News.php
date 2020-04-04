<?php

$news = News::getNewsByNewsId(
                News::getNewsIDbyDepth());

$data = arrayToObject([
    'news' => $news,
    'access' => CheckAccess('aNews')
        ]);

IncludeClass::Template('News', $data);
