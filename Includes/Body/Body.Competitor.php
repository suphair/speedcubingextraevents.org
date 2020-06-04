<?php

if ($data->delegate) {
    $data->delegate->candidates = (object) [];
    if (CheckAccess('Delegate.Candidate.Vote')) {
        $data->delegate->candidates->show = true;
        $data->delegate->candidates->link = LinkDelegate("Candidates");
        $count = DataBaseClass::getValue("Select count(*) from RequestCandidate RC "
                        . " left outer join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor and RCV.Delegate=" . $data->delegate->id
                        . " where RC.Status=0 and coalesce(RCV.Status,-2)=-2");
        if (!$count) {
            $data->delegate->candidates->new = '';
        } else {
            $data->delegate->candidates->new = $count;
        }
    }else{
        $data->delegate->candidates->show = false;
    }

    $delegateLinks = [
        ['Competition.Report', 'Reports', 'Reports', '<i class="far fa-file-alt"></i>'],
        ['Visitors', 'Visitors', 'Visitors', '<i class="fas fa-user-plus"></i>'],
        ['Texts', 'Texts', 'Texts', '<i class="fas fa-file-alt"></i>'],
        ['Delegates.Settings', 'Delegates/Settings', 'Delegate Changes', '<i class="fas fa-user-cog"></i>'],
        ['MultiLanguage', 'MultiLanguage', 'Multi language', '<i class="fas fa-language"></i>'],
        ['Access', 'Access', 'Access', '<i class="fas fa-id-badge"></i>'],
        ['Logs.Authorisations', 'Logs/Authorisations', 'Logs authorisations', '<i class="fas fa-list"></i>'],
        ['Logs.Registrations', 'Logs/Registrations', 'Logs registrations', '<i class="fas fa-list"></i>'],
        ['Logs.Scrambles', 'Logs/Scrambles', 'Logs scrambles', '<i class="fas fa-list"></i>'],
        ['Logs.Cron', 'Logs/Cron', 'Logs cron', '<i class="fas fa-list"></i>'],
        ['Logs.Mail', 'Logs/Mail', 'Logs mail', '<i class="fas fa-list"></i>']
    ];
    $links = [];
    foreach ($delegateLinks as $link) {
        if (CheckAccess($link[0])) {
            $links[] = [
                'link' => PageIndex() . $link[1],
                'value' => "{$link[3]} {$link[2]}"
            ];
        }
    }
    $data->delegate->links = arrayToObject($links);
}else{
    $data->delegate= arrayToObject(['links'=>[]]);
}

IncludeClass::Template('Body.Competitor', $data);
