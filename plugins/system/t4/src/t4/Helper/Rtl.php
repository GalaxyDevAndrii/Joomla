<?php
namespace T4\Helper;

class Rtl
{
    public static function render()
    {

        $ltrfile = Path::findInTheme('css/template.css');
        $rtlfile = T4PATH_MEDIA . '/css/rtl.css';

        if (!is_file($rtlfile) || filemtime($rtlfile) < filemtime($ltrfile)) {
            require_once T4PATH . '/vendor/autoload.php';
            require_once T4PATH . '/vendor/rtlcss/rtlcss.php';

            $ltrcss = file_get_contents($ltrfile);
            $parser = new \Sabberworm\CSS\Parser($ltrcss);
            $tree = $parser->parse();
            $rtlcss = new \MoodleHQ\RTLCSS\RTLCSS($tree);
            $rtlcss->flip();
            $rtlcss = $tree->render();
            $protocols  = '[a-zA-Z0-9\-]+:';
            $regex_url  = '\s*url\s*\(([\'\"]|\&\#0?3[49];)?(?!\/|\&\#0?3[49];|' . $protocols . '|\#)([^\)\'\"]+)([\'\"]|\&\#0?3[49];)?\)';
            $regex  = '#' . $regex_url . '#m';
            preg_match_all($regex, $rtlcss, $matches);

            if(count($matches[2])){
                foreach ($matches[2] as $match) {
                    $path = str_replace(JPATH_ROOT, "", Path::findInTheme(str_replace("../", "", $match)));
                    $rtlcss = str_replace($match, '../../..'.$path, $rtlcss);
                    
                }
            }
            \JFile::write($rtlfile, $rtlcss);
        }

        return T4PATH_MEDIA_URI . '/css/rtl.css';
    }
}
