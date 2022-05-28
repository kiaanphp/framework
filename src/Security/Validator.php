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
| Validator
|---------------------------------------------------
*/
class Validator {
    
    /**
     * Rules
     * 
    **/
    private $rules = [];

    /**
    * Namespace
    *
    */
    private $namespace;

    /**
    * Default method
    *
    */
    protected $defaultMethod;

    /**
     * Errors
     * 
    **/
    private $errors = [];

    /**
     * Errors
     * 
    **/
    private $error = [];
    
    /**
     * Check
     * 
    **/
    private $check = false;

    /**
     * Construct
     * 
    */
    public function __construct(){}

     /**
	 * Prepare path
     * 
	*/
    protected function prepare_path($path)
    {
        return str_replace(['/', '//', '.'], '\\', $path);
    }

    /**
     * Get namespace
     * 
    */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Set namespace
     * 
    */
    public function setNamespace($value) {
        $this->namespace = $value;

        return $this;
    }
    
    /**
     * Get default method
     * 
    */
    public function getDefaultMethod() {
        return $this->defaultMethod;
    }

    /**
     * Set default method
     * 
    */
    public function setDefaultMethod($value) {
        $this->defaultMethod = $value;

        return $this;
    }

    /*
    * Get rules
    */
    public function getRules(){
        return $this->rules;
    }

    /*
    * Set rules
    */
    public function setRules(array $list){
        $list = array_map('trim', $list);
        return $this->rules = $this->prepare_path($list);
    }

    /*
    * Add rules
    */
    public function addRules(array $list){
        $list = array_map('trim', $list);
        return $this->rules = $this->prepare_path(array_merge($this->rules, $list));
    }

    /**
     * Validate
     * 
    **/
    public function validate(array $request, array $rules, array $message = null)
    {
        // Errors
        $errors = array();

        // rules
        // get rules
        foreach ($rules as $key => $rulesString) { 
            if(!empty($key)){

            // Rules String
            $rulesString =  rtrim(preg_replace('/\s+/', '', str_replace(' ', '', $rulesString)), '|');

            // Rule Field
            $ruleField = explode(':', $key)[0] ?? $key;
            $ruleField = trim($ruleField);

            // Name Field
            $nameField = explode(':', $key)[1] ?? $ruleField;
            $nameField = trim($nameField);

            // get key
            $key = explode(':', $key)[0] ?? $key;
            $key = trim($key);

            // rule_errors
            $rule_errors = array();

            foreach (explode("|", $rulesString) as $rule) {
                    $arr = array($rule=>null);
                    $rule_errors = array_merge($arr,$rule_errors);
            } 

            // get rule and validate
            foreach (explode("|", $rulesString) as $rule) { 

                // prams
                $prams = explode(":", $rule)[1] ?? null;
                
                // get rule
                $rule = explode(':', $rule)[0] ?? $rule;
                $rule = trim($rule);

                if(!empty($rule)){

                // Message
                $messageField = $message[$ruleField.'.'.$rule] ?? null;

                // Call rule

                // get rule namespace
                $rule_namespace = $this->rules[$rule];
                $call_rule_namespace = $this->prepare_path($rule_namespace);
                $call_rule = trim(explode(',', $call_rule_namespace)[0]);
                $method = explode(',', $call_rule_namespace)[1] ?? $this->getDefaultMethod();
                $method = trim($method);

                if (!class_exists($call_rule)) {
                    $rule_namespace = $this->getNamespace() . '.' . $rule_namespace;
                    $rule_namespace = trim(explode(',', $rule_namespace)[0]);
                    $call_rule = $this->prepare_path($rule_namespace);
                }

                if (class_exists($call_rule)) {
                    $object = new $call_rule;
                    $object->request = $request;

                    // Get Methods
                    $methods = call_user_func([$object, $method]);

                    // validate
                    $validate = call_user_func_array([$object, $methods['rule']], [$nameField, $request[$ruleField], $prams]);
                   
                    if(!($validate)){
                        if(is_null($messageField)){
                            $validate_msg = call_user_func_array([$object, $methods['message']], [$nameField, $request[$ruleField], $prams]);
                            if(empty($validate_msg)){$validate_msg = "Field '$nameField' does not match verification";}
                        }else{
                            $validate_msg = call_user_func_array([$object, $methods['text']], [$nameField, $request[$ruleField], $prams, $messageField]);
                            if(empty($validate_msg)){$validate_msg = $messageField;}
                        }
                        
                        // Errors
                            $rule_errors[$rule] = $validate_msg;

                    }

                } else {
                    throw new \ReflectionException("Rule " . $rule . " is not found");
                }
               
            }  

            //$rule_errors = array_filter($rule_errors, 'strlen');

          }

            $rule_errors = array_filter($rule_errors);
            $errors[$key] = $rule_errors;

        }
      }

        // set errors
        if(sizeof($errors)!=0){
            $this->errors = $errors;
        }

        // Get count of errors && error
        $errors_count = 0;
        $listError = array();
        foreach ($errors as $key => $error) {
            // Count
            $errors_count += sizeof($error);

            // Error
            $listError = array_merge($listError, array_values($error));
        }

        $this->error = $listError;

        // set check
        if($errors_count==0){
            $this->check = true;
        }else{
            $this->check = false;
        }

        $errors_object = array("errors" => $this->errors, "error" => $this->error,  "count" => $errors_count, "check" => $this->check);
      
        return json_decode(json_encode($errors_object));
    }
    
    /**
     * Error
     * 
    **/
    public function error()
    {
        return $this->error;
    }

    /**
     * Errors
     * 
    **/
    public function errors($key='')
    {
        if(empty($key)){
            return $this->errors;
        }else{
            if (array_key_exists($key, $this->errors))
            {
                return $this->errors[$key];
            }
            else
            {
                return '';
            }
        }
    }

    /**
     * Check
     * 
    **/
    public function check()
    {
        return $this->check;
    }

}