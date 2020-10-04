<?php

if ($_FILES['file']['error'] == 0 and $_FILES['file']['type'] == 'application/pdf') {

    $see_option = json_decode($_POST['see_option'], false);
    $wca_options = json_decode($_POST['wca_options'], false);
    $data_tnoodle = json_decode($_POST['data_tnoodle'], false);

    RequestClass::CheckAccessExit(__FILE__, 'Competition.Settings', $see_option->competition_id);

    $timestamp_sql = date("Y-m-d H:i:s");
    $timestamp_pdf = filter_input(INPUT_POST, 'date');

    $pdf_file = $_FILES['file']['tmp_name'];
    $rand = random_string(20);
    mkdir("Scramble/HardTmp/{$rand}");
    $tmp_file = "Scramble/HardTmp/{$rand}/tmp.pdf";
    copy($pdf_file, $tmp_file);

    $im = new imagick();
    $im->readimage($tmp_file);
    $Pages = $im->getnumberimages();

    $lines = [];

    for ($i = 0; $i < $Pages; $i++) {
        $im = new imagick();
        $im->setResolution(300, 300);
        $im->readimage($pdf_file . "[$i]");
        $im->setImageFormat('jpeg');
        $jpg_file = "Scramble/HardTmp/{$rand}/{$i}.jpg";
        $im->writeImage($jpg_file);
        $im->clear();
        $im->destroy();

        $img_lines = imagecreatefromjpeg($jpg_file);

        $B = 0;
        for ($y = 250; $y < 3050; $y++) {
            if (in_array(imagecolorat($img_lines, 250, $y), [0, 65793])
                    and in_array(imagecolorat($img_lines, 250, $y + 1), [0, 65793])
                    and in_array(imagecolorat($img_lines, 310, $y), [0, 65793])
                    and in_array(imagecolorat($img_lines, 310, $y + 1), [0, 65793])
            ) {
                if (isset($lines[$i][$B][0]) and $y - $lines[$i][$B][0] < 115) {
                    $lines[$i][$B][0] = $y;
                } else {
                    $lines[$i][$B + 1][0] = $y;
                    if ($B > 0) {
                        $lines[$i][$B][1] = $y + 2;
                    }
                    $B++;
                }
                $y += 10;
            }
        }
        unset($lines[$i][$B]);
    }

    $X0 = 225;
    $X1 = 2413;
    $X = $X1 - $X0;

    @$pdf = new FPDF('P', 'mm');

    $pdf_img_Y0 = 35;
    $pdf_img_X0 = 20;
    $pdf_img_X = $pdf->w - $pdf_img_X0;
    $pdf_img_Y = $pdf_img_Y0;

    $Letter = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E", 6 => "F", 7 => "G", 8 => "H", 9 => "I");

    $pdf->SetFont('courier');

    $ScamblesOnePage = sizeof($lines[0]);
    $PageAdd = 0;
    for ($group = 0; $group < $see_option->groups; $group++) {

        $pdf->AddPage();

        $pdf->SetFont('Arial', '', 22);
        $pdf->Text(10, 13, $see_option->name . $see_option->view_round);
        $Competition_name = iconv('utf-8', 'cp1252//TRANSLIT', $see_option->competition_name);
        $pdf->SetFont('Arial', '', 16);
        $pdf->Text(10, 20, 'Group ' . $Letter[$group + 1]);
        $pdf->Text(10, 27, $Competition_name);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(10, 286, $timestamp_pdf . ' #' . $pdf->PageNo());

        $pdf_img_Y = $pdf_img_Y0;
        for ($attempt = 1; $attempt <= $see_option->attemption + $see_option->extra; $attempt++) {

            $StartLine = $lines[$group][$attempt][0];
            $EndLine = $lines[$group][$attempt][1];

            $image_cut = imagecreatetruecolor($X, $EndLine - $StartLine + 1);
            imagecolorallocate($image_cut, 0, 0, 0);

            imagecopy($image_cut, imagecreatefromjpeg("Scramble/HardTmp/{$rand}/{$group}.jpg"), 0, 0, $X0,
                    $StartLine, $X, $EndLine - $StartLine + 1);


            $file_tmp = "Scramble/HardTmp/{$rand}/{$group}_{$attempt}.png";
            imagepng($image_cut, $file_tmp);

            if ($see_option->code_script == 'all_scr') {
                $pdf->Text(10, $pdf_img_Y + 20, $Letter[$group + 1]);
            } else {
                if ($attempt > $see_option->attemption) {
                    $att = 'E' . ($attempt - $see_option->attemption);
                } else {
                    $att = $attempt;
                }

                if ($see_option->cut) {
                    if ($attempt > $see_option->attemption) {
                        if ($see_option->extra > 1) {
                            $attemp_str = ($attempt - $see_option->attemption) . ' extra';
                        } else {
                            $attemp_str = 'extra';
                        }
                    } else {
                        $attemp_str = $attempt . ' attempt';
                    }

                    $pdf->SetFont('times', '', 10);
                    $pdf->SetDash(1, 1);
                    $pdf->Line(8, $pdf_img_Y - 0.5, $pdf->w - 6, $pdf_img_Y - 0.5);
                    $pdf->SetDash(false, false);
                    $pdf->Image('Image/cut-solid.png', 6, $pdf_img_Y - 0.5 - 2, 4, 4);
                    $pdf->Text(6, $pdf_img_Y + 5, $Letter[$group + 1] . ' group');
                    $pdf->Text(6, $pdf_img_Y + 10, $attemp_str);
                } else {
                    if ($attempt > $see_option->attemption) {
                        if ($see_option->extra > 1) {
                            $attemp_str = 'E' . ($attempt - $see_option->attemption);
                        } else {
                            $attemp_str = 'E';
                        }
                    } else {
                        $attemp_str = $attempt;
                    }
                    $pdf->SetFont('times', '', 20);
                    $pdf->Text(10, $pdf_img_Y + 20, $attemp_str);
                }
            }

            $pdf->Image($file_tmp,
                    $pdf_img_X0,
                    $pdf_img_Y,
                    $pdf_img_X - $pdf_img_X0,
                    $pdf_img_X / $X * ($EndLine - $StartLine + 1));
            $pdf_img_Y += $pdf_img_X / $X * ($EndLine - $StartLine + 1) + 1;
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