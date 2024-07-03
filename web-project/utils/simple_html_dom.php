<?php
/**
 * Simple HTML DOM Parser
 * 
 * A simple PHP HTML DOM parser written by S.C. Chen.
 * 
 * https://simplehtmldom.sourceforge.io/
 * 
 * @license MIT
 */

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT', 3);
define('HDOM_TYPE_ENDTAG', 4);
define('HDOM_TYPE_ROOT', 5);
define('HDOM_TYPE_UNKNOWN', 6);

define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO', 3);

define('HDOM_INFO_BEGIN', 0);
define('HDOM_INFO_END', 1);
define('HDOM_INFO_QUOTE', 2);
define('HDOM_INFO_SPACE', 3);
define('HDOM_INFO_TEXT', 4);
define('HDOM_INFO_INNER', 5);
define('HDOM_INFO_OUTER', 6);
define('HDOM_INFO_ENDSPACE', 7);

define('DEFAULT_TARGET_CHARSET', 'UTF-8');
define('DEFAULT_BR_TEXT', "\r\n");
define('DEFAULT_SPAN_TEXT', " ");
define('MAX_FILE_SIZE', 600000);

// helper functions
// -----------------------------------------------------------------------------
// get html dom from file
if (!function_exists('file_get_html')) {
    function file_get_html($url, $use_include_path = false, $context = null, $offset = 0, $maxLen = 1000000, $lowercase = true, $forceTagsClosed = true, $target_charset = 'UTF-8', $stripRN = true, $defaultBRText = "\r\n", $defaultSpanText = ' ') {
        
        $maxLen = ($maxLen < 0 || $maxLen > 1000000) ? 1000000 : intval($maxLen);

        // We DO force the tags to be lowercase by default
        $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $defaultBRText, $defaultSpanText);
        $contents = file_get_contents($url, $use_include_path, $context, $offset, $maxLen);
        if ($contents === false || strlen($contents) > 1000000) {
            return false;
        }
        $dom->load($contents, $lowercase, $stripRN);
        return $dom;
    }
}

if (!function_exists('str_get_html')) {
    function str_get_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT) {
        $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > MAX_FILE_SIZE) {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }
}


if (!function_exists('dump_html_tree')) {
    function dump_html_tree($node, $show_attr = true, $deep = 0) {
        $node->dump($node);
    }
}

// -----------------------------------------------------------------------------
// simple html dom node
// -----------------------------------------------------------------------------
class simple_html_dom_node {
    public $nodetype = HDOM_TYPE_TEXT;
    public $tag = 'text';
    public $attr = array();
    public $children = array();
    public $nodes = array();
    public $parent = null;
    public $_ = array();
    public $tag_start = 0;

    private $dom = null;

    function __construct($dom) {
        $this->dom = $dom;
        $dom->nodes[] = $this;
    }

    function __destruct() {
        $this->clear();
    }

    function __toString() {
        return $this->outertext();
    }

    function clear() {
        $this->dom = null;
        $this->nodes = null;
        $this->parent = null;
        $this->children = null;
    }

    function dump($show_attr = true, $deep = 0) {
        $lead = str_repeat('    ', $deep);
        echo $lead . $this->tag;
        if ($show_attr && count($this->attr) > 0) {
            echo '(';
            foreach ($this->attr as $k => $v) {
                echo "[$k]=>\"$v\", ";
            }
            echo ')';
        }
        echo "\n";

        if ($this->nodes) {
            foreach ($this->nodes as $c) {
                $c->dump($show_attr, $deep + 1);
            }
        }
    }


    function innerHtml() {
        if (isset($this->_[HDOM_INFO_INNER])) {
            return $this->_[HDOM_INFO_INNER];
        }
        if (!$this->nodes) {
            return '';
        }

        $ret = '';
        foreach ($this->nodes as $n) {
            $ret .= $n->outertext();
        }
        return $ret;
    }

    function outerHtml() {
        if ($this->tag === 'root') {
            return $this->innerHtml();
        }


        if (isset($this->_[HDOM_INFO_OUTER])) {
            return $this->_[HDOM_INFO_OUTER];
        }
        if (!$this->nodes) {
            return '';
        }

        $ret = '';
        if ($this->tag != 'text') {
            $ret = '<' . $this->tag;
            if (count($this->attr) > 0) {
                foreach ($this->attr as $k => $v) {
                    if ($v === null || $v === false) {
                        continue;
                    }
                    $ret .= " $k";
                    if ($v !== true) {
                        $ret .= '="' . htmlspecialchars($v, ENT_QUOTES) . '"';
                    }
                }
            }
            $ret .= '>';
        }
        if ($this->nodes) {
            foreach ($this->nodes as $n) {
                $ret .= $n->outertext();
            }
        }
        if ($this->tag != 'text' && $this->tag != 'br' && $this->tag != 'hr' && $this->tag != 'img' && $this->tag != 'input' && $this->tag != 'meta' && $this->tag != 'link' && $this->tag != 'base' && $this->tag != 'col' && $this->tag != 'frame' && $this->tag != 'area') {
            $ret .= '</' . $this->tag . '>';
        }
        return $ret;
    }


