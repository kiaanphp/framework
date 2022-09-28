<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
**/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace Kiaan\Data\Crawl;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use ArrayIterator;
use DOMElement;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class TokenList
{

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var DOMElement
     */
    private $element;

    /**
     * @var string[]
     */
    private $tokens;

    /**
     * @var string
     */
    private $previousValue;

    /**
     * Creates a list of space-separated tokens based on the attribute value of an element.
     * 
     */
    public function __construct(DOMElement $element, string $attributeName)
    {
        $this->element = $element;
        $this->attributeName = $attributeName;
        $this->previousValue = null;
        $this->tokenize();
    }

    /**
     * Returns an item in the list by its index (returns null if the number is greater than or equal to the length of the list).
     * 
     */
    public function item(int $index)
    {
        $this->tokenize();
        if ($index >= count($this->tokens)) {
            return null;
        }
        return $this->tokens[$index];
    }

    /**
     * Returns true if the list contains the given token, otherwise false.
     * 
     */
    public function contains(string $token): bool
    {
        $this->tokenize();
        return in_array($token, $this->tokens);
    }

    /**
     * 
     */
    public function __toString(): string
    {
        $this->tokenize();
        return implode(' ', $this->tokens);
    }

    /**
     * Returns an iterator allowing you to go through all tokens contained in the list.
     * 
     */
    public function entries(): ArrayIterator
    {
        $this->tokenize();
        return new ArrayIterator($this->tokens);
    }

    /**
     * Returns the count of items
     *
     */
    public function count()
    {
        $this->tokenize();
        return count($this->tokens);
    }

    /**
     * 
     */
    private function tokenize()
    {
        $current = $this->element->getAttribute($this->attributeName);
        if ($this->previousValue === $current) {
            return;
        }
        $this->previousValue = $current;
        $tokens = explode(' ', $current);
        $finals = [];
        foreach ($tokens as $token) {
            if ($token === '') {
                continue;
            }
            if (in_array($token, $finals)) {
                continue;
            }
            $finals[] = $token;
        }
        $this->tokens = $finals;
    }

    /**
     * 
     */
    private function setAttributeValue()
    {
        $value = implode(' ', $this->tokens);
        if ($this->previousValue === $value) {
            return;
        }
        $this->previousValue = $value;
        $this->element->setAttribute($this->attributeName, $value);
    }
}
