<?php

class ScramblePrint {

    private $competition_event;

    const LETTER = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

    public static function getPDF(
    $competition_name
    , $scrambles
    , $event_codeScript
    , $event_scrambleComment
    , $event_name
    , $event_isCup
    , $event_isCut
    , $view_round
    , $attemptions
    ) {

        $rand = random_string(20);
        $dir = "Scramble/ScramblePrintTmp/$rand";
        mkdir($dir);

        $timestamp_pdf = date("Y-m-d H:i:s (P)");

        $Y_Content_S = 33;
        $Y_Content_E = 275;
        $scramble_font = 'courier';


        $y_att = 20;
        $dy = 33;
        $dyy = 3;


        $X_IMG_0 = 140;

        $X_IMG_1 = 205;

        $scramble_max = 0;
        foreach ($scrambles as $group => $group_scrambles) {
            foreach ($group_scrambles as $attempt => $scramble) {
                $scramble_max = max($scramble_max, strlen($scramble));
            }
        }

        $pdf = new FPDF('P', 'mm');
        $pdf->SetFont('courier');
        $pdf->SetLineWidth(0.3);

        $Competition_name = iconv('utf-8', 'cp1252//TRANSLIT', $competition_name);

        if (!file_exists("Scramble/{$event_codeScript}.php")) {
            user_error("Wrong {$event_codeScript}");
        }
        include "Scramble/{$event_codeScript}.php";

        foreach ($scrambles as $group => $group_scrambles) {
            $pdf->AddPage();
            //Instructions
            $pdf->SetFont('Arial', '', 10);
            $Instructions = $event_scrambleComment;
            $Instruction_rows = explode("\n", $Instructions);
            $y_instruction = 10;
            foreach ($Instruction_rows as $instruction_row) {
                $pdf->Text(100, $y_instruction, $instruction_row);
                $y_instruction += 5;
            }

            //Header
            $pdf->SetFont('Arial', '', 22);
            $pdf->Text(10, 13, $event_name . $view_round);
            $pdf->SetFont('Arial', '', 16);
            if ($event_isCup) {
                if ($group + 1 == sizeof($scrambles)) {
                    $pdf->Text(10, 20, 'Duel Extra');
                } else {
                    $pdf->Text(10, 20, 'Duel # ' . ($group + 1));
                }
            } else {
                $pdf->Text(10, 20, 'Group ' . self::LETTER[$group]);
            }
            $pdf->Text(10, 27, $competition_name);

            //Footer
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Text(10, 286, $timestamp_pdf . ' #' . $pdf->PageNo());
            $Y = $Y_Content_S;

            foreach ($group_scrambles as $attempt => $scramble) {
                $page = 1;
                $im = ScrambleImage($scramble);
                $filename = "$dir/{$group}_{$attempt}.png";
                imagePNG($im, $filename);

                $pdf->Line(10, $Y, $X_IMG_1, $Y);

                $y0 = 43;

                if ($attempt == $attemptions + 1 and ! $event_isCup) {
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->SetFillColor(230, 230, 230);
                    $pdf->Rect(17, $Y, $X_IMG_1 - 17, 6, 'DF');
                    $pdf->Text(90, $Y + 4, 'Extra scrambles');
                    $Y += 6;
                }


                if ($attempt > $attemptions) {
                    $y0 = 60;
                }

                $pdf->SetFont('times', 'B', 24);

                $texts = array();
                if (strpos($scramble, "&") === false) {

                    $scramble_len = strlen($scramble);
                    if ($scramble_max > 53 * 10) {
                        $scramble_row = 11;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 9) {
                        $scramble_row = 10;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 8) {
                        $scramble_row = 9;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 7) {
                        $scramble_row = 8;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 6) {
                        $scramble_row = 7;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 5) {
                        $scramble_row = 6;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 4) {
                        $scramble_row = 5;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 53 * 3) {
                        $scramble_row = 4;
                        $scramble_size = 10;
                    } elseif ($scramble_max > 38 * 3) {
                        $scramble_row = 3;
                        $scramble_size = 12;
                    } elseif ($scramble_max > 102) {
                        $scramble_row = 3;
                        $scramble_size = 16;
                    } elseif ($scramble_max > 90) {
                        $scramble_row = 3;
                        $scramble_size = 16;
                    } elseif ($scramble_max > 68) {
                        $scramble_row = 3;
                        $scramble_size = 16;
                    } elseif ($scramble_max > 60) {
                        $scramble_row = 2;
                        $scramble_size = 18;
                    } elseif ($scramble_max > 34) {
                        $scramble_row = 2;
                        $scramble_size = 18;
                    } elseif ($scramble_max > 20) {
                        $scramble_row = 1;
                        $scramble_size = 18;
                    } else {
                        $scramble_row = 1;
                        $scramble_size = 20;
                    }
                    if ($scramble_len < 10)
                        $scramble_row = 1;

                    if ($event_isCup) {
                        $scramble_size = 11;
                        $scramble_row = 1;
                    }

                    $r = [];
                    for ($i = 1; $i < $scramble_row; $i++) {
                        $r[$i] = ceil($scramble_len / $scramble_row * $i);
                        while (substr($scramble, $r[$i], 1) != " ") {
                            $r[$i] --;
                        }
                    }
                    $r[$scramble_row] = $scramble_len;

                    $texts[] = trim(substr($scramble, 0, $r[1]));

                    for ($i = 1; $i <= $scramble_row - 2; $i++) {
                        $texts[] = trim(substr($scramble, $r[$i] + 1, $r[$i + 1] - $r[$i]));
                    }
                    if ($scramble_row > 1) {
                        $texts[] = trim(substr($scramble, $r[$scramble_row - 1]));
                    }
                } else {
                    $texts = explode(" & ", $scramble);
                    $scramble_max = 0;
                    foreach ($texts as $text) {
                        $scramble_max = max(array($scramble_max, strlen($text)));
                    }
                    if ($scramble_max >= 50) {
                        $scramble_size = 10;
                    } elseif ($scramble_max > 42) {
                        $scramble_size = 12;
                    } elseif ($scramble_max > 38) {
                        $scramble_size = 12;
                    } elseif ($scramble_max > 33) {
                        $scramble_size = 16;
                    } elseif ($scramble_max > 30) {
                        $scramble_size = 18;
                    } else {
                        $scramble_size = 20;
                    }
                }

                $scramble_row = sizeof($texts);
                $pdf->SetFont($scramble_font, '', $scramble_size);

                $D_Att = ($scramble_row) * $scramble_size * 0.3 + 20;
                if ($D_Att < 33)
                    $D_Att = 33;
                if ($event_isCup)
                    $D_Att = 16;

                $t = 0;
                if (sizeof($texts) == 1) {
                    $t = -10;
                }
                if (sizeof($texts) == 2) {
                    $t = -2;
                }
                if (sizeof($texts) > 3) {
                    $t = 1;
                }
                foreach ($texts as $r => $text) {
                    if ($r % 2 != 0) {
                        $pdf->SetFillColor(230, 230, 230);
                        $pdf->Rect(17, $Y + $D_Att / $scramble_row * ($r + 1) - $scramble_size / 2 - 2 + $t, $X_IMG_0 - 10, $scramble_size / 2, 'F');
                    }
                    if (!$event_isCup) {
                        $pdf->Text(20, $Y + $D_Att / $scramble_row * ($r + 1) - $scramble_size * .3 + $t, $text);
                    } else {
                        $pdf->Text(20, $Y + $D_Att / 2, $text);
                    }
                }

                $pdf->SetFont('times', 'B', 16);
                if ($event_isCut) {
                    if ($attempt > $attemptions) {
                        $pdf->Text(6, $Y + $D_Att / 2, self::LETTER[$group] . "E" . ($attempt - $attemptions));
                    } else {
                        $pdf->Text(8, $Y + $D_Att / 2, self::LETTER[$group] . "" . ($attempt ));
                    }
                } else {
                    if ($attempt > $attemptions and ! $event_isCup) {
                        $pdf->Text(10, $Y + $D_Att / 2, "E" . ($attempt - $attemptions));
                    } else {
                        if ($event_isCup) {
                            $pdf->Text(5, $Y + $D_Att / 2, $attempt);
                        } else {
                            $pdf->Text(10, $Y + $D_Att / 2, $attempt);
                        }
                    }
                }


                $size = getimagesize($filename);
                $max_width = $X_IMG_1 - $X_IMG_0;
                $max_height = $D_Att - 1;
                $k = min($max_width / $size[0], $max_height / $size[1]);
                $img_dx = ($max_width - $k * $size[0]) / 2;
                $img_dy = ($D_Att - $k * $size[1]) / 2;

                $pdf->Image($filename, $X_IMG_0 + $img_dx, $Y + $img_dy, $k * $size[0], $k * $size[1]);
                $Y += $D_Att;

                $pdf->Rect(17, $Y_Content_S, $X_IMG_1 - 17, $Y - $Y_Content_S);
                if ($Y + $D_Att > $pdf->GetPageHeight() - 15 and $group_scrambles->{$attempt + 1} ?? FALSE) {
                    $page++;
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Text(10, 286, $timestamp_pdf . ' #' . $pdf->PageNo());
                    $Y = $Y_Content_S;

                    $pdf->SetFont('Arial', '', 24);
                    $pdf->Text(10, 13, $event_name . $view_round);
                    $pdf->Text(170, 13, "Page $page");
                    $pdf->SetFont('Arial', '', 16);
                    if ($event_isCup) {
                        $pdf->Text(10, 20, 'Duel # ' . ($group + 1));
                    } else {
                        $pdf->Text(10, 20, 'Group ' . self::LETTER[$group]);
                    }
                    $pdf->Text(10, 27, $competition_name);
                }
            }
        }

        DeleteFolder($dir);
        return $pdf;
    }

}
