<?php

function IncluderAction() {
    $request = getRequest();

    if (sizeof($request) >= 1) {
        if ($request[0] == "cron") {
            if (!(CheckAccess('Scripts') or $_SERVER['HTTP_USER_AGENT'] == 'Wget/1.17.1 (linux-gnu)' or strpos($_SERVER['PHP_SELF'], '/' . GetIni('LOCAL', 'PageBase') . '/') !== false)) {
                header('HTTP/1.1 401 Unauthorized');
                echo "<a href='" . PageIndex() . "'>" . GetIni('TEXT', 'title') . "</a>";
                echo "<h1 style='color:red'>You do not have permission to run [" . $file . "] cron script</h1>";
                exit();
            } else {
                $cron = new \Suphair\Cron(DataBaseClass::getConection());
                $cron->run();
                DataBaseClass::close();
                exit();
            }
        }
    }

    if (sizeof($request) >= 2) {
        $file = $request[1];
        if (strtolower($request[0]) == "actions") {
            SetPostValues($request[1]);
            foreach (scandir('Includes') as $dir) {
                foreach (scandir("Includes/$dir") as $dir2) {
                    if (strpos($dir2, 'Action') !== false) {
                        $arr[] = "Includes/$dir/$dir2/$file.php";
                    }
                }
                if (strpos($dir, 'Action') !== false) {
                    $arr[] = "Includes/$dir/$file.php";
                }
            };
            if (!IncludeExists($arr)) {
                header('HTTP/1.0 404 not found');
                echo "<a href='" . PageIndex() . "'>" . GetIni('TEXT', 'title') . "</a>";
                echo "<h1 style='color:red'>The action [" . $file . "] is not found </h1>";
                exit();
            }
        }
    }
}

function IncludeExists($file) {
    if (is_array($file)) {
        foreach ($file as $f) {
            if (file_exists($f)) {
                include $f;
                return true;
            }
        }
        return false;
    } else {
        if (file_exists($file)) {
            include $file;
            return true;
        } else {
            return false;
        }
    }
}

function getRequest() {

    global $request;
    $request_ = explode('?', $_SERVER['REQUEST_URI'])[0];
    $request = explode("/", str_replace("/" . getIni("LOCAL", "PageBase") . "/", "/", $request_));
    unset($request[0]);
    foreach ($request as $n => $v) {
        if (!$v)
            unset($request[$n]);
    }
    $request = array_values($request);
    if (isset($request[0]) and $request[0] != 'Actions') {
        foreach ($request as $k => $v) {
            $request[$k] = DataBaseClass::Escape(strtolower($v));
        }
    }
    return $request;
}

function getRequestString() {
    return str_replace("/" . getIni("LOCAL", "PageBase") . "/", "/", $_SERVER['REQUEST_URI']);
}

function Request($n = false) {
    global $request;
    if ($n) {
        if (isset($request[$n])) {
            return $request[$n];
        } else {
            return false;
        }
    }
    return $request;
}

function getPathElement($baseElement, $n) {

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $elements = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    foreach ($elements as $e => $element) {
        if (strtolower($baseElement) == strtolower($element) and isset($elements[$e + $n])) {
            return strtolower($elements[$e + $n]);
        }
    }
    return false;
}

function getQueryElement($name, $values = false) {
    $value = filter_input(INPUT_GET, $name);
    if (!$values or in_array($value, $values)) {
        return strtolower($value);
    } else {
        return strtolower($values[0]);
    }
}

function IncluderScript() {
    if (isset($_GET['Script'])) {
        if (CheckAccess('Scripts')) {
            $Script = "Script_{$_GET['Script']}";
            echo "[$Script]";
            if (function_exists($Script)) {
                echo "Found ";
                eval("$Script();");
                echo "Complete ";
            } else {
                echo "Not found";
            }
            exit();
        }
    }
}

function inputGet($keys) {
    $request = [];
    foreach ($keys as $key) {
        $request[$key] = strtolower(filter_input(INPUT_GET, $key));
    }
    return arrayToObject($request);
}
