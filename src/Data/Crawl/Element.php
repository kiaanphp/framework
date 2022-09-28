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
use Kiaan\Data\Crawl\QuerySelectors;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class Element extends \DOMElement
{

    use QuerySelectors;

    /**
     *
     * @var array
     */
    static private $foundEntitiesCache = [[], []];

    /**
     *
     * @var array
     */
    static private $newObjectsCache = [];

    /**
     * Updates the result value before returning it.
     *
     */
    private function updateResult(string $value): string
    {
        $value = str_replace(self::$foundEntitiesCache[0], self::$foundEntitiesCache[1], $value);
        if (strstr($value, 'dom-document') !== false) {
            $search = [];
            $replace = [];
            $matches = [];
            preg_match_all('/dom-document([12])-(.*?)-end/', $value, $matches);
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $i => $match) {
                $search[] = $match;
                $replace[] = html_entity_decode(($matches[1][$i] === '1' ? '&' : '&#') . $matches[2][$i] . ';');
            }
            $value = str_replace($search, $replace, $value);
            self::$foundEntitiesCache[0] = array_merge(self::$foundEntitiesCache[0], $search);
            self::$foundEntitiesCache[1] = array_merge(self::$foundEntitiesCache[1], $replace);
            unset($search);
            unset($replace);
            unset($matches);
        }
        return $value;
    }

    /**
     * Returns the updated nodeValue Property
     * 
     */
    public function value(): string
    {
        return $this->updateResult($this->nodeValue);
    }

    /**
     * Returns the updated $textContent Property
     * 
     */
    public function text(): string
    {
        return $this->updateResult($this->textContent);
    }

    /**
     * Returns html source code
     * 
     */
    public function html(): string
    {
        if ($this->firstChild === null) {
            $nodeName = $this->nodeName;
            $attributes = $this->attributes();
            $result = '<' . $nodeName . '';
            foreach ($attributes as $name => $value) {
                $result .= ' ' . $name . '="' . htmlentities($value) . '"';
            }
            if (array_search($nodeName, ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr']) === false) {
                $result .= '></' . $nodeName . '>';
            } else {
                $result .= '/>';
            }
            return $result;
        }
        return $this->ownerDocument->source($this);
    }

    /**
     * Returns the value for the attribute name specified.
     *
     */
    public function attribute($name): string
    {
        if ($this->attributes->length === 0) { // Performance optimization
            return '';
        }
        $value = parent::getAttribute($name);
        return $value !== '' ? (strstr($value, 'html5-dom-document-internal-entity') !== false ? $this->updateResult($value) : $value) : '';
    }

    /**
     * Returns an array containing all attributes.
     *
     */
    public function attributes(): array
    {
        $attributes = [];
        foreach ($this->attributes as $attributeName => $attribute) {
            $value = $attribute->value;
            $attributes[$attributeName] = $value !== '' ? (strstr($value, 'html5-dom-document-internal-entity') !== false ? $this->updateResult($value) : $value) : '';
        }
        return $attributes;
    }

    /**
     * Returns the element outerHTML.
     *
     */
    public function __toString(): string
    {
        return $this->outerHTML;
    }

    /**
     * Returns the first child element matching the selector.
     *
     */
    public function first(string $selector)
    {
        return $this->internalQuerySelector($selector);
    }

    /**
     * Returns a list of children elements matching the selector.
     *
     */
    public function get(string $selector)
    {
        return $this->internalQuerySelectorAll($selector);
    }
}
