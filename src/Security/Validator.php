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
namespace Kiaan\Security;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Security\Validator\Validation;
use Kiaan\Security\Validator\Rule;

/*
|---------------------------------------------------
| Validator
|---------------------------------------------------
*/
class Validator
{
    /*
    * Traits
    *
    */
    use Validator\Traits\MessagesTrait;
    use Validator\Traits\PdoTrait;

    /** @var array */
    protected $validators = [];

    /** @var bool */
    protected $allowRuleOverride = false;

    /** @var bool */
    protected $useHumanizedKeys = true;

    /**
     * Namespace
     * 
     **/
    protected $namespace;

    /**
     * Path
     * 
     **/
    protected $path;


    /**
     * Run
     *
     */
    public function run()
    {
        $this->registerValidators();
    }

    /**
     * Register or override existing validator
     *
     * @param mixed $key
     * @param \use Kiaan\Security\Rule $rule
     * @return void
     */
    public function setValidator(string $key, $rule)
    {
        $this->validators[$key] = $rule;
        $rule->setKey($key);
    }

    /**
     * Get validator object from given $key
     *
     * @param mixed $key
     * @return mixed
     */
    public function getValidator($key)
    {
        return isset($this->validators[$key]) ? $this->validators[$key] : null;
    }

    /**
     * Set namespace
     *
     */
    public function setNamespace($namespace)
    {
        return $this->namespace = $namespace;
    }

    /**
     * Get namespace
     *
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set path
     *
     */
    public function setPath($path)
    {
        return $this->path = $path;
    }

    /**
     * Get path
     *
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Validate $inputs
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function validate(array $inputs, array $rules, array $messages = []): Validation
    {
        $validation = $this->make($inputs, $rules, $messages);
        $validation->validate();
        return $validation;
    }

    /**
     * Given $inputs, $rules and $messages to make the Validation class instance
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function make(array $inputs, array $rules, array $messages = []): Validation
    {
        $messages = array_merge($this->messages, $messages);
        $validation = new Validation($this, $inputs, $rules, $messages);

        return $validation;
    }

    /**
     * Magic invoke method to make Rule instance
     *
     * @param string $rule
     * @return Rule
     */
    public function __invoke(string $rule): Validator\Rule
    {
        $args = func_get_args();
        $rule = array_shift($args);
        $params = $args;
        $validator = $this->getValidator($rule);
        if (!$validator) {
            throw new \Exception("Validator '{$rule}' is not registered", 1);
        }

        $clonedValidator = clone $validator;
        $clonedValidator->fillParameters($params);

        return $clonedValidator;
    }

    /**
     * Initialize validators array
     *
     * @return void
     */
    protected function registerValidators()
    {
        $baseValidator = [
            'required'                  => new Validator\Rules\Required,
            'required_if'               => new Validator\Rules\RequiredIf,
            'required_unless'           => new Validator\Rules\RequiredUnless,
            'required_with'             => new Validator\Rules\RequiredWith,
            'required_without'          => new Validator\Rules\RequiredWithout,
            'required_with_all'         => new Validator\Rules\RequiredWithAll,
            'required_without_all'      => new Validator\Rules\RequiredWithoutAll,
            'email'                     => new Validator\Rules\Email,
            'alpha'                     => new Validator\Rules\Alpha,
            'numeric'                   => new Validator\Rules\Numeric,
            'alpha_num'                 => new Validator\Rules\AlphaNum,
            'alpha_dash'                => new Validator\Rules\AlphaDash,
            'alpha_spaces'              => new Validator\Rules\AlphaSpaces,
            'in'                        => new Validator\Rules\In,
            'not_in'                    => new Validator\Rules\NotIn,
            'min'                       => new Validator\Rules\Min,
            'max'                       => new Validator\Rules\Max,
            'between'                   => new Validator\Rules\Between,
            'url'                       => new Validator\Rules\Url,
            'integer'                   => new Validator\Rules\Integer,
            'boolean'                   => new Validator\Rules\Boolean,
            'ip'                        => new Validator\Rules\Ip,
            'ipv4'                      => new Validator\Rules\Ipv4,
            'ipv6'                      => new Validator\Rules\Ipv6,
            'extension'                 => new Validator\Rules\Extension,
            'array'                     => new Validator\Rules\TypeArray,
            'same'                      => new Validator\Rules\Same,
            'regex'                     => new Validator\Rules\Regex,
            'date'                      => new Validator\Rules\Date,
            'accepted'                  => new Validator\Rules\Accepted,
            'present'                   => new Validator\Rules\Present,
            'different'                 => new Validator\Rules\Different,
            'uploaded_file'             => new Validator\Rules\UploadedFile,
            'mimes'                     => new Validator\Rules\Mimes,
            'callback'                  => new Validator\Rules\Callback,
            'before'                    => new Validator\Rules\Before,
            'after'                     => new Validator\Rules\After,
            'lowercase'                 => new Validator\Rules\Lowercase,
            'uppercase'                 => new Validator\Rules\Uppercase,
            'json'                      => new Validator\Rules\Json,
            'digits'                    => new Validator\Rules\Digits,
            'digits_between'            => new Validator\Rules\DigitsBetween,
            'defaults'                  => new Validator\Rules\Defaults,
            'default'                   => new Validator\Rules\Defaults, // alias of defaults
            'nullable'                  => new Validator\Rules\Nullable,
            'string'                    => new Validator\Rules\Str,
            'sometimes'                 => new Validator\Rules\Sometimes,
            'unique'                    => new Validator\Rules\Unique($this->getPdo()),
            'exists'                    => new Validator\Rules\Exists($this->getPdo())
        ];

        foreach ($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

    /**
     * Add custome rules
     *
     * @return void
     */
    public function rules(array $rules)
    {
        foreach ($rules as $key => $validator) {
            if(!is_object($validator)){
                if(!class_exists($validator)){
                    $validator = $this->getNamespace()."\\$validator";
                }

                $validator = new $validator;
            }

            $this->setValidator($key, $validator);
        }
    }

    /**
     * Given $ruleName and $rule to add new validator
     *
     * @param string $ruleName
     * @param \use Kiaan\Security\Rule $rule
     * @return void
     */
    public function addValidator(string $ruleName, $rule)
    {
        if (!$this->allowRuleOverride && array_key_exists($ruleName, $this->validators)) {
            throw new \Exception("You cannot override a built in rule. You have to rename your rule");
        }

        $this->setValidator($ruleName, $rule);
    }

    /**
     * Set rule can allow to be overrided
     *
     * @param boolean $status
     * @return void
     */
    public function allowRuleOverride(bool $status = false)
    {
        $this->allowRuleOverride = $status;
    }

    /**
     * Set this can use humanize keys
     *
     * @param boolean $useHumanizedKeys
     * @return void
     */
    public function setUseHumanizedKeys(bool $useHumanizedKeys = true)
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    /**
     * Get $this->useHumanizedKeys value
     *
     * @return void
     */
    public function isUsingHumanizedKey(): bool
    {
        return $this->useHumanizedKeys;
    }
}