    function find($selector, $idx = null, $lowercase = true) {
        $selectors = $this->parse_selector($selector);
        if (($count = count($selectors)) === 0) {
            return array();
        }

        $found_keys = array();

        for ($c = 0; $c < $count; ++$c) {
            if (($levle = count($selectors[$c])) === 0) {
                return array();
            }
            if (!isset($this->_[HDOM_INFO_BEGIN])) {
                return array();
            }

            $head = array($this->_[HDOM_INFO_BEGIN] => 1);

            for ($l = 0; $l < $levle; ++$l) {
                $ret = array();
                foreach ($head as $k => $v) {
                    $n = ($k === -1) ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret);
                }
                $head = $ret;
            }

            foreach ($head as $k => $v) {
                if (!isset($found_keys[$k])) {
                    $found_keys[$k] = 1;
                }
            }
        }

        ksort($found_keys);

        $found = array();
        foreach ($found_keys as $k => $v) {
            $found[] = $this->dom->nodes[$k];
        }

        if (is_null($idx)) {
            return $found;
        }
        if ($idx < 0) {
            $idx = count($found) + $idx;
        }
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    protected function parse_selector($selector_string) {
        $pattern = '/(\w+)?(\#[\w\-]+)?(\.[\w\-]+)?(\[[^\]]+\])?(\:[^\s\+>]+)?([\s\+>]+)?/';
        preg_match_all($pattern, trim($selector_string) . ' ', $matches, PREG_SET_ORDER);
        $selectors = array();
        $result = array();
        foreach ($matches as $m) {
            $result[] = array(
                'tag' => ($m[1]) ? $m[1] : '*',
                'id' => (isset($m[2])) ? substr($m[2], 1) : null,
                'class' => (isset($m[3])) ? substr($m[3], 1) : null,
                'attributes' => (isset($m[4])) ? $this->parse_attr($m[4]) : null,
                'pseudo' => (isset($m[5])) ? $m[5] : null,
                'rel' => (isset($m[6])) ? trim($m[6]) : null,
            );
        }
        return $result;
    }

    protected function parse_attr($attr_string) {
        $pattern = '/\[\s*([^\=\~\|\^\$\*]+)\s*(\=|\~\=|\|\=|\^\=|\$\=|\*\=)?\s*([^\]]+)?\s*\]/';
        preg_match_all($pattern, trim($attr_string) . ' ', $matches, PREG_SET_ORDER);
        $attributes = array();
        foreach ($matches as $m) {
            $attributes[] = array(
                'name' => $m[1],
                'operator' => (isset($m[2])) ? $m[2] : null,
                'value' => (isset($m[3])) ? $m[3] : null,
            );
        }
        return $attributes;
    }
}

// -----------------------------------------------------------------------------
// simple html dom
// -----------------------------------------------------------------------------
class simple_html_dom {
    public $root = null;
    public $nodes = array();
    public $lowercase = false;
    public $default_br_text = '';
    public $default_span_text = '';

    private $doc = '';
    private $pos;
    private $cursor;
    private $parent;
    private $noise = array();
    private $token_blank = " \t\r\n";
    private $token_equal = ' =/>';
    private $token_slash = " />\r\n\t";
    private $token_attr = ' >';
    private $char_lowercase = 'abcdefghijklmnopqrstuvwxyz';
    private $char_uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $char_digit = '0123456789';
    private $noise_char = '\'"`';
    private $allowed_charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private $lowercase_charset = 'abcdefghijklmnopqrstuvwxyz';
    private $default_charset = DEFAULT_TARGET_CHARSET;

    function __construct($str = null, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT) {
        if ($str) {
            if (preg_match('/^http:\/\//i', $str) || is_file($str)) {
                $this->load_file($str);
            } else {
                $this->load($str);
            }
        }
    }

    function __destruct() {
        $this->clear();
    }

