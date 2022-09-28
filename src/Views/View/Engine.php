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
namespace Kiaan\Views\View;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use ArrayAccess;
use BadMethodCallException;
use Closure;
use Countable;
use Exception;
use InvalidArgumentException;

/*
|---------------------------------------------------
| Templating Engine
|---------------------------------------------------
*/
class Engine {

    /*
    * Traits
    * 
    */
    use CommentsTrait;
    use StatementsTrait;
    use FunctionsTrait;
    use InheritanceTrait;
    use ClassesTrait;

    /* Mods */    
    /** If has changed,then the file is replaced. */
    const MODE_AUTO = 0;
    /** It's useful for development. */
    const MODE_SLOW = 1;
    /** It's fast and it's useful for production. */
    const MODE_FAST = 2;
    /** The file is always compiled and the filename is identifiable. */
    const MODE_DEBUG = 5;

    /* Dictionary */
    public static $dictionary = [];

    /* PHP tag */
    public $phpTag = '<?php '; 

    /* PHP tag with echo */
    public $phpTagEcho = '<?php echo ';

    /* Escaped tag */
    public $escapedTag = '@';

    /** CSRF */
    public $csrf = ["input"=>"", "value"=>""];

    /* Method */
    public $method = "_method";

    /* Echo tags */
    /** @var array Array of opening and closing tags for raw echos. */
    protected $rawTags = ['{!!', '!!}']; // stored for historical purpose.
    /** @var array Array of opening and closing tags for regular echos. */
    protected $contentTags = ['{{', '}}'];
    /** @var array Array of opening and closing tags for escaped echos. */
    protected $escapedTags = ['{{{', '}}}'];
    /** @var string The "regular" / legacy echo string format. */
    protected $echoFormat = '\htmlentities(%s, ENT_QUOTES, \'UTF-8\', false)';
    protected $echoFormatOld = 'static::e(%s)';

    /*
    * File extension for the template files. 
    */
    protected $fileExtension = '.html';

    /* 
    * Roots path
    */
    protected $rootsPath = ["root" => "", "public" => "", "asset" => ""];

    /* Options */
    /** @var string The path to the missing translations log file. If empty then every missing key is not saved. */
    public $missingLog = '';
    public $pipeEnable = true;
    /** @var array Alias (with or without namespace) of the classes) */
    public $aliasClasses = [];
    /** @var array All of the registered extensions. */
    protected $extensions = [];
    /** @var array All of the finished, captured sections. */
    protected $sections = [];
    /** @var string The template currently being compiled. For example "folder.template" */
    protected $fileName;
    protected $currentView;
    protected $notFoundPath;
    /** @var array The stack of in-progress sections. */
    protected $sectionStack = [];
    /** @var array The stack of in-progress loops. */
    protected $loopsStack = [];
    /** @var array Dictionary of data */
    protected $data = [];
    /** @var array Dictionary of global data */
    protected $dataGlobal = [];
    /** @var array Dictionary of variables */
    protected $variables = [];
    /** @var null Dictionary of global variables */
    protected $variablesGlobal = [];
    /** @var array All of the available compiler functions. */
    protected $compilers = [
        'Extensions',
        'Statements',
        'Comments',
        'Echos',
    ];
    /** @var string|null it allows to sets the stack */
    protected $viewStack;
    /** @var array used by $this->composer() */
    protected $composerStack = [];
    /** @var array The stack of in-progress push sections. */
    protected $pushStack = [];
    /** @var array All of the finished, captured push sections. */
    protected $pushes = [];
    /** @var int The number of active rendering operations. */
    protected $renderCount = 0;
    /** @var string[] Get the template path for the compiled views. */
    protected $templatePath;
    /** @var string Get the compiled path for the compiled views. If null then it uses the default path */
    protected $compiledPath;
    /** @var string the extension of the compiled file. */
    protected $compileExtension = '.htmlfinal';
    /** @var array Custom "directive" dictionary. Those directives run at compile time. */
    protected $customDirectives = [];
    /** @var bool[] Custom directive dictionary. Those directives run at runtime. */
    protected $customDirectivesRT = [];
    /** @var array Used for conditional if. */
    protected $conditions = [];
    /** @var int Unique counter. It's used for extends */
    protected $uidCounter = 0;
    /** @var bool if true then it removes tabs and unneeded spaces */
    protected $optimize = true;
    /** @var bool if false, then the template is not compiled (but executed on memory). */
    protected $isCompiled = true;
    /** @var bool */
    protected $isRunFast = false;
    /** @var array Lines that will be added at the footer of the template */
    protected $footer = [];
    /** @var string Placeholder to temporary mark the position of escape blocks. */
    protected $escapePlaceholder = '$__escape__$';
    /** @var array Array to temporary store the escape blocks found in the template. */
    protected $escapeBlocks = [];
    /** @var int Counter to keep track of nested forelse statements. */
    protected $forelseCounter = 0;
    /** @var string tag unique */
    protected $PARENTKEY = '@parentXYZABC';
    /** mode */
    protected $mode;
    /** @var int Indicates the number of open switches */
    private $switchCount = 0;
    /** @var bool Indicates if the switch is recently open */
    private $firstCaseInSwitch = true;

    /**
     * Bob the constructor.
     * The folder at $compiledPath is created in case it doesn't exist.
     *
     * @param string|array $templatePath If null then it uses (caller_folder)/views
     * @param string $compiledPath If null then it uses (caller_folder)/compiles
     * @param int $mode =[TemplatingEngine::MODE_AUTO,TemplatingEngine::MODE_DEBUG,TemplatingEngine::MODE_FAST,TemplatingEngine::MODE_SLOW][$i]
     */
    public function __construct($templatePath = null, $compiledPath = null, $mode = 0)
    {
        if ($templatePath === null) {
            $templatePath = __DIR__ . '/Resources/Temp';
        }
        if ($compiledPath === null) {
            $compiledPath = __DIR__ . '/Resources/Temp';
        }
        
        $templatePath = $this->prepare_path($templatePath);
        $compiledPath = $this->prepare_path($compiledPath);

        $this->templatePath = (is_array($templatePath)) ? $templatePath : [$templatePath];
        $this->compiledPath = $compiledPath;

        $this->setMode($mode);
        
        if (!\file_exists($this->compiledPath)) {
            $ok = @\mkdir($this->compiledPath, 0777, true);
            if ($ok === false) {
                $this->showError(
                    'Constructing',
                    "Unable to create the compile folder [{$this->compiledPath}].",
                    true
                );
            }
        }
        // If the traits has "Constructors", then we call them.
        // Requisites.
        // 1- the method must be public or protected
        // 2- it must doesn't have arguments
        // 3- It must have the name of the trait. i.e. trait=MyTrait, method=MyTrait()
        $traits = get_declared_traits();
        if ($traits !== null) {
            foreach ($traits as $trait) {
                $r = explode('\\', $trait);
                $name = end($r);
                if (is_callable([$this, $name]) && method_exists($this, $name)) {
                    $this->{$name}();
                }
            }
        }
    }

    /**
     * Prepare path
     * 
    */
    protected function prepare_path($path)
    {
        return str_replace(['/', '//', '.'], '\\', $path);
    }
    
    /**
     * Show an error in the web.
     *
     * @param string $id Title of the error
     * @param string $text Message of the error
     * @param bool $critic if true then the compilation is ended, otherwise it continues
     * @return string
     */
    public function showError($id, $text, $critic = false)
    {
        \ob_get_clean();
        echo "<div style='background-color: red; color: black; padding: 3px; border: solid 1px black;'>";
        echo "Templating engine Error [{$id}]:<br>";
        echo "<span style='color:white'>$text</span><br></div>\n";
        if ($critic) {
            die(1);
        }
        return '';
    }

