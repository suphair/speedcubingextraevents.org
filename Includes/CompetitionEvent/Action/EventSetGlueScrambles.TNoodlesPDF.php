<?php

if ($_FILES['file']['error'] == 0 and $_FILES['file']['type'] == 'application/pdf') {

    $see_option = json_decode($_POST['see_option'], false);
    $wca_options = json_decode($_POST['wca_options'], false);
    $data_tnoodle = json_decode($_POST['data_tnoodle'], false);

    RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $see_option->competition_id);

    $timestamp_sql = date("Y-m-d H:i:s");
    $timestamp_pdf = date("Y-m-d H:i:s (P)");

    $pdf_file = $_FILES['file']['tmp_name'];
    $rand = random_string(20);
    mkdir("Scramble/HardTmp/{$rand}");
    $tmp_file = "Scramble/HardTmp/{$rand}/tmp.pdf";
    copy($pdf_file, $tmp_file);

    $im = new imagick();
    $im->readimage($tmp_file);
    $pages = $im->getnumberimages();

    $lines = [];

    for ($page = 0; $page < $pages; $page++) {
        $im = new imagick();
        $im->setResolution(300, 300);
        $im->readimage($pdf_file . "[$page]");
        $im->setImageFormat('jpeg');
        $jpg_file = "Scramble/HardTmp/{$rand}/{$page}.jpg";
        $im->writeImage($jpg_file);
        $im->clear();
        $im->destroy();

        $img_lines = imagecreatefromjpeg($jpg_file);

        $page_attempt = 0;
        for ($y = 250; $y < 3050; $y++) {
            if (in_array(imagecolorat($img_lines, 250, $y), [0, 65793])
                    and in_array(imagecolorat($img_lines, 250, $y + 1), [0, 65793])
                    and in_array(imagecolorat($img_lines, 310, $y), [0, 65793])
                    and in_array(imagecolorat($img_lines, 310, $y + 1), [0, 65793])
            ) {
                if (isset($lines[$page][$page_attempt - 1][0]) and $y - $lines[$page][$page_attempt - 1][0] < 115) {
                    $lines[$page][$page_attempt - 1][0] = $y;
                } else {
                    $lines[$page][$page_attempt][0] = $y;
                    if ($page_attempt > 0) {
                        $lines[$page][$page_attempt - 1][1] = $y + 2;
                    }
                    $page_attempt++;
                }
                $y += 10;
            }
        }
        unset($lines[$page][$page_attempt]);
    }

    $page_events = [];
    $page = 0;
    foreach ($wca_options as $event) {
        $event_attempt = 0;
        for ($group = 0; $group < $event->groups; $group++) {
            for ($page_attempt = 0; $page_attempt < $event->attemption + $event->extra; $page_attempt++) {
                $page_events[$event->code][$event_attempt] = ['page' => $page, 'attempt' => $page_attempt, 'lines' => $lines[$page][$page_attempt]];
                $event_attempt++;
            }
            $page++;
        }
    }


    $X0 = 225;
    $X1 = 2413;
    $X = $X1 - $X0;

    @$pdf = new FPDF('P', 'mm');

    $pdf_img_Y0 = 35;
    $pdf_img_X0 = 20;
    $pdf_img_X = $pdf->w - $pdf_img_X0;
    $pdf_img_Y = $pdf_img_Y0;

    $Letter = array(0 => "A", 1 => "B", 2 => "C", 3 => "D", 4 => "E", 5 => "F", 6 => "G", 7 => "H", 8 => "I");

    $pdf->SetFont('courier');

    $ScamblesOnePage = (5 + 2);
    $ScramblesEvent = $see_option->groups * ($see_option->attemption + 1);
    $PagesEvent = ceil($ScramblesEvent / $ScamblesOnePage);


    $events = [];
    foreach (explode(',', $see_option->tnoodles) as $tnoodle) {
        for ($i = 1; $i <= $see_option->tnoodles_mult; $i++) {
            $events[] = $tnoodle;
        }
    }

    $mguild_events = [
        ['sq1' => 'Square-1', 'skewb' => 'Skewb', 'clock' => 'Clock'],
        ['minx' => 'Megaminx', 'pyram' => 'Pyraminx', '333oh' => '3x3x3 One-Handed'],
        ['555' => '5x5x5 Cube', '444' => '4x4x4 Cube', '333' => '3x3x3 Cube', '222' => '2x2x2 Cube'],
    ];

    $event_attemptions = [];
    for ($group = 0; $group < $see_option->groups; $group++) {
        for ($attemption = 0; $attemption < $see_option->attemption + $see_option->extra; $attemption++) {
            $page = 0;
            foreach ($events as $e => $event) {
                $event_attemptions[$event] ??= 0;
                #echo $group." ".$attemption." ".$event." :".$event_attemptions[$event];
                #print_r ($page_events[$event][$event_attemptions[$event]]);
                #echo "<br>";

                $line_page = $page_events[$event][$event_attemptions[$event]]['page'];
                $line_start = $page_events[$event][$event_attemptions[$event]]['lines'][0];
                $line_end = $page_events[$event][$event_attemptions[$event]]['lines'][1];

                $next = ($pdf_img_Y + $pdf_img_X / $X * ($line_end - $line_start + 1)) > ($pdf->h - 10);

                if (!$page or $next) {
                    $page++;
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 22);
                    $pdf->Text(10, 13, $see_option->name . $see_option->view_round);

                    $pdf->SetFont('Arial', '', 16);
                    if ($attemption < $see_option->attemption) {
                        $pdf->Text(10, 20, 'Group ' . $Letter[$group] . ' / Attempt ' . ($attemption + 1) . ' / Page ' . $page);
                    } else {
                        $pdf->Text(10, 20, 'Group ' . $Letter[$group] . ' / Extra attempt / Page ' . $page);
                    }

                    $pdf->Text(10, 27, iconv('utf-8', 'cp1252//TRANSLIT', $see_option->competition_name));

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->Text(10, 286, $timestamp_pdf . ' #' . $pdf->PageNo());

                    $pdf_img_Y = $pdf_img_Y0;
                }


                $image_cut = imagecreatetruecolor($X, $line_end - $line_start + 1);
                imagecolorallocate($image_cut, 0, 0, 0);
                imagecopy($image_cut, imagecreatefromjpeg("Scramble/HardTmp/{$rand}/{$line_page}.jpg"), 0, 0, $X0, $line_start, $X, $line_end - $line_start + 1);
                $file_tmp = "Scramble/HardTmp/{$rand}/{$group}_{$attemption}_{$event}.png";
                imagepng($image_cut, $file_tmp);

                if ($see_option->cut) {

                    if ($attemption >= $see_option->attemption) {
                        $attemp_str = 'extra';
                    } else {
                        $attemp_str = 'attempt';
                    }

                    $pdf->SetFont('times', '', 10);
                    $pdf->SetDash(1, 1);
                    $pdf->Line(8, $pdf_img_Y - 0.5, $pdf->w - 6, $pdf_img_Y - 0.5);
                    $pdf->SetDash(false, false);
                    $pdf->Image('Image/cut-solid.png', 6, $pdf_img_Y - 0.5 - 2, 4, 4);
                    $pdf->Text(6, $pdf_img_Y + 5, $Letter[$group] . ' group');
                    $pdf->Text(6, $pdf_img_Y + 10, $attemp_str);

                } else {
                    if ($see_option->tnoodles_mult > 1) {
                        $pdf->SetFont('times', '', 16);
                        $pdf->Text(10, $pdf_img_Y + 10, $e + 1);
                    }
                    if ($see_option->mguild) {
                        foreach ($mguild_events as $line => $row) {
                            if ($row[$event] ?? FALSE) {
                                $pdf->SetFont('times', '', 12);
                                $pdf->Text(10, $pdf_img_Y + 10, ($line + 1) . ' row');
                            }
                        }
                    }
                }
                if (isset($events[$e])) {
                    $pdf->Image("Image/Events/" . $events[$e] . ".png", 10, $pdf_img_Y + 12, 10, 10);
                }

                $pdf->Image($file_tmp, $pdf_img_X0, $pdf_img_Y, $pdf_img_X - $pdf_img_X0, $pdf_img_X / $X * ($line_end - $line_start + 1));
                $pdf_img_Y += $pdf_img_X / $X * ($line_end - $line_start + 1) + 1;

                $event_attemptions[$event]++;
            }
            if ($see_option->mguild) {
                $mguild_X0 = 10;
                $mguild_Y0 = $pdf_img_Y + 10;

                $mguild_X1 = $pdf->w - 10;
                $mguild_Y1 = $pdf->h - 30;

                $pdf->SetFont('times', '', 18);
                $pdf->Text(85, $mguild_Y1 + 8, 'Competitor');
                $pdf->Rect($mguild_X0, $mguild_Y0, $mguild_X1 - $mguild_X0, $mguild_Y1 - $mguild_Y0 + 12);

                $mguild_line = [];
                $mguild_ceil = [];
                for ($mguild_i = 0; $mguild_i < sizeof($mguild_events); $mguild_i++) {
                    $mguild_line[$mguild_i] = $mguild_Y0 + ($mguild_Y1 - $mguild_Y0) / sizeof($mguild_events) * $mguild_i;
                    $mguild_j = 0;
                    foreach ($mguild_events[$mguild_i] as $code => $name) {
                        $mguild_ceil[$mguild_i][$code] = $mguild_X0 + ($mguild_X1 - $mguild_X0) / sizeof($mguild_events[$mguild_i]) * $mguild_j;
                        $mguild_j++;
                    }
                }
                $mguild_line[sizeof($mguild_events)] = $mguild_Y1;

                for ($mguild_i = 0; $mguild_i < sizeof($mguild_events); $mguild_i++) {
                    $pdf->Line($mguild_X0, $mguild_line[$mguild_i], $mguild_X1, $mguild_line[$mguild_i]);
                    $pdf->SetFont('times', '', 12);
                    $pdf->Text($mguild_X0 + 1, $mguild_line[$mguild_i] + 4, ($mguild_i + 1) . ' row');
                    foreach ($mguild_events[$mguild_i] as $code => $name) {
                        $pdf->Line($mguild_ceil[$mguild_i][$code], $mguild_line[$mguild_i], $mguild_ceil[$mguild_i][$code], $mguild_line[$mguild_i + 1]);
                        $pdf->Image("Image/Events/$code.png", $mguild_ceil[$mguild_i][$code] + 5, $mguild_line[$mguild_i] + 5, 10, 10);
                        $pdf->SetFont('times', '', 18);
                        $pdf->Text($mguild_ceil[$mguild_i][$code] + 5, $mguild_line[$mguild_i] + 30, $name);
                    }
                }
                $pdf->Line($mguild_X0, $mguild_line[sizeof($mguild_events)], $mguild_X1, $mguild_line[sizeof($mguild_events)]);
            }
        }
    }

    DataBaseClass::Query("Update Event set ScrambleSalt='$rand' where ID=" . $see_option->id);
    $file = "Image/Scramble/" . $rand . ".pdf";
    $pdf->Output($file, 'F');
    $pdf->Close();
    DeleteFolder("Scramble/HardTmp/$rand");
    DataBaseClass::Query("Insert into ScramblePdf (Event,Secret,Delegate,Timestamp,Action) values ('" . $see_option->id . "','$rand','" . getDelegate()['Delegate_ID'] . "','$timestamp_sql','Generation')");
    header('Location: ' . PageIndex() . "/Scramble/" . $see_option->id);
    exit();
}