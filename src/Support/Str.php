<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Support;

/*
|---------------------------------------------------
| String
|---------------------------------------------------
*/
class Str {
    
    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\ExtendingTrait;
         
    /**
     * @var array the rules for converting a word into its plural form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     */
    protected $plurals = [
        '/([nrlm]ese|deer|fish|sheep|measles|ois|pox|media)$/i' => '\1',
        '/^(sea[- ]bass)$/i' => '\1',
        '/(m)ove$/i' => '\1oves',
        '/(f)oot$/i' => '\1eet',
        '/(h)uman$/i' => '\1umans',
        '/(s)tatus$/i' => '\1tatuses',
        '/(s)taff$/i' => '\1taff',
        '/(t)ooth$/i' => '\1eeth',
        '/(quiz)$/i' => '\1zes',
        '/^(ox)$/i' => '\1\2en',
        '/([m|l])ouse$/i' => '\1ice',
        '/(matr|vert|ind)(ix|ex)$/i' => '\1ices',
        '/(x|ch|ss|sh)$/i' => '\1es',
        '/([^aeiouy]|qu)y$/i' => '\1ies',
        '/(hive)$/i' => '\1s',
        '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '\1a',
        '/(p)erson$/i' => '\1eople',
        '/(m)an$/i' => '\1en',
        '/(c)hild$/i' => '\1hildren',
        '/(buffal|tomat|potat|ech|her|vet)o$/i' => '\1oes',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
        '/us$/i' => 'uses',
        '/(alias)$/i' => '\1es',
        '/(ax|cris|test)is$/i' => '\1es',
        '/(currenc)y$/' => '\1ies',
        '/s$/' => 's',
        '/^$/' => '',
        '/$/' => 's',
    ];

