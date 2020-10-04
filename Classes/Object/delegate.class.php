<?php

Class Delegate {

    public $id = false;
    public $wid = false;
    public $link = false;
    public $wcaid = false;
    public $status = false;
    public $secret = false;
    public $contact = false;
    public $competitor;
    public $competitions = [];

    function __construct() {
        $this->competitor = new Competitor();
    }

    function getById($id) {
        $delegate = Delegate_data::getById($id);
        if ($delegate and $delegate != new stdClass()) {
            $this->SetbyRow($delegate);
        }
    }

    function getByWcaid($wca) {
        $delegate = Delegate_data::getByWcaid($wca);
        if ($delegate and $delegate != new stdClass()) {
            $this->SetbyRow($delegate);
        }
    }

    function getByWid($wid) {
        $delegate = Delegate_data::getByWid($wid);
        if ($delegate and $delegate != new stdClass()) {
            $this->SetbyRow($delegate);
        }
    }

    function SetbyRow($delegate) {
        $this->id = $delegate->id;
        $this->wid = $delegate->wid;
        $this->link = PageIndex() . "/Delegate/$delegate->wcaid";
        $this->wcaid = $delegate->wcaid;
        $this->status = $delegate->status;
        $this->secret = $delegate->secret;
        $this->contact = $delegate->contact;
        $this->competitor->getByWcaid($delegate->wcaid);
    }

    function getCompetitionsIdbyDelegate($filterValues = []) {
        if ($this->id) {
            return Competition::getCompetitionsIdbyDelegate($this->id, $filterValues);
        } else {
            return [];
        }
    }

    static function getDelegates($withArchive = false) {
        $delegates = [];
        foreach (Delegate_data::getDelegatesId($withArchive) as $delegateID) {
            $delegate = new Delegate();
            $delegate->getById($delegateID);
            $delegates[] = $delegate;
        }

        usort($delegates, function($a, $b) {
            return strcmp(
                    $a->competitor->name, $b->competitor->name
            );
        });

        return $delegates;
    }

}
