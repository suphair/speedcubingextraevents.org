<?php

Function DataBaseInit() {

    $connection = mysqli_init();
    @$success = mysqli_real_connect(
            $connection
            , Suphair \ Config :: get('DB', 'host')
            , Suphair \ Config :: get('DB', 'username')
            , Suphair \ Config :: get('DB', 'password')
            , Suphair \ Config :: get('DB', 'schema')
            , Suphair \ Config :: get('DB', 'port')
    );

    if (!$success) {
        echo '<h1>Error establishing a database connection</h1>';
        exit();
    }

    mysqli_query($connection, "SET CHARSET utf8mb4");
    DataBaseClass::setConectionSee($connection);



    $connection2 = mysqli_init();
    @$success = mysqli_real_connect(
            $connection2
            , Suphair \ Config :: get('DB', 'host')
            , Suphair \ Config :: get('DB', 'username')
            , Suphair \ Config :: get('DB', 'password')
            , Suphair \ Config :: get('DB', 'schema_WCA')
            , Suphair \ Config :: get('DB', 'port')
    );

    if (!$success) {
        echo '<h1>Error establishing a database2 connection</h1>';
        exit();
    }

    mysqli_query($connection2, "SET CHARSET utf8mb4");
    DataBaseClassWCA::setConection($connection2);
    DataBaseClass::setConectionWca($connection2);


    $connection3 = mysqli_init();
    $success = mysqli_real_connect(
            $connection3
            , Suphair \ Config :: get('DB', 'host')
            , Suphair \ Config :: get('DB', 'username')
            , Suphair \ Config :: get('DB', 'password')
            , Suphair \ Config :: get('DB', 'schema_Export')
            , Suphair \ Config :: get('DB', 'port')
    );

    if (!$success) {
        echo '<h1>Error establishing a database3 connection</h1>';
        exit();
    }

    mysqli_query($connection3, "SET CHARSET utf8mb4");
    DataBaseClassExport::setConection($connection3);



    DataBaseClass::AddTable('Competition', 'C', array('ID', 'Name', 'WCA', 'City', 'StartDate', 'EndDate', 'WebSite', 'Registration', 'Country', 'Status', 'MaxCardID', 'CheckDateTime', 'LoadDateTime', 'Comment', 'Onsite', 'Unofficial', 'DelegateWCA', 'DelegateWCAOn', 'Cubingchina'));
    DataBaseClass::SetOrder('Competition', ' StartDate desc');

    DataBaseClass::AddTable('Discipline', 'D', array('ID', 'Name', 'Code', 'Status', 'Competitors', 'TNoodle', 'TNoodles', 'TNoodlesMult', 'CutScrambles', 'GlueScrambles', 'Simple', 'Inspection', 'CodeScript', 'Comment', 'ScrambleComment', 'Codes'));
    DataBaseClass::SetOrder('Discipline', 'Name');

    DataBaseClass::AddTable('Delegate', 'Dl', array('ID', 'Name', 'Site', 'Contact', 'Status', 'WCA_ID', 'WID', 'Secret'));
    DataBaseClass::SetOrder('Delegate', ' Name');

    DataBaseClass::AddTable('CompetitionDelegate', 'CD', array('ID', 'Competition', 'Delegate'));
    DataBaseClass::SetJoin('CompetitionDelegate', 'Delegate');
    DataBaseClass::SetJoin('CompetitionDelegate', 'Competition');

    DataBaseClass::AddTable('CompetitionReport', 'CR', array('ID', 'Competition', 'Delegate', 'Report', 'Parsedown'));
    DataBaseClass::SetJoin('CompetitionReport', 'Delegate');
    DataBaseClass::SetJoin('CompetitionReport', 'Competition');


    DataBaseClass::AddTable('Competitor', 'Cm', array('ID', 'Name', 'WCAID', 'Country', 'WID', 'Language', 'Email'));
    DataBaseClass::SetOrder('Competitor', ' Name');

    DataBaseClass::AddTable('Format', 'F', array('ID', 'Result', 'Attemption', 'Name', 'ExtResult'));

    DataBaseClass::AddTable('DisciplineFormat', 'DF', array('ID', 'Discipline', 'Format'));
    DataBaseClass::SetJoin('DisciplineFormat', 'Format');
    DataBaseClass::SetJoin('DisciplineFormat', 'Discipline');


    DataBaseClass::AddTable('Event', 'E', array('ID', 'CutoffMinute', 'CutoffSecond', 'LimitMinute', 'LimitSecond', 'Secret', 'Competitors', 'Competition', 'Groups', 'LocalID', 'Round', 'vRound', 'DisciplineFormat', 'Cumulative', 'Comment', 'ScrambleSalt', 'ScramblePublic', 'CommandsCup'));
    DataBaseClass::SetJoin('Event', 'Competition');
    DataBaseClass::SetJoin('Event', 'DisciplineFormat');

    DataBaseClass::AddTable('Command', 'Com', array('ID', 'Place', 'CardID', 'Decline', 'Event', 'Group', 'Event', 'Secret', 'vCompetitors', 'vCountry', 'Warnings', 'Onsite', 'DateCreated', 'Video', 'Name', 'Sum333', 'inCup'));
    DataBaseClass::SetJoin('Command', 'Event');

    DataBaseClass::AddTable('CommandCompetitor', 'CC', array('ID', 'Command', 'Competitor', 'CheckStatus'));
    DataBaseClass::SetJoin('CommandCompetitor', 'Command');
    DataBaseClass::SetJoin('CommandCompetitor', 'Competitor');

    DataBaseClass::AddTable('Attempt', 'A', array('ID', 'Attempt', 'IsDNF', 'IsDNS', 'Minute', 'Second', 'Milisecond', 'Except', 'Special', 'vOut', 'vOrder', 'Amount'));
    DataBaseClass::SetJoin('Attempt', 'Command');
    DataBaseClass::SetOrder('Attempt', ' vOrder');

    DataBaseClass::AddTable('Scramble', 'S', array('ID', 'Event', 'Scramble', 'Group', 'Timestamp'));
    DataBaseClass::SetJoin('Scramble', 'Event');

    DataBaseClass::AddTable('RequestCandidate', 'RC', array('ID', 'Competitor', 'Datetime', 'Status'));
    DataBaseClass::SetJoin('RequestCandidate', 'Competitor');

    DataBaseClass::AddTable('RequestCandidateField', 'RCF', array('ID', 'RequestCandidate', 'Field', 'Value'));
    DataBaseClass::SetJoin('RequestCandidateField', 'RequestCandidate');


    DataBaseClass::AddTable('RequestCandidateTemplate', 'RCT', array('ID', 'Name', 'Type', 'Language'));
    DataBaseClass::SetOrder('RequestCandidateTemplate', ' ID');

    DataBaseClass::AddTable('Registration', 'Reg', array('ID', 'Competition', 'Competitor'));
    DataBaseClass::SetJoin('Registration', 'Competitor');

    DataBaseClass::AddTable('Regulation', 'R', array('ID', 'Event', 'Language', 'Text'));


    DataBaseClass::AddTable('FormatResult', 'FR', array('ID', 'Name', 'Format'));
    DataBaseClass::SetJoin('Discipline', 'FormatResult');

    DataBaseClass::AddTable('BlockText', 'BT', array('ID', 'Name', 'Value', 'Country'));


    DataBaseClass::AddTable('News', 'N', array('ID', 'Date', 'Text', 'Delegate'));
}