    /**
     * @var array the rules for converting a word into its singular form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     */
    public $singulars = [
        '/([nrlm]ese|deer|fish|sheep|measles|ois|pox|media|ss)$/i' => '\1',
        '/^(sea[- ]bass)$/i' => '\1',
        '/(s)tatuses$/i' => '\1tatus',
        '/(f)eet$/i' => '\1oot',
        '/(t)eeth$/i' => '\1ooth',
        '/^(.*)(menu)s$/i' => '\1\2',
        '/(quiz)zes$/i' => '\\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias)(es)*$/i' => '\1',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
        '/([ftw]ax)es/i' => '\1',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe|slave)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/ouses$/' => 'ouse',
        '/([^a])uses$/' => '\1us',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1\2ovie',
        '/(s)eries$/i' => '\1\2eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/(drive)s$/i' => '\1',
        '/([^fo])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(p)eople$/i' => '\1\2erson',
        '/(m)en$/i' => '\1an',
        '/(c)hildren$/i' => '\1\2hild',
        '/(n)ews$/i' => '\1\2ews',
        '/(n)etherlands$/i' => '\1\2etherlands',
        '/eaus$/' => 'eau',
        '/(currenc)ies$/' => '\1y',
        '/^(.*us)$/' => '\\1',
        '/s$/i' => '',
    ];

    /**
     * @var array the special rules for converting a word between its plural form and singular form.
     * The keys are the special words in singular form, and the values are the corresponding plural form.
     */
    protected $specials = [
        'atlas' => 'atlases',
        'beef' => 'beefs',
        'brother' => 'brothers',
        'cafe' => 'cafes',
        'child' => 'children',
        'cookie' => 'cookies',
        'corpus' => 'corpuses',
        'cow' => 'cows',
        'curve' => 'curves',
        'foe' => 'foes',
        'ganglion' => 'ganglions',
        'genie' => 'genies',
        'genus' => 'genera',
        'graffito' => 'graffiti',
        'hoof' => 'hoofs',
        'loaf' => 'loaves',
        'man' => 'men',
        'money' => 'monies',
        'mongoose' => 'mongooses',
        'move' => 'moves',
        'mythos' => 'mythoi',
        'niche' => 'niches',
        'numen' => 'numina',
        'occiput' => 'occiputs',
        'octopus' => 'octopuses',
        'opus' => 'opuses',
        'ox' => 'oxen',
        'pasta' => 'pasta',
        'penis' => 'penises',
        'sex' => 'sexes',
        'soliloquy' => 'soliloquies',
        'testis' => 'testes',
        'trilby' => 'trilbys',
        'turf' => 'turfs',
        'wave' => 'waves',
        'Amoyese' => 'Amoyese',
        'bison' => 'bison',
        'Borghese' => 'Borghese',
        'bream' => 'bream',
        'breeches' => 'breeches',
        'britches' => 'britches',
        'buffalo' => 'buffalo',
        'cantus' => 'cantus',
        'carp' => 'carp',
        'chassis' => 'chassis',
        'clippers' => 'clippers',
        'cod' => 'cod',
        'coitus' => 'coitus',
        'Congoese' => 'Congoese',
        'contretemps' => 'contretemps',
        'corps' => 'corps',
        'debris' => 'debris',
        'diabetes' => 'diabetes',
        'djinn' => 'djinn',
        'eland' => 'eland',
        'elk' => 'elk',
        'equipment' => 'equipment',
        'Faroese' => 'Faroese',
        'flounder' => 'flounder',
        'Foochowese' => 'Foochowese',
        'gallows' => 'gallows',
        'Genevese' => 'Genevese',
        'Genoese' => 'Genoese',
        'Gilbertese' => 'Gilbertese',
        'graffiti' => 'graffiti',
        'headquarters' => 'headquarters',
        'herpes' => 'herpes',
        'hijinks' => 'hijinks',
        'Hottentotese' => 'Hottentotese',
        'information' => 'information',
        'innings' => 'innings',
        'jackanapes' => 'jackanapes',
        'Kiplingese' => 'Kiplingese',
        'Kongoese' => 'Kongoese',
        'Lucchese' => 'Lucchese',
        'mackerel' => 'mackerel',
        'Maltese' => 'Maltese',
        'mews' => 'mews',
        'moose' => 'moose',
        'mumps' => 'mumps',
        'Nankingese' => 'Nankingese',
        'news' => 'news',
        'nexus' => 'nexus',
        'Niasese' => 'Niasese',
        'Pekingese' => 'Pekingese',
        'Piedmontese' => 'Piedmontese',
        'pincers' => 'pincers',
        'Pistoiese' => 'Pistoiese',
        'pliers' => 'pliers',
        'Portuguese' => 'Portuguese',
        'proceedings' => 'proceedings',
        'rabies' => 'rabies',
        'rice' => 'rice',
        'rhinoceros' => 'rhinoceros',
        'salmon' => 'salmon',
        'Sarawakese' => 'Sarawakese',
        'scissors' => 'scissors',
        'series' => 'series',
        'Shavese' => 'Shavese',
        'shears' => 'shears',
        'siemens' => 'siemens',
        'species' => 'species',
        'swine' => 'swine',
        'testes' => 'testes',
        'trousers' => 'trousers',
        'trout' => 'trout',
        'tuna' => 'tuna',
        'Vermontese' => 'Vermontese',
        'Wenchowese' => 'Wenchowese',
        'whiting' => 'whiting',
        'wildebeest' => 'wildebeest',
        'Yengeese' => 'Yengeese',
    ];

    /**
     * Converts a word to its plural form.
     * Note that this is for English only!
     * For example, 'apple' will become 'apples', and 'child' will become 'children'.
     * @param string $word the word to be pluralized
     * @return string the pluralized word
     */
    public function pluralize($word)
    {
        if (isset($this->specials[$word])) {
            return $this->specials[$word];
        }
        foreach ($this->plurals as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    /**
     * Returns the singular of the $word.
     * @param string $word the english word to singularize
     * @return string Singular noun.
     */
    public function singularize($word)
    {
        $result = array_search($word, $this->specials, true);
        if ($result !== false) {
            return $result;
        }
        foreach ($this->singulars as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    /**
     * Replace Everything Inside Two Strings
     */
    public function replaceBetween(string $string, string $str, string $start, string $end): string
    {
        return preg_replace('/'.preg_quote($start).'[\s\S]+?'.preg_quote($end).'/', $str, $string);
    }
   
    /**
     * make slug (URL string)
     */
    public function slug($string, $replacement = '-')
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', $replacement, $string)));
    }

    /**
     * Limit the number of characters in a string. Put value of $end to the string end.
     *
     * ### limit
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * limit( string $string, int $limit = 100, string $end = '...' ): string
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     *
     * limit( $string, 15 );
     *
     * // The quick brown...
     * ```
     *
     * @param  string $string
     * The string to limit the characters.
     * @param  int    $limit
     * The number of characters to limit. Defaults to 100.
     * @param  string $end
     * The string to end the cut string. Defaults to '...'
     * @return string
     * The limited string with $end at the end.
     */
    public function limit($string, $limit = 100, $end = '...')
    {
        if (mb_strwidth($string, 'UTF-8') <= $limit) {
            return $string;
        }

        return rtrim(mb_strimwidth($string, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Get the part of a string before a given value.
     *
     * ### before
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * before( string $search, string $string ): string
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     *
     * before( 'fox' $string );
     *
     * // The quick brown
     * ```
     *
     * @param string $search
     * The string to search for.
     * @param string $string
     * The string to search in.
     * @return string
     * The found string before the search string. Whitespaces at end will be removed.
     */
    public function before($string, $search)
    {
        return $search === '' ? $string : rtrim(explode($search, $string)[0]);
    }

    /**
     * Return the part of a string after a given value.
     *
     * ### after
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * after( string $search, string $string ): string
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     *
     * after( 'fox' $string );
     *
     * // jumps over the lazy dog
     * ```
     *
     * @param string $search
     * The string to search for.
     * @param string $string
     * The string to search in.
     * @return string
     * The found string after the search string. Whitespaces at beginning will be removed.
     */
    public function after($string, $search)
    {
        return $search === '' ? $string : ltrim(array_reverse(explode($search, $string, 2))[0]);
    }

    /**
     * Return the content in a string between a left and right element.
     *
     * ### between
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * between( string $left, string $right, string $string ): array
     * ```
     *
     * #### Example
     * ```php
     * $string = '<tag>foo</tag>foobar<tag>bar</tag>'
     *
     * between( '<tag>', '</tag>' $string );
     *
     * // (
     * //     [0] => foo
     * //     [1] => bar
     * // )
     * ```
     *
     *
     * @param string $left
     * The left element of the string to search.
     * @param string $right
     * The right element of the string to search.
     * @param string $string
     * The string to search in.
     * @return array
     * A result array with all matches of the search.
     */
    public function between($string, $left, $right)
    {
        preg_match_all('/' . preg_quote($left, '/') . '(.*?)' . preg_quote($right, '/') . '/s', $string, $matches);
        $result = array_map('trim', $matches[1]);
        return $result[0];
    }
    
    /**
     * Tests if a string contains a given element
     *
     * ### contains
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * contains( string|array $needle, string $haystack ): boolean
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     * $array = [
     *      'cat',
     *      'fox'
     * ];
     *
     * contains( $array, $string );
     *
     * // bool(true)
     * ```
     *
     * @param string|array $needle
     * A string or an array of strings.
     * @param string       $haystack
     * The string to search in.
     * @return bool
     * True if $needle is found, false otherwise.
     */
    public function contains($string, $needle)
    {
        foreach ((array)$needle as $ndl) {
            if (strpos($string, $ndl) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tests if a string contains a given element. Ignore case sensitivity.
     *
     * ### icontains
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * icontains( string|array $needle, string $haystack ): boolean
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     * $array = [
     *      'Cat',
     *      'Fox'
     * ];
     *
     * icontains( $array, $string );
     *
     * // bool(true)
     * ```
     *
     * @param string|array $needle
     * A string or an array of strings.
     * @param string       $haystack
     * The string to search in.
     * @return bool
     * True if $needle is found, false otherwise.
     */
    public function containsIgnoreCase($string, $needle)
    {
        foreach ((array)$needle as $ndl) {
            if (stripos($string, $ndl) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring. Ignore case sensitivity.
     *
     * ### istarts_with
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * istarts_with( string|array $needle, string $haystack ): boolean
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     * $array = [
     *      'cat',
     *      'the'
     * ];
     *
     * istarts_with( $array, $string );
     *
     * // bool(true)
     * ```
     *
     * @param string|array $needle
     * The string or array of strings to search for.
     * @param string       $string
     * The string to search in.
     * @return bool
     * True if $needle was found, false otherwise.
     */
    public function startsWithIgnoreCase($string, $needle)
    {
        $hs = strtolower($string);

        foreach ((array)$needle as $ndl) {
            $n = strtolower($ndl);
            if ($n !== '' && substr($hs, 0, strlen($n)) === (string)$n) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * ### iends_with
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * iends_with( string|array $needle, string $haystack ): boolean
     * ```
     *
     * #### Example
     * ```php
     * $string = 'The quick brown fox jumps over the lazy dog';
     * $array = [
     *      'Cat',
     *      'Dog'
     * ];
     *
     * iends_with( $array, $string );
     *
     * // bool(true)
     * ```
     *
     * @param string|array $needle
     * The string or array of strings to search for.
     * @param string       $string
     * The string to search in.
     * @return bool
     * True if $needle was found, false otherwise.
     */
    public function endsWithIgnoreCase($string, $needle)
    {
        $hs = strtolower($string);

        foreach ((array)$needle as $ndl) {
            $n = strtolower($ndl);
            $length = strlen($ndl);
            if ($length === 0 || (substr($hs, -$length) === (string)$n)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the part of a string after the last occurrence of a given search value.
     *
     * ### after_last
     * Related global function (description see above).
     *
     * > #### [( jump back )](#available-php-functions)
     *
     * ```php
     * after_last( string $search, string $string ): string
     * ```
     *
     * #### Example
     * ```php
     * $path = "/var/www/html/public/img/image.jpg";
     *
     * after_last( '/' $path );
     *
     * // image.jpg
     * ```
     *
     * @param string $search
     * The string to search for.
     * @param string $string
     * The string to search in.
     * @return string
     * The found string after the last occurrence of the search string. Whitespaces at beginning will be removed.
     */
    public function afterLast($string, $search)
    {
        return $search === '' ? $string : ltrim(array_reverse(explode($search, $string))[0]);
    }

    /**
     * Convert empty to null
     * @param $string
     *
     * @return null
     */
    public function emptyToNull(&$string)
    {
        return empty($string) ? null : $string;
    }

    /**
     * Limits a phrase to a given number of words.
     *     $text = static::limit_words($text);.
     *
     * @param string $str      phrase to limit words of
     * @param int    $limit    number of words to limit to
     * @param string $end_char end character or entity
     *
     * @return string
     */
    public function limitWords($str, $limit = 100, $end_char = null)
    {
        $limit = (int)$limit;
        $end_char = (null === $end_char) ? '…' : $end_char;

        if ('' === \trim($str)) {
            return $str;
        }

        if ($limit <= 0) {
            return $end_char;
        }

        \preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $str, $matches);

        // Only attach the end character if the matched string is shorter
        // than the starting string.
        return \rtrim($matches[0]) . ((\strlen($matches[0]) === \strlen($str)) ? '' : $end_char);
    }

    /**
     * replace Line breaks from string
     * @param        $string
     * @param string $replaceWith
     *
     * @return mixed
     */
    public function replaceLineBreaks($string, $replaceWith = ' ')
    {
        return \preg_replace('/[\r\n]+/', $replaceWith, $string);
    }

    /**
     * Check if a string contains any encoded special chars
     *
     * @param string $text
     * @return bool
     */
    public function containsHtml(string $text): bool
    {
        return $text !== \htmlspecialchars_decode($text);
    }

    /**
     * Make both, first and last char of given string lowercase
     *
     * @param string $str
     * @return string
     */
    public function lowerFirstAndLast(string $str): string
    {
        $str = \strrev($str);
        $str = \lcfirst($str);
        $str = \strrev($str);

        return \lcfirst($str);
    }

     /**
     * Convert numbers to bytes string
     *
     */
    public function formatBytes(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base  = \log($size) / \log(1024);

        return \round(1024 ** ($base - \floor($base)), 1) . ' ' . $units[(int)\floor($base)];
    }

    /**
     * Generate alphabetical string from given character index:
     *
     * 0  = 'a', 1 = 'b', ...,
     * 25 = 'z'
     * 26 = 'aa' (when index > 25: use character of index mod 25,
     *            repeated as many times as there are modulo "wrap-arounds")
     *
     * @param  int $characterIndex
     * @return string
     */
    public function alpha(int $characterIndex): string
    {
        if($characterIndex <= 0){$characterIndex = 1;}
        $characterIndex -=1;
        $letters = \range('a', 'z');

        if ($characterIndex <= 25) {
            return (string)$letters[$characterIndex];
        }

        $dividend       = $characterIndex + 1;
        $alphaCharacter = '';

        while ($dividend > 0) {
            $modulo         = ($dividend - 1) % 26;
            $alphaCharacter = $letters[$modulo] . $alphaCharacter;
            $dividend       = \floor(($dividend - $modulo) / 26);
        }

        return $alphaCharacter;
    }

/**
 * replace First string only
 *
 */
  public function replaceFirst(string $subject, string $search, string $replace = ''): string
  {
      if ('' !== $search) {
          /** @noinspection ReturnFalseInspection */
          $offset = \strpos($subject, $search);

          if (false !== $offset) {
              return \substr_replace($subject, $replace, $offset, \strlen($search));
          }
      }

      return $subject;
  }

 /**
 * replace First string last
 *
 */
  public function replaceLast(string $subject, string $search, string $replace = ''): string
  {
      /** @noinspection ReturnFalseInspection */
      $offset = \strrpos($subject, $search);

      return false !== $offset
          ? \substr_replace($subject, $replace, $offset, \strlen($search))
          : $subject;
  }

    /**
     * Get part of string.
     *
     * @param string $string To get substring from.
     * @param int $start Character to start at.
     * @param int|null $length Number of characters to get.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @see https://php.net/manual/en/function.mb-substr.php
     *
     * @return string
     */
    public function substr(string $string, int $start, int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Counts words in a string.
     *
     * @param string $string
     * @return int
     */
    public function words($string)
    {
        return count(preg_split('/\s+/u', $string, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Get string length.
     *
     * @param string $string String to calculate length for.
     * @param string $encoding The encoding to use, defaults to "UTF-8".
     *
     * @see https://php.net/manual/en/function.mb-strlen.php
     *
     * @return int
     */
    public function length(string $string): int
    {
        return mb_strlen($string, 'UTF-8');
    }

    /**
     * Converts string to lowercase using a specified character encoding.
     *
     * See: https://www.php.net/manual/en/mbstring.supported-encodings.php
     *
     * @param string $string
     * @param string $encoding
     *
     * @return string
     */

    public function lowercase(string $string): string
    {
        return mb_convert_case($string, MB_CASE_LOWER, 'UTF-8');
    }

    /**
     * Converts string to uppercase using a specified character encoding.
     *
     * See: https://www.php.net/manual/en/mbstring.supported-encodings.php
     *
     * @param string $string
     * @param string $encoding
     *
     * @return string
     */

    public function uppercase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_convert_case($string, MB_CASE_UPPER, $encoding);
    }

    /**
     * Converts string to snake case, replacing any non-alpha
     * and non-numeric characters with an underscore.
     *
     * @param string $string
     * @param bool $lowercase (Convert string to lowercase)
     *
     * @return string
     */
    public function snakeCase(string $string, bool $lowercase = true): string
    {

        // Replace non letter or digit with underscore (_)
        $string = preg_replace('/[^a-z0-9]+/i', '_', $string);

        // Transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        // Trim
        $string = trim($string, '_');

        // Remove duplicate _
        $string = preg_replace('/_+/', '_', $string);

        if (true === $lowercase) {

            return $this->lowercase($string);

        }

        return $string;

    }

    /**
     * Converts string to title case using a specified character encoding.
     *
     * @param string $string
     * @param string $encoding
     *
     * @return string
     */

    public function titleCase(string $string, string $encoding = 'UTF-8'): string
    {
        return mb_convert_case($string, MB_CASE_TITLE, $encoding);
    }

    /**
     * Converts string to camel case, removing any non-alpha and non-numeric characters.
     *
     * @param string $string
     *
     * @return string
     */

    public function camelCase(string $string): string
    {

        // Non-alpha and non-numeric characters become spaces

        $string = preg_replace("/[^a-z0-9]+/i", " ", $string);

        $string = ucwords(strtolower(trim($string)));

        return lcfirst(str_replace(" ", "", $string));

    }

    /**
     * Converts string to kebab case (URL-friendly slug), replacing any non-alpha
     * and non-numeric characters with a hyphen.
     *
     * @param string $string
     * @param bool $lowercase (Convert string to lowercase)
     *
     * @return string
     */

    public function kebabCase(string $string, bool $lowercase = false): string
    {

        // Replace non letter or digit with hyphen (-)
        $string = preg_replace('/[^a-z0-9]+/i', '-', $string);

        // Transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        // Trim
        $string = trim($string, '-');

        // Remove duplicate -
        $string = preg_replace('/-+/', '-', $string);

        if (true === $lowercase) {

            return $this->lowercase($string);

        }

        return $string;

    }

    /**
     * Checks if a string starts with a given case-sensitive string.
     *
     * @param string $string
     * @param string $starts_with
     *
     * @return bool
     */

    public function startsWith(string $string, string $starts_with = ''): bool
    {
        return (substr($string, 0, strlen($starts_with)) === $starts_with);
    }

    /**
     * Checks if a string ends with a given case-sensitive string.
     *
     * @param string $string
     * @param string $ends_with
     *
     * @return bool
     */

    public function endsWith(string $string, string $ends_with = ''): bool
    {

        $length = strlen($ends_with);

        return $length === 0 || (substr($string, -$length) === $ends_with);

    }

    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd ...
     * @param int $number the number to get its ordinal value
     * @return string
     */
    public function ordinalize($number)
    {
        if (in_array($number % 100, range(11, 13))) {
            return $number . 'th';
        }
        switch ($number % 10) {
            case 1:
                return $number . 'st';
            case 2:
                return $number . 'nd';
            case 3:
                return $number . 'rd';
            default:
                return $number . 'th';
        }
    }

    /*
    * Make URL inside text clickable link
    *
    */
    public function toLink(string $plaintext, $attributes='')
    {
        // Find and replace link
        $str = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.~]*(\?\S+)?)?)*)@', '<a href="$1" '.$attributes.'>$1</a>', $plaintext);
        
        // Add "http://" if not set
        $str = preg_replace('/<a\s[^>]*href\s*=\s*"((?!https?:\/\/)[^"]*)"[^>]*>/i', '<a href="http://$1" '.$attributes.'>', $str);
        
        return $str;
    }

    /**
     * String to lower
     * 
     */
    public function lower(string $string)
    {
        return strtolower($string);
    }

    /**
     * String to upper
     * 
     */
    public function upper(string $string)
    {
        return strtoupper($string);
    }

}