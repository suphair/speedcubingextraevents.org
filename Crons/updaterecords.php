<?php

AddLog('UpdateRecords', 'Cron', 'Start');
Attempt::updateRecords();
AddLog('UpdateRecords', 'Cron', 'End');

exit();

