<?php

Class Delegate {

    public $id = false;
    public $wid = false;
    public $link = false;
    public $wcaid = false;
    public $status = false;
    public $secret = false;
    public $contact = false;
    public $competitor = false;
    public $competitions = [];

    public function getById($id) {
        $delegate = Delegate_data::getById($id);
        if ($delegate) {
            $this->SetbyRow($delegate);
        }
    }

    function getByWcaid($wca) {
        $delegate = Delegate_data::getByWcaid($wca);
        if ($delegate) {
            $this->SetbyRow($delegate);
        }
    }

    function getByWid($wid) {
        $delegate = Delegate_data::getByWid($wid);
        if ($delegate) {
            $this->SetbyRow($delegate);
        }
    }

    public function SetbyRow($delegate) {
        $this->id = $delegate->id;
        $this->wid = $delegate->wid;
        $this->link = PageIndex() . "Delegate/$this->wcaid";
        $this->wcaid = $delegate->wcaid;
        $this->status = $delegate->status;
        $this->secret = $delegate->secret;
        $this->contact = $delegate->contact;
        $this->competitor = new Competitor();
        $this->competitor->getByWcaid($this->wcaid);
    }

    public function getCompetitionsIdbyDelegate($filterValues = []) {
        if ($this->id) {
            return Competition::getCompetitionsIdbyDelegate($this->id, $filterValues);
        } else {
            return [];
        }
    }

}