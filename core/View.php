<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 27/05/2015
 * Time: 20:36
 */

class View {
    public static function renderFile($viewFile, $params, $header) {
        if (!file_exists(ROOT.'/views/'.$viewFile.'.php'))
            return '';
        extract($params);
        ob_start();
        if ($header && file_exists(ROOT.'/views/htmlheader.php'))
            include ROOT.'/views/htmlheader.php';
        include ROOT.'/views/'.$viewFile.'.php';
        if ($header && file_exists(ROOT.'/views/htmlfooter.php'))
            include ROOT.'/views/htmlfooter.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public static function render($viewFile, $params = []) {
        return self::renderFile($viewFile, $params, true);
    }

    public static function partial($viewFile, $params = []) {
        return self::renderFile($viewFile, $params, false);
    }
} 