    /**
     * Escape HTML entities in a string.
     *
     * @param string $value
     * @return string
     */
    public static function e($value)
    {
        return (\is_array($value) || \is_object($value))
            ? \htmlentities(\print_r($value, true), ENT_QUOTES, 'UTF-8', false)
            : \htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    protected static function convertArgCallBack($k, $v)
    {
        return $k . "='{$v}' ";
    }

    /**
     * @param mixed|\DateTime $variable
     * @param string|null $format
     * @return string
     */
    public function format($variable, $format = null)
    {
        if ($variable instanceof \DateTime) {
            $format = $format === null ? 'Y/m/d' : $format;
            return $variable->format($format);
        }
        $format = $format === null ? '%s' : $format;
        return sprintf($format, $variable);
    }

    /**
     * It converts a text into a php code with echo<br>
     * <b>Example:</b><br>
     * <pre>
     * $this->wrapPHP('$hello'); // "< ?php echo $this->e($hello); ? >"
     * $this->wrapPHP('$hello',''); // < ?php echo $this->e($hello); ? >
     * $this->wrapPHP('$hello','',false); // < ?php echo $hello; ? >
     * $this->wrapPHP('"hello"'); // "< ?php echo $this->e("hello"); ? >"
     * $this->wrapPHP('hello()'); // "< ?php echo $this->e(hello()); ? >"
     * </pre>
     *
     * @param string $input The input value
     * @param string $quote The quote used (to quote the result)
     * @param bool $parse If the result will be parsed or not. If false then it's returned without $this->e
     * @return string
     */
    public function wrapPHP($input, $quote = '"', $parse = true)
    {
        if (strpos($input, '(') !== false && !$this->isQuoted($input)) {
            if ($parse) {
                return $quote . $this->phpTagEcho . '$this->e(' . $input . ');?>' . $quote;
            }

            return $quote . $this->phpTagEcho . $input . ';?>' . $quote;
        }
        if (strpos($input, '$') === false) {
            if ($parse) {
                return self::enq($input);
            }

            return $input;
        }
        if ($parse) {
            return $quote . $this->phpTagEcho . '$this->e(' . $input . ');?>' . $quote;
        }
        return $quote . $this->phpTagEcho . $input . ';?>' . $quote;
    }

    /**
     * Returns true if the text is surrounded by quotes (double or single quote)
     *
     * @param string|null $text
     * @return bool
     */
    public function isQuoted($text)
    {
        if (!$text || strlen($text) < 2) {
            return false;
        }
        if ($text[0] === '"' && substr($text, -1) === '"') {
            return true;
        }
        return ($text[0] === "'" && substr($text, -1) === "'");
    }

    /**
     * Escape HTML entities in a string.
     *
     * @param string $value
     * @return string
     */
    public static function enq($value)
    {
        if (\is_array($value) || \is_object($value)) {
            return \htmlentities(\print_r($value, true), ENT_NOQUOTES, 'UTF-8', false);
        }
        return \htmlentities($value, ENT_NOQUOTES, 'UTF-8', false);
    }

    /**
     * @param string $view example "folder.template"
     * @param string|null $alias example "mynewop". If null then it uses the name of the template.
     */
    public function addInclude($view, $alias = null)
    {
        if (!isset($alias)) {
            $alias = \explode('.', $view);
            $alias = \end($alias);
        }
        $this->directive($alias, function ($expression) use ($view) {
            $expression = $this->stripParentheses($expression) ?: '[]';
            return "{$this->phpTag} echo \$this->runChild('{$view}', {$expression}); ?>";
        });
    }

    /**
     * Register a handler for custom directives.
     *
     * @param string $name
     * @param callable $handler
     * @return void
     */
    public function directive($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
        $this->customDirectivesRT[$name] = false;
    }
  
    /**
     * Strip the parentheses from the given expression.
     *
     * @param string $expression
     * @return string
     */
    public function stripParentheses($expression)
    {
        if (static::startsWith($expression, '(')) {
            $expression = \substr($expression, 1, -1);
        }
        return $expression;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '') {
                if (\function_exists('mb_strpos')) {
                    if (\mb_strpos($haystack, $needle) === 0) {
                        return true;
                    }
                } elseif (\strpos($haystack, $needle) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * If false then the file is not compiled and it is executed directly from the memory.<br>
     * By default the value is true<br>
     * It also sets the mode to MODE_SLOW
     *
     * @param bool $bool
     * @return TemplatingEngine
     * @see \kiaan\TemplatingEngine\TemplatingEngine::setMode
     */
    public function setIsCompiled($bool = false)
    {
        $this->isCompiled = $bool;
        if (!$bool) {
            $this->setMode(self::MODE_SLOW);
        }
        return clone($this);
    }

    /**
     * It sets the template and compile path (without trailing slash).
     * <p>Example:setPath("somefolder","otherfolder");
     *
     * @param null|string|string[] $templatePath If null then it uses the current path /views folder
     * @param null|string $compiledPath If null then it uses the current path /views folder
     */
    public function setPath($templatePath=null, $compiledPath=null)
    {
        if ($templatePath === null) {
            $templatePath = __DIR__ . '/Resources/Temp';
        }
        if ($compiledPath === null) {
            $compiledPath = __DIR__ . '/Resources/Temp';
        }
        
        $templatePath = $this->prepare_path($templatePath);
        $compiledPath = $this->prepare_path($compiledPath);

        $this->templatePath = (is_array($templatePath)) ? $templatePath : [$templatePath];
        $this->compiledPath = $compiledPath;
    }

    /**
     * run the html engine. It returns the result of the code.
     *
     * @param string HTML to parse
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function runString($string, $data = [])
    {
        $php = $this->compileString($string);

        $obLevel = \ob_get_level();
        \ob_start();
        \extract($data, EXTR_SKIP);

        $previousError = \error_get_last();

        try {
            @eval('?' . '>' . $php);
        } catch (Exception $e) {
            while (\ob_get_level() > $obLevel) {
                \ob_end_clean();
            }
            throw $e;
        } catch (\ParseError $e) { // PHP 7
            while (\ob_get_level() > $obLevel) {
                \ob_end_clean();
            }
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $lastError = \error_get_last(); // PHP 5.6
        if ($previousError != $lastError && $lastError['type'] == E_PARSE) {
            while (\ob_get_level() > $obLevel) {
                \ob_end_clean();
            }
            throw new Exception($lastError['message'], $lastError['type']);
        }

        return \ob_get_clean();
    }

    /**
     * Compile the given html template contents.
     *
     * @param string $value
     * @return string
     */
    protected function compileString($value)
    {
        $result = '';
        if (\strpos($value, '@escape') !== false) {
            $value = $this->storeEscapeBlocks($value);
        }
        $this->footer = [];
        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (\token_get_all($value) as $token) {
            $result .= \is_array($token) ? $this->parseToken($token) : $token;
        }
        if (!empty($this->escapeBlocks)) {
            $result = $this->restoreEscapeBlocks($result);
        }
        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (\count($this->footer) > 0) {
            $result = \ltrim($result, PHP_EOL)
                . PHP_EOL . \implode(PHP_EOL, \array_reverse($this->footer));
        }
        return $result;
    }

    /**
     * Store the escape blocks and replace them with a temporary placeholder.
     *
     * @param string $value
     * @return string
     */
    protected function storeEscapeBlocks($value)
    {
        return \preg_replace_callback('/(?<!@)@escape(.*?)@endescape/s', function ($matches) {
            $this->escapeBlocks[] = $matches[1];
            return $this->escapePlaceholder;
        }, $value);
    }

    /**
     * Parse the tokens from the template.
     *
     * @param array $token
     *
     * @return string
     *
     * @see \kiaan\TemplatingEngine\TemplatingEngine::compileStatements
     * @see \kiaan\TemplatingEngine\TemplatingEngine::compileExtends
     * @see \kiaan\TemplatingEngine\TemplatingEngine::compileComments
     * @see \kiaan\TemplatingEngine\TemplatingEngine::compileEchos
     */
    protected function parseToken($token)
    {
        list($id, $content) = $token;
        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }
        return $content;
    }

    /**
     * Replace the raw placeholders with the original code stored in the raw blocks.
     *
     * @param string $result
     * @return string
     */
    protected function restoreEscapeBlocks($result)
    {
        $result = \preg_replace_callback('/' . \preg_quote($this->escapePlaceholder) . '/', function () {
            return \array_shift($this->escapeBlocks);
        }, $result);
        $this->escapeBlocks = [];
        return $result;
    }

    /**
     * Start injecting content into a push section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    public function startPush($section, $content = '')
    {
        if ($content === '') {
            if (\ob_start()) {
                $this->pushStack[] = $section;
            }
        } else {
            $this->extendPush($section, $content);
        }
    }

    /*
     * endswitch tag
     */

    /**
     * Append content to a given push section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    protected function extendPush($section, $content)
    {
        if (!isset($this->pushes[$section])) {
            $this->pushes[$section] = []; // start an empty section
        }
        if (!isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] .= $content;
        }
    }

    /**
     * Start injecting content into a push section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    public function startPrepend($section, $content = '')
    {
        if ($content === '') {
            if (\ob_start()) {
                \array_unshift($this->pushStack[], $section);
            }
        } else {
            $this->extendPush($section, $content);
        }
    }

    /**
     * Stop injecting content into a push section.
     *
     * @return string
     */
    public function stopPush()
    {
        if (empty($this->pushStack)) {
            $this->showError('stopPush', 'Cannot end a section without first starting one', true);
        }
        $last = \array_pop($this->pushStack);
        $this->extendPush($last, \ob_get_clean());
        return $last;
    }

    /**
     * Stop injecting content into a push section.
     *
     * @return string
     */
    public function stopPrepend()
    {
        if (empty($this->pushStack)) {
            $this->showError('stopPrepend', 'Cannot end a section without first starting one', true);
        }
        $last = \array_shift($this->pushStack);
        $this->extendStartPush($last, \ob_get_clean());
        return $last;
    }

    /**
     * Append content to a given push section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    protected function extendStartPush($section, $content)
    {
        if (!isset($this->pushes[$section])) {
            $this->pushes[$section] = []; // start an empty section
        }
        if (!isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] = $content . $this->pushes[$section][$this->renderCount];
        }
    }

    /**
     * Get the string contents of a push section.
     *
     * @param string $section
     * @param string $default
     * @return string
     */
    public function contentPushContent($section, $default = '')
    {
        if (!isset($this->pushes[$section])) {
            return $default;
        }
        return \implode(\array_reverse($this->pushes[$section]));
    }

    /**
     * Get the string contents of a push section.
     *
     * @param int|string $each if int, then it split the foreach every $each numbers.<br>
     *                         if string, "c3" it means that it will split in 3 columns<br>
     * @param string $splitText
     * @param string $splitEnd
     * @return string
     */
    public function splitForeach($each = 1, $splitText = ',', $splitEnd = '')
    {
        $loopStack = static::last($this->loopsStack); // array(7) { ["index"]=> int(0) ["remaining"]=> int(6) ["count"]=> int(5) ["first"]=> bool(true) ["last"]=> bool(false) ["depth"]=> int(1) ["parent"]=> NULL }
        if (($loopStack['index']) == $loopStack['count'] - 1) {
            return $splitEnd;
        }
        $eachN = 0;
        if (is_numeric($each)) {
            $eachN = $each;
        } elseif (strlen($each) > 1) {
            if ($each[0] === 'c') {
                $eachN = $loopStack['count'] / substr($each, 1);
            }
        } else {
            $eachN = PHP_INT_MAX;
        }

        if (($loopStack['index'] + 1) % $eachN === 0) {
            return $splitText;
        }
        return '';
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param array $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public static function last($array, callable $callback = null, $default = null)
    {
        if (\is_null($callback)) {
            return empty($array) ? static::value($default) : \end($array);
        }
        return static::first(\array_reverse($array), $callback, $default);
    }

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param array $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if (\is_null($callback)) {
            return empty($array) ? static::value($default) : \reset($array);
        }
        foreach ($array as $key => $value) {
            if ($callback($key, $value)) {
                return $value;
            }
        }
        return static::value($default);
    }

    /**
     * @param string $name
     * @param        $args []
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $args)
    {
        if ($name === 'if') {
            return $this->registerIfStatement(isset($args[0]) ? $args[0] : null, isset($args[1]) ? $args[1] : null);
        }
        throw new BadMethodCallException("function $name is not defined<br>");
    }

    /**
     * Register an "if" statement directive.
     *
     * @param string $name
     * @param callable $callback
     * @return string
     */
    public function registerIfStatement($name, callable $callback)
    {
        $this->conditions[$name] = $callback;

        $this->directive($name, function ($expression) use ($name) {
            $tmp = $this->stripParentheses($expression);
            return $expression !== ''
                ? $this->phpTag . " if (\$this->check('{$name}', {$tmp})): ?>"
                : $this->phpTag . " if (\$this->check('{$name}')): ?>";
        });

        $this->directive('else' . $name, function ($expression) use ($name) {
            $tmp = $this->stripParentheses($expression);
            return $expression !== ''
                ? $this->phpTag . " elseif (\$this->check('{$name}', {$tmp})): ?>"
                : $this->phpTag . " elseif (\$this->check('{$name}')): ?>";
        });

        $this->directive('end' . $name, function () {
            return $this->phpTag . ' endif; ?>';
        });
        return '';
    }

    /**
     * Check the result of a condition.
     *
     * @param string $name
     * @param array $parameters
     * @return bool
     */
    public function check($name, ...$parameters)
    {
        return \call_user_func($this->conditions[$name], ...$parameters);
    }

    /**
     * @param bool $bool
     * @param string $view name of the view
     * @param array $value arrays of values
     * @return string
     * @throws Exception
     */
    public function includeWhen($bool = false, $view = '', $value = [])
    {
        if ($bool) {
            return $this->runChild($view, $value);
        }
        return '';
    }

    /**
     * @param bool $bool
     * @param string $view name of the view
     * @param array $value arrays of values
     * @return string
     * @throws Exception
     */
    public function includeUnless($bool = false, $view = '', $value = [])
    {
        if (!$bool) {
            return $this->runChild($view, $value);
        }
        return '';
    }

    /**
     * Macro of function run
     *
     * @param       $view
     * @param array $variables
     * @return string
     * @throws Exception
     */
    public function runChild($view, $variables = [])
    {
        if (\is_array($variables)) {
            $newVariables = \array_merge($this->variables, $variables);
        } else {
            $this->showError('run/include', "Include/run variables should be defined as array ['idx'=>'value']", true);
            return '';
        }
        return $this->runInternal($view, $newVariables, false, false, $this->isRunFast);
    }

    /**
     * run the html engine. It returns the result of the code.
     *
     * @param string $view
     * @param array $variables
     * @param bool $forced if true then it recompiles no matter if the compiled file exists or not.
     * @param bool $isParent
     * @param bool $runFast if true then the code is not compiled neither checked and it runs directly the compiled
     *                       version.
     * @return string
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    private function runInternal($view, $variables = [], $forced = false, $isParent = true, $runFast = false)
    {
        $this->currentView = $view;
        if (@\count($this->composerStack)) {
            $this->evalComposer($view);
        }
        if (@\count($this->variablesGlobal) > 0) {
            $this->variables = \array_merge($variables, $this->variablesGlobal);
            $this->variablesGlobal = []; // used so we delete it.
        } else {
            $this->variables = $variables;
        }
        if (!$runFast) {
            // a) if the compile is forced then we compile the original file, then save the file.
            // b) if the compile is not forced then we read the datetime of both file and we compared.
            // c) in both cases, if the compiled doesn't exist then we compile.
            if ($view) {
                $this->fileName = $view;
            }
            $result = $this->compile($view, $forced);
            if (!$this->isCompiled) {
                return $this->evaluateText($result, $this->variables);
            }
        } elseif ($view) {
            $this->fileName = $view;
        }
        $this->isRunFast = $runFast;
        return $this->evaluatePath($this->getCompiledFile(), $this->variables);
    }

    protected function evalComposer($view)
    {
        foreach ($this->composerStack as $viewKey => $fn) {
            if ($this->wildCardComparison($view, $viewKey)) {
                if (is_callable($fn)) {
                    $fn($this);
                } elseif ($this->methodExistsStatic($fn, 'composer')) {
                    // if the method exists statically then $fn is the class and 'composer' is the name of the method
                    $fn::composer($this);
                } elseif (is_object($fn) || class_exists($fn)) {
                    // if $fn is an object or it is a class and the class exists.
                    $instance = (is_object($fn)) ? $fn : new $fn();
                    if (method_exists($instance, 'composer')) {
                        // and the method exists inside the instance.
                        $instance->composer($this);
                    } else {
                        if ($this->mode === self::MODE_DEBUG) {
                            throw new \RuntimeException('TemplatingEngine: composer() added an incorrect method [$fn]');
                        }
                        throw new \RuntimeException('TemplatingEngine: composer() added an incorrect method');
                    }
                } else {
                    throw new \RuntimeException('TemplatingEngine: composer() added an incorrect method');
                }
            }
        }
    }

    /**
     * It compares with wildcards (*) and returns true if both strings are equals<br>
     * The wildcards only works at the beginning and/or at the end of the string.<br>
     * <b>Example:<b><br>
     * <pre>
     * Text::wildCardComparison('abcdef','abc*'); // true
     * Text::wildCardComparison('abcdef','*def'); // true
     * Text::wildCardComparison('abcdef','*abc*'); // true
     * Text::wildCardComparison('abcdef','*cde*'); // true
     * Text::wildCardComparison('abcdef','*cde'); // false
     *
     * </pre>
     *
     * @param string $text
     * @param string|null $textWithWildcard
     *
     * @return bool
     */
    protected function wildCardComparison($text, $textWithWildcard)
    {
        if (($textWithWildcard === null && $textWithWildcard === '')
            || strpos($textWithWildcard, '*') === false) {
            // if the text with wildcard is null or empty or it contains two ** or it contains no * then..
            return $text == $textWithWildcard;
        }
        if ($textWithWildcard === '*' || $textWithWildcard === '**') {
            return true;
        }
        $c0 = $textWithWildcard[0];
        $c1 = substr($textWithWildcard, -1);
        $textWithWildcardClean = str_replace('*', '', $textWithWildcard);
        $p0 = strpos($text, $textWithWildcardClean);
        if ($p0 === false) {
            // no matches.
            return false;
        }
        if ($c0 === '*' && $c1 === '*') {
            // $textWithWildcard='*asasasas*'
            return true;
        }
        if ($c1 === '*') {
            // $textWithWildcard='asasasas*'
            return $p0 === 0;
        }
        // $textWithWildcard='*asasasas'
        $len = strlen($textWithWildcardClean);
        return (substr($text, -$len) === $textWithWildcardClean);
    }

    protected function methodExistsStatic($class, $method)
    {
        try {
            $mc = new \ReflectionMethod($class, $method);
            return $mc->isStatic();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Compile the view at the given path.
     *
     * @param string $templateName The name of the template. Example folder.template
     * @param bool $forced If the compilation will be forced (always compile) or not.
     * @return boolean|string True if the operation was correct, or false (if not exception)
     *                             if it fails. It returns a string (the content compiled) if isCompiled=false
     * @throws Exception
     */
    public function compile($templateName = null, $forced = false)
    {
        $compiled = $this->getCompiledFile($templateName);
        $template = $this->getTemplateFile($templateName);
        if (!$this->isCompiled) {
            return $this->compileString($this->getFile($template));
        }
        if ($forced || $this->isExpired($templateName)) {
            // compile the original file
            $contents = $this->compileString($this->getFile($template));
            $dir = \dirname($compiled);
            if (!\file_exists($dir)) {
                $ok = @\mkdir($dir, 0777, true);
                if ($ok === false) {
                    $this->showError(
                        'Compiling',
                        "Unable to create the compile folder [{$dir}]. Check the permissions of it's parent folder.",
                        true
                    );
                    return false;
                }
            }
            if ($this->optimize) {
                // removes space and tabs and replaces by a single space
                $contents = \preg_replace('/^ {2,}/m', ' ', $contents);
                $contents = \preg_replace('/^\t{2,}/m', ' ', $contents);
            }
            $ok = @\file_put_contents($compiled, $contents);
            if ($ok === false) {
                $this->showError(
                    'Compiling',
                    "Unable to save the file [{$compiled}]. Check the compile folder is defined and has the right permission"
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Get the full path of the compiled file.
     *
     * @param string $templateName
     * @return string
     */
    public function getCompiledFile($templateName = '')
    {
        $templateName = (empty($templateName)) ? $this->fileName : $templateName;
        if ($this->getMode() == self::MODE_DEBUG) {
            return $this->compiledPath . '/' . $templateName . $this->compileExtension;
        }

        return $this->compiledPath . '/' . \sha1($templateName) . $this->compileExtension;
    }

    /**
     * Get the mode of the engine.See TemplatingEngine::MODE_* constants
     *
     * @return int=[self::MODE_AUTO,self::MODE_DEBUG,self::MODE_FAST,self::MODE_SLOW][$i]
     */
    public function getMode()
    {
        if (\defined('TemplatingEngine_MODE')) {
            $this->mode = TemplatingEngine_MODE;
        }
        return $this->mode;
    }

    /**
     * Set the compile mode
     *
     * @param $mode int=[self::MODE_AUTO,self::MODE_DEBUG,self::MODE_FAST,self::MODE_SLOW][$i]
     * @return void
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Get the full path of the template file.
     * <p>Example: getTemplateFile('.abc.def')</p>
     *
     * @param string $templateName template name. If not template is set then it uses the base template.
     * @return string
     */
    public function getTemplateFile($templateName = '')
    {
        $templateName = (empty($templateName)) ? $this->fileName : $templateName;
        if (\strpos($templateName, '/') !== false) {
            return $this->locateTemplate($templateName); // it's a literal
        }
        $arr = \explode('.', $templateName);
        $c = \count($arr);
        if ($c == 1) {
            // its in the root of the template folder.
            return $this->locateTemplate($templateName . $this->fileExtension);
        }

        $file = $arr[$c - 1];
        \array_splice($arr, $c - 1, $c - 1); // delete the last element
        $path = \implode('/', $arr);
        return $this->locateTemplate($path . '/' . $file . $this->fileExtension);
    }

    /**
     * Find template file with the given name in all template paths in the order the paths were written
     *
     * @param string $name Filename of the template (without path)
     * @return string template file
     */
    private function locateTemplate($name)
    {
        $this->notFoundPath = '';
        foreach ($this->templatePath as $dir) {
            $path = $dir . '/' . $name;
            if (\file_exists($path)) {
                return $path;
            }

            $this->notFoundPath .= $path . ",";
        }
        return '';
    }

    /**
     * Get the contents of a file.
     *
     * @param string $fullFileName It gets the content of a filename or returns ''.
     *
     * @return string
     */
    public function getFile($fullFileName)
    {
        if (\is_file($fullFileName)) {
            return \file_get_contents($fullFileName);
        }
        $this->showError('getFile', "File does not exist at paths (separated by comma) [{$this->notFoundPath}] or permission denied", true);
        return '';
    }

    /**
     * Determine if the view has expired.
     *
     * @param string|null $fileName
     * @return bool
     */
    public function isExpired($fileName)
    {
        $compiled = $this->getCompiledFile($fileName);
        $template = $this->getTemplateFile($fileName);
        if (!\file_exists($template)) {
            if ($this->mode == self::MODE_DEBUG) {
                $this->showError('Read file', 'Template not found :' . $this->fileName . " on file: $template", true);
            } else {
                $this->showError('Read file', 'Template not found :' . $this->fileName, true);
            }
        }
        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!$this->compiledPath || !\file_exists($compiled)) {
            return true;
        }
        return \filemtime($compiled) < \filemtime($template);
    }

    /**
     * Evaluates a text (string) using the current variables
     *
     * @param string $content
     * @param array $variables
     * @return string
     * @throws Exception
     */
    protected function evaluateText($content, $variables)
    {
        \ob_start();
        \extract($variables);
        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            eval(' ?>' . $content . $this->phpTag);
        } catch (Exception $e) {
            $this->handleViewException($e);
        }
        return \ltrim(\ob_get_clean());
    }

    /**
     * Handle a view exception.
     *
     * @param Exception $e
     * @return void
     * @throws $e
     */
    protected function handleViewException($e)
    {
        \ob_get_clean();
        throw $e;
    }

    /**
     * Evaluates a compiled file using the current variables
     *
     * @param string $compiledFile full path of the compile file.
     * @param array $variables
     * @return string
     * @throws Exception
     */
    protected function evaluatePath($compiledFile, $variables)
    {
        \ob_start();
        // note, the variables are extracted locally inside this method,
        // they are not global variables :-3
        \extract($variables);
        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            /** @noinspection PhpIncludeInspection */
            include $compiledFile;
        } catch (Exception $e) {
            $this->handleViewException($e);
        }
        return \ltrim(\ob_get_clean());
    }

    /**
     * @param array $views array of views
     * @param array $value
     * @return string
     * @throws Exception
     */
    public function includeFirst($views = [], $value = [])
    {
        foreach ($views as $view) {
            if ($this->templateExist($view)) {
                return $this->runChild($view, $value);
            }
        }
        return '';
    }

    /**
     * Returns true if the template exists. Otherwise it returns false
     *
     * @param $templateName
     * @return bool
     */
    private function templateExist($templateName)
    {
        $file = $this->getTemplateFile($templateName);
        return \file_exists($file);
    }

    /**
     * Convert an array such as ["class1"=>"myclass","style="mystyle"] to class1='myclass' style='mystyle' string
     *
     * @param array|string $array array to convert
     * @return string
     */
    public function convertArg($array)
    {
        if (!\is_array($array)) {
            return $array;  // nothing to convert.
        }
        return \implode(' ', \array_map('static::convertArgCallBack', \array_keys($array), $array));
    }

    public function ipClient()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            && \preg_match('/^([d]{1,3}).([d]{1,3}).([d]{1,3}).([d]{1,3})$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * Stop injecting content into a section and return its contents.
     *
     * @return string
     */
    public function contentSection()
    {
        $sc = $this->stopSection();
        return isset($this->sections[$sc]) ? $this->sections[$sc] : null;
    }

    /**
     * Stop injecting content into a section.
     *
     * @param bool $overwrite
     * @return string
     */
    public function stopSection($overwrite = false)
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot end a section without first starting one.');
        }
        $last = \array_pop($this->sectionStack);
        if ($overwrite) {
            $this->sections[$last] = \ob_get_clean();
        } else {
            $this->extendSection($last, \ob_get_clean());
        }
        return $last;
    }

    /**
     * Append content to a given section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        if (isset($this->sections[$section])) {
            $content = \str_replace($this->PARENTKEY, $content, $this->sections[$section]);
            $this->sections[$section] = $content;
        } else {
            $this->sections[$section] = $content;
        }
    }

    /**
     * Start injecting content into a section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    public function startSection($section, $content = '')
    {
        if ($content === '') {
            \ob_start() && $this->sectionStack[] = $section;
        } else {
            $this->extendSection($section, $content);
        }
    }

    /**
     * Stop injecting content into a section and append it.
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function appendSection()
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot end a section without first starting one.');
        }
        $last = \array_pop($this->sectionStack);
        if (isset($this->sections[$last])) {
            $this->sections[$last] .= \ob_get_clean();
        } else {
            $this->sections[$last] = \ob_get_clean();
        }
        return $last;
    }

    /**
     * Get the string contents of a section.
     *
     * @param string $section
     * @param string $default
     * @return string
     */
    public function contentContent($section, $default = '')
    {
        if (isset($this->sections[$section])) {
            return \str_replace($this->PARENTKEY, $default, $this->sections[$section]);
        }

        return $default;
    }

    /**
     * Register a custom html compiler.
     *
     * @param callable $compiler
     * @return void
     */
    public function extend(callable $compiler)
    {
        $this->extensions[] = $compiler;
    }

    /**
     * Register a handler for custom directives for run at runtime
     *
     * @param string $name
     * @param callable $handler
     * @return void
     */
    public function directiveRT($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
        $this->customDirectivesRT[$name] = true;
    }

    /**
     * Sets the escaped content tags used for the compiler.
     *
     * @param string $openTag
     * @param string $closeTag
     * @return void
     */
    public function setEscapedContentTags($openTag, $closeTag)
    {
        $this->setContentTags($openTag, $closeTag, true);
    }

    /**
     * Gets the content tags used for the compiler.
     *
     * @return array
     */
    public function getContentTags()
    {
        return $this->getTags();
    }

    /**
     * Sets the content tags used for the compiler.
     *
     * @param string $openTag
     * @param string $closeTag
     * @param bool $escaped
     * @return void
     */
    public function setContentTags($openTag, $closeTag, $escaped = false)
    {
        $property = ($escaped === true) ? 'escapedTags' : 'contentTags';
        $this->{$property} = [\preg_quote($openTag), \preg_quote($closeTag)];
    }

    /**
     * Gets the tags used for the compiler.
     *
     * @param bool $escaped
     * @return array
     */
    protected function getTags($escaped = false)
    {
        $tags = $escaped ? $this->escapedTags : $this->contentTags;
        return \array_map('stripcslashes', $tags);
    }

    /**
     * Gets the escaped content tags used for the compiler.
     *
     * @return array
     */
    public function getEscapedContentTags()
    {
        return $this->getTags(true);
    }


    /**
     * Get the file extension for template files.
     *
     * @return string
     */
    public function getCompiledExtension()
    {
        return $this->compileExtension;
    }

    /**
     * Set the file extension for the compiled files.
     * Including the leading dot for the extension is required, e.g. .htmlc
     *
     * @param $fileExtension
     */
    public function setCompiledExtension($fileExtension)
    {
        $this->compileExtension = $fileExtension;
    }

    /**
     * Add new loop to the stack.
     *
     * @param array|Countable $data
     * @return void
     */
    public function addLoop($data)
    {
        $length = \is_array($data) || $data instanceof Countable ? \count($data) : null;
        $parent = static::last($this->loopsStack);
        $this->loopsStack[] = [
            'index' => -1,
            'iteration' => 0,
            'remaining' => isset($length) ? $length + 1 : null,
            'count' => $length,
            'first' => true,
            'even' => true,
            'odd' => false,
            'last' => isset($length) ? $length == 1 : null,
            'depth' => \count($this->loopsStack) + 1,
            'parent' => $parent ? (object)$parent : null,
        ];
    }

    /**
     * Increment the top loop's indices.
     *
     * @return object
     */
    public function incrementLoopIndices()
    {
        $c = \count($this->loopsStack) - 1;
        $loop = &$this->loopsStack[$c];

        $loop['index']++;
        $loop['iteration']++;
        $loop['first'] = $loop['index'] == 0;
        $loop['even'] = $loop['index'] % 2 == 0;
        $loop['odd'] = !$loop['even'];
        if (isset($loop['count'])) {
            $loop['remaining']--;
            $loop['last'] = $loop['index'] == $loop['count'] - 1;
        }
        return (object)$loop;
    }

    /**
     * Pop a loop from the top of the loop stack.
     *
     * @return void
     */
    public function popLoop()
    {
        \array_pop($this->loopsStack);
    }

    /**
     * Get an instance of the first loop in the stack.
     *
     * @return array|object
     */
    public function getFirstLoop()
    {
        return ($last = static::last($this->loopsStack)) ? (object)$last : null;
    }

    /**
     * Get the rendered contents of a partial from a loop.
     *
     * @param string $view
     * @param array $data
     * @param string $iterator
     * @param string $empty
     * @return string
     * @throws Exception
     */
    public function renderEach($view, $data, $iterator, $empty = 'raw|')
    {
        $result = '';

        if (\count($data) > 0) {
            // If is actually data in the array, we will loop through the data and append
            // an instance of the partial view to the final result HTML passing in the
            // iterated value of this data array, allowing the views to access them.
            foreach ($data as $key => $value) {
                $data = ['key' => $key, $iterator => $value];
                $result .= $this->runChild($view, $data);
            }
        } elseif (static::startsWith($empty, 'raw|')) {
            $result = \substr($empty, 4);
        } else {
            $result = $this->run($empty, []);
        }
        return $result;
    }

    /**
     * Run the html engine. It returns the result of the code.
     *
     * @param string|null $view The name of the cache. Ex: "folder.folder.view" ("/folder/folder/view.html")
     * @param array $variables An associative arrays with the values to display.
     * @return string
     * @throws Exception
     */
    public function run($view = null, $variables = [])
    {
        $mode = $this->getMode();

        if ($view === null) {
            $view = $this->viewStack;
        }
        $this->viewStack = null;
        if ($view === null) {
            throw new \RuntimeException('TemplatingEngine: view not set');
        }

        $forced = $mode & 1; // mode=1 forced:it recompiles no matter if the compiled file exists or not.
        $runFast = $mode & 2; // mode=2 runfast: the code is not compiled neither checked and it runs directly the compiled
        $this->sections = [];
        if ($mode == 3) {
            $this->showError('run', "we can't force and run fast at the same time", true);
        }
        return $this->runInternal($view, $variables, $forced, true, $runFast);
    }

    /**
     * It sets the current view<br>
     * This value is cleared when it is used (method run).<br>
     * <b>Example:<b><br>
     * <pre>
     * $this->setView('folder.view')->share(['var1'=>20])->run(); // or $this->run('folder.view',['var1'=>20]);
     * </pre>
     *
     * @param string $view
     * @return TemplatingEngine
     */
    public function setView($view)
    {
        $this->viewStack = $view;
        return clone($this);
    }

    /**
     * It injects a function, an instance, or a method class when a view is called.<br>
     * It could be stacked.   If it sets null then it clears all definitions.
     * <b>Example:<b><br>
     * <pre>
     * $this->composer('folder.view',function($TemplatingEngine) { $TemplatingEngine->share('newvalue','hi there'); });
     * $this->composer('folder.view','namespace1\namespace2\SomeClass'); // SomeClass must exists and it must has the
     *                                                                   // method 'composer'
     * $this->composer('folder.*',$instance); // $instance must has the method called 'composer'
     * $this->composer(); // clear all composer.
     * </pre>
     *
     * @param string|array|null $view It could contains wildcards (*). Example: 'aa.bb.cc','*.bb.cc','aa.bb.*','*.bb.*'
     *
     * @param callable|string|null $functionOrClass
     * @return TemplatingEngine
     */
    public function composer($view = null, $functionOrClass = null)
    {
        if ($view === null && $functionOrClass === null) {
            $this->composerStack = [];
            return clone($this);
        }
        if (is_array($view)) {
            foreach ($view as $v) {
                $this->composerStack[$v] = $functionOrClass;
            }
        } else {
            $this->composerStack[$view] = $functionOrClass;
        }

        return clone($this);
    }

    /**
     * @return string
     */
    public function getPhpTag()
    {
        return $this->phpTag;
    }

    /**
     * @param string $phpTag
     */
    public function setPhpTag($phpTag)
    {
        $this->phpTag = $phpTag;
    }

    /**
     * If true then it optimizes the result (it removes tab and extra spaces).
     *
     * @param bool $bool
     * @return TemplatingEngine
     */
    public function setOptimize($bool = false)
    {
        $this->optimize = $bool;
        return clone($this);
    }

    /**
     * Get the entire loop stack.
     *
     * @return array
     */
    public function getLoopStack()
    {
        return $this->loopsStack;
    }

    /**
     * It adds a string inside a quoted string<br>
     * <b>example:</b><br>
     * <pre>
     * $this->addInsideQuote("'hello'"," world"); // 'hello world'
     * $this->addInsideQuote("hello"," world"); // hello world
     * </pre>
     *
     * @param $quoted
     * @param $newFragment
     * @return string
     */
    public function addInsideQuote($quoted, $newFragment)
    {
        if ($this->isQuoted($quoted)) {
            return substr($quoted, 0, -1) . $newFragment . substr($quoted, -1);
        }
        return $quoted . $newFragment;
    }

    /**
     * Return true if the string is a php variable (it starts with $)
     *
     * @param string|null $text
     * @return bool
     */
    public function isVariablePHP($text)
    {
        if (!$text || strlen($text) < 2) {
            return false;
        }
        return $text[0] === '$';
    }

    /**
     * Its the same than @_e, however it parses the text (using sprintf).
     * If the operation fails then, it returns the original expression without translation.
     *
     * @param $phrase
     *
     * @return string
     */
    public function _ef($phrase)
    {
        $argv = \func_get_args();
        $r = $this->_e($phrase);
        $argv[0] = $r; // replace the first argument with the translation.
        $result = @sprintf(...$argv);
        $result = ($result === false) ? $r : $result;
        return $result;
    }

    /**
     * Tries to translate the word if its in the array defined by TemplatingEngineLang::$dictionary
     * If the operation fails then, it returns the original expression without translation.
     *
     * @param $phrase
     *
     * @return string
     */
    public function _e($phrase)
    {
        if ((!\array_key_exists($phrase, static::$dictionary))) {
            $this->missingTranslation($phrase);
            return $phrase;
        }

        return static::$dictionary[$phrase];
    }

    /**
     * Log a missing translation into the file $this->missingLog.<br>
     * If the file is not defined, then it doesn't write the log.
     *
     * @param string $txt Message to write on.
     */
    private function missingTranslation($txt)
    {
        if (!$this->missingLog) {
            return; // if there is not a file assigned then it skips saving.
        }
        $fz = @\filesize($this->missingLog);
        if (\is_object($txt) || \is_array($txt)) {
            $txt = \print_r($txt, true);
        }
        // Rewrite file if more than 100000 bytes
        $mode = ($fz > 100000) ? 'w' : 'a';
        $fp = \fopen($this->missingLog, $mode);
        \fwrite($fp, $txt . "\n");
        \fclose($fp);
    }

    /**
     * if num is more than one then it returns the phrase in plural, otherwise the phrase in singular.
     * Note: the translation should be as follow: $msg['Person']='Person' $msg=['Person']['p']='People'
     *
     * @param string $phrase
     * @param string $phrases
     * @param int $num
     *
     * @return string
     */
    public function _n($phrase, $phrases, $num = 0)
    {
        if ((!\array_key_exists($phrase, static::$dictionary))) {
            $this->missingTranslation($phrase);
            return ($num <= 1) ? $phrase : $phrases;
        }

        return ($num <= 1) ? $this->_e($phrase) : $this->_e($phrases);
    }

    /**
     * Remove first and end quote from a quoted string of text
     *
     * @param mixed $text
     * @return null|string|string[]
     */
    public function stripQuotes($text)
    {
        if (!$text || strlen($text) < 2) {
            return $text;
        }
        $text=trim($text);
        $p0=$text[0];
        $p1=\substr($text, -1);
        if ($p0===$p1 && ($p0==='"' || $p0==="'")) {
            return \substr($text, 1, -1);
        }
        return $text;
    }

    /**
     * Execute the user defined extensions.
     *
     * @param string $value
     * @return string
     */
    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            $value = $compiler($value, $this);
        }
        return $value;
    }

    /**
     * Compile html echos into valid PHP.
     *
     * @param string $value
     * @return string
     */
    protected function compileEchos($value)
    {
        foreach ($this->getEchoMethods() as $method => $length) {
            $value = $this->$method($value);
        }
        return $value;
    }

    /**
     * Get the echo methods in the proper order for compilation.
     *
     * @return array
     */
    protected function getEchoMethods()
    {
        $methods = [
            'compileRawEchos' => \strlen(\stripcslashes($this->rawTags[0])),
            'compileEscapedEchos' => \strlen(\stripcslashes($this->escapedTags[0])),
            'compileRegularEchos' => \strlen(\stripcslashes($this->contentTags[0])),
        ];
        \uksort($methods, static function ($method1, $method2) use ($methods) {
            // Ensure the longest tags are processed first
            if ($methods[$method1] > $methods[$method2]) {
                return -1;
            }
            if ($methods[$method1] < $methods[$method2]) {
                return 1;
            }
            // Otherwise give preference to raw tags (assuming they've overridden)
            if ($method1 === 'compileRawEchos') {
                return -1;
            }
            if ($method2 === 'compileRawEchos') {
                return 1;
            }
            if ($method1 === 'compileEscapedEchos') {
                return -1;
            }
            if ($method2 === 'compileEscapedEchos') {
                return 1;
            }
            throw new BadMethodCallException('Method not defined');
        });
        return $methods;
    }
    
    /**
     * Compile html statements that start with "@".
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function compileStatements($value)
    {
        /**
         * @param array $match
         *                    [0]=full expression with @ and parenthesis
         *                    [1]=expression without @ and argument
         *                    [2]=????
         *                    [3]=argument with parenthesis and without the first @
         *                    [4]=argument without parenthesis.
         *
         * @return mixed|string
         */
        $callback = function ($match) {
            if (static::contains($match[1], $this->escapedTag)) {
                // @@escaped tag
                $match[0] = isset($match[3]) ? $match[1] . $match[3] : $match[1];
            } else {
                if (strpos($match[1], '::') !== false) {
                    // Someclass::method
                    return $this->compileStatementClass($match);
                }
                if (isset($this->customDirectivesRT[$match[1]])) {
                    if ($this->customDirectivesRT[$match[1]] == true) {
                        $match[0] = $this->compileStatementCustom($match);
                    } else {
                        $match[0] =  \call_user_func(
                            $this->customDirectives[$match[1]],
                            $this->stripParentheses(static::get($match, 3))
                        );
                    }
                } elseif (\method_exists($this, $method = 'compile' . \ucfirst($match[1]))) {
                    // it calls the function compile<name of the tag>
                    $match[0] = $this->$method(static::get($match, 3));
                } else {
                    //todo: $this->showError("@compile", "Operation not defined:@".$match[1], true);
                    return $match[0];
                }
            }
            return isset($match[3]) ? $match[0] : $match[0] . $match[2];
        };
        /* return \preg_replace_callback('/\B@(@?\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value); */
        return preg_replace_callback('/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value);
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '') {
                if (\function_exists('mb_strpos')) {
                    if (\mb_strpos($haystack, $needle) !== false) {
                        return true;
                    }
                } elseif (\strpos($haystack, $needle) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function compileStatementClass($match)
    {
        if (isset($match[3])) {
            return $this->phpTagEcho . $this->fixNamespaceClass($match[1]) . $match[3] . '; ?>';
        }

        return $this->phpTagEcho . $this->fixNamespaceClass($match[1]) . '(); ?>';
    }

    /**
     * Util method to fix namespace of a class<br>
     * Example: "SomeClass::method()" -> "\namespace\SomeClass::method()"<br>
     *
     * @param string $text
     *
     * @return string
     * @see \kiaan\TemplatingEngine\TemplatingEngine::$aliasClasses
     */
    private function fixNamespaceClass($text)
    {
        if (strpos($text, '::') === false) {
            return $text;
        }
        $classPart = explode('::', $text, 2);
        if (isset($this->aliasClasses[$classPart[0]])) {
            $classPart[0] = $this->aliasClasses[$classPart[0]];
        }
        return $classPart[0] . '::' . $classPart[1];
    }

    /**
     * For compile custom directive at runtime.
     *
     * @param $match
     * @return string
     */
    protected function compileStatementCustom($match)
    {
        $v = $this->stripParentheses(static::get($match, 3));
        $v = ($v == '') ? '' : ',' . $v;
        return $this->phpTag . 'echo call_user_func($this->customDirectives[\'' . $match[1] . '\']' . $v . '); ?>';
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param ArrayAccess|array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        $accesible = \is_array($array) || $array instanceof ArrayAccess;
        if (!$accesible) {
            return static::value($default);
        }
        if (\is_null($key)) {
            return $array;
        }
        if (static::existsView($array, $key)) {
            return $array[$key];
        }
        foreach (\explode('.', $key) as $segment) {
            if (static::existsView($array, $segment)) {
                $array = $array[$segment];
            } else {
                return static::value($default);
            }
        }
        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param ArrayAccess|array $array
     * @param string|int $key
     * @return bool
     */
    public static function existsView($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return \array_key_exists($key, $array);
    }

    /**
     * This method removes the parenthesis of the expression and parse the arguments.
     * @param string $expression
     * @return array
     */
    protected function getArgs($expression)
    {
        return $this->parseArgs($this->stripParentheses($expression), ' ');
    }

    /**
     * It separates a string using a separator and excluding quotes and double quotes.
     *
     * @param string $text
     * @param string $separator
     * @return array
     */
    public function parseArgs($text, $separator = ',')
    {
        if ($text === null || $text === '') {
            return []; //nothing to convert.
        }
        $chars = str_split($text);
        $parts = [];
        $nextpart = '';
        $strL = count($chars);
        /** @noinspection ForeachInvariantsInspection */
        for ($i = 0; $i < $strL; $i++) {
            $char = $chars[$i];
            if ($char === '"' || $char === "'") {
                $inext = strpos($text, $char, $i + 1);
                $inext = $inext === false ? $strL : $inext;
                $nextpart .= substr($text, $i, $inext - $i + 1);
                $i = $inext;
            } else {
                $nextpart .= $char;
            }
            if ($char === $separator) {
                $parts[] = substr($nextpart, 0, -1);
                $nextpart = '';
            }
        }
        if ($nextpart !== '') {
            $parts[] = $nextpart;
        }
        $result = [];
        foreach ($parts as $part) {
            $r = explode('=', $part, 2);
            $result[trim($r[0])] = count($r) === 2 ? trim($r[1]) : null;
        }
        return $result;
    }

    /**
     * Compile the "raw" echo statements.
     *
     * @param string $value
     * @return string
     */
    protected function compileRawEchos($value)
    {
        $pattern = \sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->rawTags[0], $this->rawTags[1]);
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3] . $matches[3];
            return $matches[1] ? \substr(
                $matches[0],
                1
            ) : $this->phpTagEcho . $this->compileEchoDefaults($matches[2]) . '; ?>' . $whitespace;
        };
        return \preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the default values for the echo statement.
     *
     * @param string $value
     * @return string
     */
    protected function compileEchoDefaults($value)
    {
        $result = \preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $value);
        if (!$this->pipeEnable) {
            return $this->fixNamespaceClass($result);
        }
        return $this->pipeDream($this->fixNamespaceClass($result));
    }

    /**
     * It converts a string separated by pipes | into an filtered expression.<br>
     * If the method exists (as directive), then it is used<br>
     * If the method exists (in this class) then it is used<br>
     * Otherwise, it uses a global function.<br>
     * If you want to escape the "|", then you could use "/|"<br>
     * <b>Note:</b> It only works if $this->pipeEnable=true and by default it is false<br>
     * <b>Example:</b><br>
     * <pre>
     * $this->pipeDream('$name | strtolower | substr:0,4'); // strtolower(substr($name ,0,4)
     * $this->pipeDream('$name| getMode') // $this->getMode($name)
     * </pre>
     *
     * @param string $result
     * @return string
     * @\kiaan\TemplatingEngine\TemplatingEngine::$pipeEnable
     */
    protected function pipeDream($result)
    {
        $array = preg_split('~\\\\.(*SKIP)(*FAIL)|\|~s', $result);
        $c = count($array) - 1; // base zero.
        if ($c === 0) {
            return $result;
        }

        $prev = '';
        for ($i = $c; $i >= 1; $i--) {
            $r = @explode(':', $array[$i], 2);
            $fnName = trim($r[0]);
            $fnNameF=$fnName[0]; // first character
            if ($fnNameF === '"' || $fnNameF === '\'' || $fnNameF === '$' || is_numeric($fnNameF)) {
                $fnName='!isset('.$array[0].') ? '.$fnName.' : ';
            } else {
                if (isset($this->customDirectives[$fnName])) {
                    $fnName = '$this->customDirectives[\'' . $fnName . '\']';
                } elseif (method_exists($this, $fnName)) {
                    $fnName = '$this->' . $fnName;
                }
            }
            if ($i === 1) {
                $prev = $fnName . '(' . $array[0];
                if (count($r) === 2) {
                    $prev .= ',' . $r[1];
                }
                $prev .= ')';
            } else {
                $prev = $fnName . '(' . $prev;
                if (count($r) === 2) {
                    if ($i === 2) {
                        $prev .= ',';
                    }
                    $prev .= $r[1] . ')';
                }
            }
        }
        return $prev;
    }

    /**
     * Compile the "regular" echo statements. {{ }}
     *
     * @param string $value
     * @return string
     */
    protected function compileRegularEchos($value)
    {
        $pattern = \sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3] . $matches[3];
            $wrapped = \sprintf($this->echoFormat, $this->compileEchoDefaults($matches[2]));
            return $matches[1] ? \substr($matches[0], 1) : $this->phpTagEcho . $wrapped . '; ?>' . $whitespace;
        };
        return \preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the escaped echo statements. {!! !!}
     *
     * @param string $value
     * @return string
     */
    protected function compileEscapedEchos($value)
    {
        $pattern = \sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3] . $matches[3];

            return $matches[1] ? $matches[0] : $this->phpTag
                . \sprintf($this->echoFormat, $this->compileEchoDefaults($matches[2])) . '; ?>'
                . $whitespace;
            //return $matches[1] ? $matches[0] : $this->phpTag
            // . 'echo static::e(' . $this->compileEchoDefaults($matches[2]) . '); ? >' . $whitespace;
        };
        return \preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the each statements into valid PHP.
     *
     * @param string $expression
     * @return string
     */
    protected function compileEach($expression)
    {
        return $this->phpTagEcho . "\$this->renderEach{$expression}; ?>";
    }

