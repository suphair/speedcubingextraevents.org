<?php

Class IncludeClass {

    private static function Element($type, $file, $tag = false, $data = null) {
        foreach (scandir('Includes') as $dir) {
            $filename = "Includes/$dir/$file";
            if (!in_array($dir, ['.', '..']) and file_exists($filename)) {
                echo "\n<!-- start $filename -->\n";
                echo $tag ? "<$tag>\n" : "";
                include $filename;
                echo $tag ? "\n</$tag>" : "";
                echo "\n<!-- end $filename -->\n";
                return true;
            }
        }
        echo "\n<!-- none $type/$file -->\n";
        #exit();
    }

    public static function Page($file, $data = null, $ext = []) {
        if (is_array($ext)) {
            foreach ($ext as $key => $value) {
                $data->$key = $value;
            }
        }
        if(!$data){
            $data=(object)[];
        }
        $data->pageIndex= PageIndex();
        self::Element("Page", "$file.php", false, $data);
    }

    public static function Template($file, $data = null) {
        self::Element("Style", "$file.css", "style");
        self::Element("Template", "$file.t.php", false, $data);
        self::Element("JavaScript", "$file.js", "script");
    }

}
