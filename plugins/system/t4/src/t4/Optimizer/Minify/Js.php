<?php
namespace T4\Optimizer\Minify;

class Js extends \MatthiasMullie\Minify\JS {
    /**
     * Strip comments from source code.
     */
    protected function stripComments()
    {
        // PHP only supports $this inside anonymous functions since 5.4
        $minifier = $this;
        $callback = function ($match) use ($minifier) {
            $count = count($minifier->extracted);
            $placeholder = '/*'.$count.'*/';
            $minifier->extracted[$placeholder] = $match[0];

            return $placeholder;
        };
        // multi-line comments
        // Remove all comments
        //$this->registerPattern('/\n?\/\*(!|.*?@license|.*?@preserve).*?\*\/\n?/s', $callback);
        $this->registerPattern('/\/\*.*?\*\//s', '');

        // single-line comments
        $this->registerPattern('/\/\/.*$/m', '');
    }
 
}
