<?php

require_once 'PHP/Perlish.php';

PHP_Perlish::loadFunction('is_hash');


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
    public $fold_meta_pairs = 1;

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

    public function fold_meta_pairs($fold_meta_pairs = 1) {
        $this->fold_meta_pairs = $fold_meta_pairs;
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

    // return the offset that the first node is found, or -ve
    // optimised for speed
    static public function _node_offset($values) {
        $atom = -1;
        foreach ($values as $value) {
            $atom++;
            if (is_hash($value)) {
                return $atom;
            }
        }

        return -1;
    }

    static public function _node_eq($lvalue, $rvalue) {
        if (is_null($lvalue) && is_null($rvalue)) {
            return false;
        }

        return $lvalue === $rvalue;

        /*
        return 0 if not defined $_[0] or not defined $_[1];
        return 0 if ref $_[0] ne ref $_[1];
        # Now that we have determined that the reference of each
        # argument are the same, we only have to test the first
        # one, which gives us a nice micro-optimisation.
        if( ref($_[0]) eq 'HASH' ) {
            keys %{$_[0]} == keys %{$_[1]}
                and
            # does this short-circuit to avoid _re_path() cost more than it saves?
            join( '|' => sort keys %{$_[0]}) eq join( '|' => sort keys %{$_[1]})
                and
            _re_path(undef, [$_[0]] ) eq _re_path(undef, [$_[1]] );
        }
        elsif( ref($_[0]) eq 'ARRAY' ) {
            scalar @{$_[0]} == scalar @{$_[1]}
                and
            _re_path(undef, $_[0]) eq _re_path(undef, $_[1]);
        }
        else {
            $_[0] eq $_[1];
        }
         */
    }
    /*
public function _re_path {
            my $self = shift;
            // in shorter assemblies, _re_path() is the second hottest
            // routine. after insert(), so make it fast.

    if ($self->{unroll_plus}) {
        # but we can't easily make this blockless
        my @arr = @{$_[0]};
        my $str = '';
        my $skip = 0;
        for my $i (0..$#arr) {
            if (ref($arr[$i]) eq 'ARRAY') {
                $str .= _re_path($self, $arr[$i]);
            }
            elsif (ref($arr[$i]) eq 'HASH') {
                $str .= exists $arr[$i]->{''}
                    ? _combine_new( $self,
                        map { _re_path( $self, $arr[$i]->{$_} ) } grep { $_ ne '' } keys %{$arr[$i]}
                    ) . '?'
                    : _combine_new($self, map { _re_path( $self, $arr[$i]->{$_} ) } keys %{$arr[$i]})
                ;
            }
            elsif ($i < $#arr and $arr[$i+1] =~ /\A$arr[$i]\*(\??)\Z/) {
                $str .= "$arr[$i]+" . (defined $1 ? $1 : '');
                ++$skip;
            }
            elsif ($skip) {
                $skip = 0;
            }
            else {
                $str .= $arr[$i];
            }
        }
        return $str;
    }

    return join( '', @_ ) unless grep { length ref $_ } @_;
    my $p;
    return join '', map {
        ref($_) eq '' ? $_
        : ref($_) eq 'HASH' ? do {
            # In the case of a node, see whether there's a '' which
            # indicates that the whole thing is optional and thus
            # requires a trailing ?
            # Unroll the two different paths to avoid the needless
            # grep when it isn't necessary.
            $p = $_;
            exists $_->{''}
            ?  _combine_new( $self,
                map { _re_path( $self, $p->{$_} ) } grep { $_ ne '' } keys %$_
            ) . '?'
            : _combine_new($self, map { _re_path( $self, $p->{$_} ) } keys %$_ )
        }
        : _re_path($self, $_) # ref($_) eq 'ARRAY'
    } @{$_[0]}
}
    */

    static public function _make_class(&$stub, $set) {
        $set = array_combine($set, array_pad(array(), count($set), 1));

        if (isset($set['\\w'])) {unset($set['\\d']);}
        if (isset($set['\\W'])) {unset($set['\\D']);}

        if (isset($set['.']) ||
            ($stub->fold_meta_pairs && (
                   (isset($set['\\d']) && isset($set['\\D']))
                or (isset($set['\\s']) && isset($set['\\S']))
                or (isset($set['\\w']) && isset($set['\\W']))
            ))) {
            return '.';
        }

        foreach (array('\\d', '\\D', '\\s', '\\S', '\\w', '\\W') as $meta) {
            if (isset($set[$meta])) {
                foreach (array_keys($set) as $key) {
                    if (preg_match('/^'.$meta.'$/', $key)) {
                        unset($set[$key]);
                    }
                }
            }
        }

        if (count($set) === 1) {
            reset($set);
            return key($set);
        }

        foreach (array( '.', '+', '*', '?', '(', ')', '^', '@', '$', '[', '/', ) as $meta) {
            if (isset($set["\\$meta"])) {
                unset($set["\\$meta"]);
                $set[$meta] = 1;
            }
        }

        $dash = '';
        if (isset($set['-'])) {
            unset($set['-']);
            $dash = '-';
        }
        $caret = '';
        if (isset($set['^'])) {
            unset($set['^']);
            $caret = '^';
        }

        ksort($set);
        $class = implode('', array_keys($set));
        if ($c = preg_replace('/0123456789/', '\d', $class)) {
            if ($c === '\d') {
                return $c;
            }
        }

        return "[$dash$class$caret]";
    }

    public function _path() {
        // access the path
        return $this->path;
    }
}
