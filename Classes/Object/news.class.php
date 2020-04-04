<?php

Class News {

    public $id = false;
    public $text = false;
    public $date = false;
    public $title = false;
    public $author = false;

    static function getNewsIDbyDepth($deepDays = false) {
        return News_data::getNewsIDbyDepth($deepDays);
    }

    static function getNewsByNewsId($newsId) {
        $news = [];
        foreach ($newsId as $aNewsId) {
            $new = new News();
            $new->getById($aNewsId);
            $news[] = $new;
        }
        usort($news, function($a, $b) {
            return strcmp($b->date, $a->date);
        });

        return $news;
    }

    function getById($aNewsId) {
        $aNews = News_data::getById($aNewsId);
        $text = ml_json($aNews->text);
        $text_new = $text;
        $text_line = explode("\n", $text);
        if (isset($text_line[0])) {
            $text_new = $text_line[0];
        }
        unset($text_line[0]);
        $author = new Delegate();
        $author->getByWid($aNews->delegateWid);

        $this->id = $aNews->id;
        $this->text = implode('<br>', $text_line);
        $this->date = date_range($aNews->date);
        $this->title = $text_new;
        $this->author = $author;
    }

}