    function load($str, $lowercase = true, $stripRN = true) {
        $this->prepare($str, $lowercase, $stripRN);
        while ($this->parse());
        $this->root->_[HDOM_INFO_END] = $this->cursor;
        return $this;
    }

    function load_file() {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
    }

    function prepare($str, $lowercase = true, $stripRN = true) {
        $this->clear();
        $this->doc = $str;
        if ($stripRN) {
            $this->doc = str_replace("\r", '', $this->doc);
            $this->doc = str_replace("\n", '', $this->doc);
        }
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->lowercase = $lowercase;
        $this->default_br_text = "\r\n";
        $this->default_span_text = ' ';
        $this->root = new simple_html_dom_node($this);
        $this->root->tag = 'root';
        $this->root->_[HDOM_INFO_BEGIN] = 0;
    }

    function parse() {
        if (($s = $this->copy_until_char('<')) === '') {
            return false;
        }
        $node = new simple_html_dom_node($this);
        $node->_[HDOM_INFO_TEXT] = $s;
        $this->link_nodes($node, false);
        return true;
    }

    function copy_until_char($char) {
        $start = $this->pos;
        while ($this->pos < strlen($this->doc)) {
            if ($this->doc[$this->pos] === $char) {
                return substr($this->doc, $start, $this->pos - $start);
            }
            ++$this->pos;
        }
        return '';
    }

    function link_nodes($node, $is_child) {
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;
        if ($is_child) {
            $this->parent->children[] = $node;
        }
    }

    function clear() {
        foreach ($this->nodes as $n) {
            $n->clear();
            $n = null;
        }
        if (isset($this->root)) {
            $this->root->clear();
        }
        $this->root = null;
        $this->nodes = null;
        $this->noise = null;
        $this->doc = null;
    }

    function find($selector, $idx = null, $lowercase = true) {
        $selectors = $this->parse_selector($selector);
        if (($count = count($selectors)) === 0) {
            return array();
        }
        $found_keys = array();
        for ($c = 0; $c < $count; ++$c) {
            if (($levle = count($selectors[$c])) === 0) {
                return array();
            }
            if (!isset($this->_[HDOM_INFO_BEGIN])) {
                return array();
            }
            $head = array($this->_[HDOM_INFO_BEGIN] => 1);
            for ($l = 0; $l < $levle; ++$l) {
                $ret = array();
                foreach ($head as $k => $v) {
                    $n = ($k === -1) ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret);
                }
                $head = $ret;
            }
            foreach ($head as $k => $v) {
                if (!isset($found_keys[$k])) {
                    $found_keys[$k] = 1;
                }
            }
        }
        ksort($found_keys);
        $found = array();
        foreach ($found_keys as $k => $v) {
            $found[] = $this->dom->nodes[$k];
        }
        if (is_null($idx)) {
            return $found;
        }
        if ($idx < 0) {
            $idx = count($found) + $idx;
        }
        return (isset($found[$idx])) ? $found[$idx] : null;
    }

    protected function parse_selector($selector_string) {
        $pattern = '/(\w+)?(\#[\w\-]+)?(\.[\w\-]+)?(\[[^\]]+\])?(\:[^\s\+>]+)?([\s\+>]+)?/';
        preg_match_all($pattern, trim($selector_string) . ' ', $matches, PREG_SET_ORDER);
        $selectors = array();
        $result = array();
        foreach ($matches as $m) {
            $result[] = array(
                'tag' => isset($m[1]) ? $m[1] : '*',
                'id' => isset($m[2]) ? substr($m[2], 1) : null,
                'class' => isset($m[3]) ? substr($m[3], 1) : null,
                'attributes' => isset($m[4]) ? $this->parse_attr($m[4]) : null,
                'pseudo' => isset($m[5]) ? $m[5] : null,
                'rel' => isset($m[6]) ? trim($m[6]) : null,
            );
        }
        return $result;
    }

    protected function parse_attr($attr_string) {
        $pattern = '/\[\s*([^\=\~\|\^\$\*]+)\s*(\=|\~\=|\|\=|\^\=|\$\=|\*\=)?\s*([^\]]+)?\s*\]/';
        preg_match_all($pattern, trim($attr_string) . ' ', $matches, PREG_SET_ORDER);
        $attributes = array();
        foreach ($matches as $m) {
            $attributes[] = array(
                'name' => isset($m[1]) ? $m[1] : null,
                'operator' => isset($m[2]) ? $m[2] : null,
                'value' => isset($m[3]) ? $m[3] : null,
            );
        }
        return $attributes;
    }
}

?>