    protected function compileSet($expression)
    {
        $segments = \explode('=', \preg_replace("/[()\\\']/", '', $expression));
        $value = (\count($segments) >= 2) ? ' =@' . $segments[1] : '++';
        return $this->phpTag . \trim($segments[0]) . $value . '; ?>';
    }

    /**
     * Compile the {@}compilestamp statement.
     *
     * @param string $expression
     *
     * @return false|string
     */
    protected function compileCompileStamp($expression)
    {
        $expression = $this->stripQuotes($this->stripParentheses($expression));
        $expression = ($expression === '') ? 'Y-m-d H:i:s' : $expression;
        return date($expression);
    }

    /**
     * compile the {@}viewname statement<br>
     * {@}viewname('compiled') returns the full compiled path
     * {@}viewname('template') returns the full template path
     * {@}viewname('') returns the view name.
     *
     * @param mixed $expression
     *
     * @return string
     */
    protected function compileViewName($expression)
    {
        $expression = $this->stripQuotes($this->stripParentheses($expression));
        switch ($expression) {
            case 'compiled':
                return $this->getCompiledFile($this->fileName);
            case 'template':
                return $this->getTemplateFile($this->fileName);
            default:
                return $this->fileName;
        }
    }

    /**
     * Used for @_e directive.
     *
     * @param $expression
     *
     * @return string
     */
    protected function compile_e($expression)
    {
        return $this->phpTagEcho . "\$this->_e{$expression}; ?>";
    }

    /**
     * Used for @_ef directive.
     *
     * @param $expression
     *
     * @return string
     */
    protected function compile_ef($expression)
    {
        return $this->phpTagEcho . "\$this->_ef{$expression}; ?>";
    }

    /**
     * Used for @_n directive.
     *
     * @param $expression
     *
     * @return string
     */
    protected function compile_n($expression)
    {
        return $this->phpTagEcho . "\$this->_n{$expression}; ?>";
    }

    /**
     * Compile a split of a foreach cycle. Used for example when we want to separate limites each "n" elements.
     *
     * @param string $expression
     * @return string
     */
    protected function compileSplitForeach($expression)
    {
        return $this->phpTagEcho . '$this::splitForeach' . $expression . '; ?>';
    }

}