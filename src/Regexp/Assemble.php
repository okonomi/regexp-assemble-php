<?php


class Regexp_Assemble
{
    // The following patterns were generated with eg/naive
    const Default_Lexer = '/(?![[(\\]).(?:[*+?]\??|\{\d+(?:,\d*)?\}\??)?|\\(?:[bABCEGLQUXZ]|[lu].|(?:[^\w]|[aefnrtdDwWsS]|c.|0\d{2}|x(?:[\da-fA-F]{2}|{[\da-fA-F]{4}})|N\{\w+\}|[Pp](?:\{\w+\}|.))(?:[*+?]\??|\{\d+(?:,\d*)?\}\??)?)|\[.*?(?<!\\)\](?:[*+?]\??|\{\d+(?:,\d*)?\}\??)?|\(.*?(?<!\\)\)(?:[*+?]\??|\{\d+(?:,\d*)?\}\??)?/'; // ]) restore equilibrium


    protected $path = array();

    public $chomp;
    public $indent;
    public $mutable;
    public $flags;
    public $modifiers;
    public $track;
    public $reduce;

    public $pre_filter;

    public $filter;


    function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            call_user_func(array($this, $key), $value);
        }
    }

    public function chomp($chomp = 1) {
        $this->chomp = $chomp;
        return $this;
    }

    public function indent($indent = 0) {
        $this->indent = $indent;
        return $this;
    }

    public function reduce($reduce = 1) {
        $this->reduce = $reduce;
        return $this;
    }

    public function mutable($mutable = 1) {
        $this->mutable = $mutable;
        return $this;
    }

    public function flags($flags = '') {
        $this->flags = $flags;
        return $this;
    }

    public function modifiers($modifiers = '') {
        return $this->flags($modifiers);;
    }

    public function track($track = 1) {
        $this->track = $track;
        return $this;
    }

    public function pre_filter($pre_filter) {
        $this->pre_filter = $pre_filter;
        return $this;
    }

    public function filter($filter) {
        $this->filter = $filter;
        return $this;
    }

    static public function _node_key($node) {
        $key = '';
        foreach ($node as $k => $v) {
            if (!empty($k)) {
                $key = $k;
                break;
            }
        }

        return $key;
    }

    public function _path() {
        // access the path
        return $this->path;
    }
}
