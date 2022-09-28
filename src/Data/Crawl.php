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
namespace Kiaan\Data;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Data\Crawl\QuerySelectors;
use Kiaan\Data\Crawl\Element;

/*
|---------------------------------------------------
| Crawl
|---------------------------------------------------
*/
class Crawl extends \DOMDocument
{

    /*
    * Traits
    *
    */
    use QuerySelectors;

    /*
    * Global traits
    *
    */
    use \Kiaan\Application\Resources\Global\FilesystemPathTrait;
    use \Kiaan\Application\Resources\Global\ExtendingTrait;

    /**
     * A modification that removes all but the last title elements.
     */
    const FIX_MULTIPLE_TITLES = 2;

    /**
     * A modification that removes all but the last metatags with matching name or property attributes.
     */
    const FIX_DUPLICATE_METATAGS = 4;

    /**
     * A modification that merges multiple head elements.
     */
    const FIX_MULTIPLE_HEADS = 8;

    /**
     * A modification that merges multiple body elements.
     */
    const FIX_MULTIPLE_BODIES = 16;

    /**
     * A modification that moves charset metatag and title elements first.
     */
    const OPTIMIZE_HEAD = 32;

    /**
     *
     * @var array
     */
    static private $newObjectsCache = [];

    /**
     * Indicates whether an HTML code is loaded.
     *
     * @var boolean
     */
    private $loaded = false;
    
    /**
     * Content
     *
     */
    private $content;
    
    /**
     * Creates a new object.
     *
     */
    public function __construct()
    {
        parent::__construct(1.0);
        $this->registerNodeClass('DOMElement', Element::class);
    }

    /**
     * Load HTML from URL.
     *
     */
    public function url($url){
        $content = file_get_contents($url);
        
        $this->content = $content;
        
        return $this->loadHTML($content);
    }

    /**
     * Load HTML from file.
     *
     */
    public function file($path){
        $path = $this->filesystemRoot() . $path;

        $content = file_get_contents($path);

        $this->content = $content;

        return $this->loadHTML($content);
    }
    
    /**
     * Load HTML from a string.
     *
     */
    public function string(string $string){
        $this->content = $string;

        return $this->loadHTML($string);
    }

    /**
     * Load HTML from a string.
     *
     */
    public function loadHTML($source, $options = 0)
    {
        // Enables libxml errors handling
        $internalErrorsOptionValue = libxml_use_internal_errors();
        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(true);
        }

        $source = trim($source);

        // Add CDATA around script tags content
        $matches = null;
        preg_match_all('/<script(.*?)>/', $source, $matches);
        if (isset($matches[0])) {
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $match) {
                if (substr($match, -2, 1) !== '/') { // check if ends with />
                    $source = str_replace($match, $match . '<![CDATA[-html5-dom-document-internal-cdata', $source); // Add CDATA after the open tag
                }
            }
        }
        $source = str_replace('</script>', '-html5-dom-document-internal-cdata]]></script>', $source); // Add CDATA before the end tag
        $source = str_replace('<![CDATA[-html5-dom-document-internal-cdata-html5-dom-document-internal-cdata]]>', '', $source); // Clean empty script tags
        $matches = null;
        preg_match_all('/\<!\[CDATA\[-html5-dom-document-internal-cdata.*?-html5-dom-document-internal-cdata\]\]>/s', $source, $matches);
        if (isset($matches[0])) {
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $match) {
                if (strpos($match, '</') !== false) { // check if contains </
                    $source = str_replace($match, str_replace('</', '<-html5-dom-document-internal-cdata-endtagfix/', $match), $source);
                }
            }
        }

        $autoAddHtmlAndBodyTags = !defined('LIBXML_HTML_NOIMPLIED') || ($options & LIBXML_HTML_NOIMPLIED) === 0;
        $autoAddDoctype = !defined('LIBXML_HTML_NODEFDTD') || ($options & LIBXML_HTML_NODEFDTD) === 0;

        // Add body tag if missing
        if ($autoAddHtmlAndBodyTags && $source !== '' && preg_match('/\<!DOCTYPE.*?\>/', $source) === 0 && preg_match('/\<html.*?\>/', $source) === 0 && preg_match('/\<body.*?\>/', $source) === 0 && preg_match('/\<head.*?\>/', $source) === 0) {
            $source = '<body>' . $source . '</body>';
        }

        // Add DOCTYPE if missing
        if ($autoAddDoctype && strtoupper(substr($source, 0, 9)) !== '<!DOCTYPE') {
            $source = "<!DOCTYPE html>\n" . $source;
        }

        // Adds temporary head tag
        $charsetTag = '<meta data-html5-dom-document-internal-attribute="charset-meta" http-equiv="content-type" content="text/html; charset=utf-8" />';
        $matches = [];
        preg_match('/\<head.*?\>/', $source, $matches);
        $removeHeadTag = false;
        $removeHtmlTag = false;
        if (isset($matches[0])) { // has head tag
            $insertPosition = strpos($source, $matches[0]) + strlen($matches[0]);
            $source = substr($source, 0, $insertPosition) . $charsetTag . substr($source, $insertPosition);
        } else {
            $matches = [];
            preg_match('/\<html.*?\>/', $source, $matches);
            if (isset($matches[0])) { // has html tag
                $source = str_replace($matches[0], $matches[0] . '<head>' . $charsetTag . '</head>', $source);
            } else {
                $source = '<head>' . $charsetTag . '</head>' . $source;
                $removeHtmlTag = true;
            }
            $removeHeadTag = true;
        }

        // Preserve html entities
        $source = preg_replace('/&([a-zA-Z]*);/', 'html5-dom-document-internal-entity1-$1-end', $source);
        $source = preg_replace('/&#([0-9]*);/', 'html5-dom-document-internal-entity2-$1-end', $source);

        $result = parent::loadHTML('<?xml encoding="utf-8" ?>' . $source, $options);
        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(false);
        }
        if ($result === false) {
            return false;
        }
        $this->encoding = 'utf-8';
        foreach ($this->childNodes as $item) {
            if ($item->nodeType === XML_PI_NODE) {
                $this->removeChild($item);
                break;
            }
        }
        /** @var Element|null */
        $metaTagElement = $this->getElementsByTagName('meta')->item(0);
        if ($metaTagElement !== null) {
            if ($metaTagElement->getAttribute('data-html5-dom-document-internal-attribute') === 'charset-meta') {
                $headElement = $metaTagElement->parentNode;
                $htmlElement = $headElement->parentNode;
                $metaTagElement->parentNode->removeChild($metaTagElement);
                if ($removeHeadTag && $headElement !== null && $headElement->parentNode !== null && ($headElement->firstChild === null || ($headElement->childNodes->length === 1 && $headElement->firstChild instanceof \DOMText))) {
                    $headElement->parentNode->removeChild($headElement);
                }
                if ($removeHtmlTag && $htmlElement !== null && $htmlElement->parentNode !== null && $htmlElement->firstChild === null) {
                    $htmlElement->parentNode->removeChild($htmlElement);
                }
            }
        }

        $this->loaded = true;

        return clone($this);
    }

    /**
     * Xpath
     *
     */
    public function xpath(string $selector) {
        $doc = new \DomDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($this->content);
        $xpath = new \DomXpath($doc);
        
        return $xpath->query($selector);
    }

    /**
     * CSS selector
     *
     */
    public function selector(string $selector) {
        return $this->xpath($this->selector_to_xpath($selector));
    }

    /**
     * Convert selector css to xpath
     *
     */
    private function selector_to_xpath($selector) {
        // remove spaces around operators
        $selector = preg_replace('/\s*>\s*/', '>', $selector);
        $selector = preg_replace('/\s*~\s*/', '~', $selector);
        $selector = preg_replace('/\s*\+\s*/', '+', $selector);
        $selector = preg_replace('/\s*,\s*/', ',', $selector);
        $selectors = preg_split('/\s+(?![^\[]+\])/', $selector);
      
        foreach ($selectors as &$selector) {
            // ,
            $selector = preg_replace('/,/', '|descendant-or-self::', $selector);
            // +
            $selector = preg_replace('/\+([_\w-]+[_\w\d-]*)/', '/following-sibling::\1[position()=1]', $selector);
            // input:checked, :disabled, etc.
            $selector = preg_replace('/(.+)?:(checked|disabled|required|autofocus)/', '\1[@\2="\2"]', $selector);
            // input:autocomplete, :autocomplete
            $selector = preg_replace('/(.+)?:(autocomplete)/', '\1[@\2="on"]', $selector);
            // input:button, input:submit, etc.
            $selector = preg_replace('/:(text|password|checkbox|radio|button|submit|reset|file|hidden|image|datetime|datetime-local|date|month|time|week|number|range|email|url|search|tel|color)/', 'input[@type="\1"]', $selector);
            // foo[id]
            $selector = preg_replace('/(\w+)\[([_\w-]+[_\w\d-]*)\]/', '\1[@\2]', $selector);
            // [id]
            $selector = preg_replace('/\[([_\w-]+[_\w\d-]*)\]/', '*[@\1]', $selector);
            // foo[id=foo]
            $selector = preg_replace('/\[([_\w-]+[_\w\d-]*)=[\'"]?(.*?)[\'"]?\]/', '[@\1="\2"]', $selector);
            // [id=foo]
            $selector = preg_replace('/^\[/', '*[', $selector);
            // div#foo
            $selector = preg_replace('/([_\w-]+[_\w\d-]*)\#([_\w-]+[_\w\d-]*)/', '\1[@id="\2"]', $selector);
            // #foo
            $selector = preg_replace('/\#([_\w-]+[_\w\d-]*)/', '*[@id="\1"]', $selector);
            // div.foo
            $selector = preg_replace('/([_\w-]+[_\w\d-]*)\.([_\w-]+[_\w\d-]*)/', '\1[contains(concat(" ",@class," ")," \2 ")]', $selector);
            // .foo
            $selector = preg_replace('/\.([_\w-]+[_\w\d-]*)/', '*[contains(concat(" ",@class," ")," \1 ")]', $selector);
            // div:first-child
            $selector = preg_replace('/([_\w-]+[_\w\d-]*):first-child/', '*/\1[position()=1]', $selector);
            // div:last-child
            $selector = preg_replace('/([_\w-]+[_\w\d-]*):last-child/', '*/\1[position()=last()]', $selector);
            // :first-child
            $selector = str_replace(':first-child', '*/*[position()=1]', $selector);
            // :last-child
            $selector = str_replace(':last-child', '*/*[position()=last()]', $selector);
            // :nth-last-child
            $selector = preg_replace('/:nth-last-child\((\d+)\)/', '[position()=(last() - (\1 - 1))]', $selector);
            // div:nth-child
            $selector = preg_replace('/([_\w-]+[_\w\d-]*):nth-child\((\d+)\)/', '\1[(count(preceding-sibling::*)+1) = \2]', $selector);
            // :nth-child
            $selector = preg_replace('/:nth-child\((\d+)\)/', '*/*[position()=\1]', $selector);
            // :contains(Foo)
            $selector = preg_replace('/([_\w-]+[_\w\d-]*):contains\((.*?)\)/', '\1[contains(string(.),"\2")]', $selector);
            // >
            $selector = preg_replace('/>/', '/', $selector);
            // ~
            $selector = preg_replace('/~/', '/following-sibling::', $selector);
            // Finish
            $selector = str_replace(']*', ']', $selector);
            $selector = str_replace(']/*', ']', $selector);
        }
      
        // ' '
        $selector = implode('/descendant::', $selectors);
        $selector = 'descendant-or-self::' . $selector;
        // :scope
        $selector = preg_replace('/(((\|)?descendant-or-self::):scope)/', '.\3', $selector);
        // $element
        $sub_selectors = explode(',', $selector);
      
        foreach ($sub_selectors as $key => $sub_selector) {
            $parts = explode('$', $sub_selector);
            $sub_selector = array_shift($parts);
      
            if (count($parts) && preg_match_all('/((?:[^\/]*\/?\/?)|$)/', $parts[0], $matches)) {
                $results = $matches[0];
                $results[] = str_repeat('/..', count($results) - 2);
                $sub_selector .= implode('', $results);
            }
      
            $sub_selectors[$key] = $sub_selector;
        }
      
        $selector = implode(',', $sub_selectors);
        
        return $selector;
      }

    /**
     * Adds the HTML tag to the document if missing.
     *
     */
    private function addHtmlElementIfMissing(): bool
    {
        if ($this->getElementsByTagName('html')->length === 0) {
            if (!isset(self::$newObjectsCache['htmlelement'])) {
                self::$newObjectsCache['htmlelement'] = new \DOMElement('html');
            }
            $this->appendChild(clone (self::$newObjectsCache['htmlelement']));
            return true;
        }
        return false;
    }

    /**
     * Adds the HEAD tag to the document if missing.
     *
     */
    private function addHeadElementIfMissing(): bool
    {
        if ($this->getElementsByTagName('head')->length === 0) {
            $htmlElement = $this->getElementsByTagName('html')->item(0);
            if (!isset(self::$newObjectsCache['headelement'])) {
                self::$newObjectsCache['headelement'] = new \DOMElement('head');
            }
            $headElement = clone (self::$newObjectsCache['headelement']);
            if ($htmlElement->firstChild === null) {
                $htmlElement->appendChild($headElement);
            } else {
                $htmlElement->insertBefore($headElement, $htmlElement->firstChild);
            }
            return true;
        }
        return false;
    }

    /**
     * Adds the BODY tag to the document if missing.
     *
     */
    private function addBodyElementIfMissing(): bool
    {
        if ($this->getElementsByTagName('body')->length === 0) {
            if (!isset(self::$newObjectsCache['bodyelement'])) {
                self::$newObjectsCache['bodyelement'] = new \DOMElement('body');
            }
            $this->getElementsByTagName('html')->item(0)->appendChild(clone (self::$newObjectsCache['bodyelement']));
            return true;
        }
        return false;
    }

    /**
     * Dumps the internal document into a string using HTML formatting.
     *
     */
    public function source(\DOMNode $node = null): string
    {
        $nodeMode = $node !== null;
        if ($nodeMode && $node instanceof \DOMDocument) {
            $nodeMode = false;
        }

        if ($nodeMode) {
            if (!isset(self::$newObjectsCache['domdocument'])) {
                self::$newObjectsCache['domdocument'] = new $this();
            }
            $tempDomDocument = clone (self::$newObjectsCache['domdocument']);
            if ($node->nodeName === 'html') {
                $tempDomDocument->loadHTML('<!DOCTYPE html>');
                $tempDomDocument->appendChild($tempDomDocument->importNode(clone ($node), true));
                $html = $tempDomDocument->source();
                $html = substr($html, 16); // remove the DOCTYPE + the new line after
            } elseif ($node->nodeName === 'head' || $node->nodeName === 'body') {
                $tempDomDocument->loadHTML("<!DOCTYPE html>\n<html></html>");
                $tempDomDocument->childNodes[1]->appendChild($tempDomDocument->importNode(clone ($node), true));
                $html = $tempDomDocument->source();
                $html = substr($html, 22, -7); // remove the DOCTYPE + the new line after + html tag
            } else {
                $isInHead = false;
                $parentNode = $node;
                for ($i = 0; $i < 1000; $i++) {
                    $parentNode = $parentNode->parentNode;
                    if ($parentNode === null) {
                        break;
                    }
                    if ($parentNode->nodeName === 'body') {
                        break;
                    } elseif ($parentNode->nodeName === 'head') {
                        $isInHead = true;
                        break;
                    }
                }
                $tempDomDocument->loadHTML("<!DOCTYPE html>\n<html>" . ($isInHead ? '<head></head>' : '<body></body>') . '</html>');
                $tempDomDocument->childNodes[1]->childNodes[0]->appendChild($tempDomDocument->importNode(clone ($node), true));
                $html = $tempDomDocument->source();
                $html = substr($html, 28, -14); // remove the DOCTYPE + the new line + html + body or head tags
            }
            $html = trim($html);
        } else {
            $removeHtmlElement = false;
            $removeHeadElement = false;
            $headElement = $this->getElementsByTagName('head')->item(0);
            if ($headElement === null) {
                if ($this->addHtmlElementIfMissing()) {
                    $removeHtmlElement = true;
                }
                if ($this->addHeadElementIfMissing()) {
                    $removeHeadElement = true;
                }
                $headElement = $this->getElementsByTagName('head')->item(0);
            }
            $meta = $this->createElement('meta');
            $meta->setAttribute('data-html5-dom-document-internal-attribute', 'charset-meta');
            $meta->setAttribute('http-equiv', 'content-type');
            $meta->setAttribute('content', 'text/html; charset=utf-8');
            if ($headElement->firstChild !== null) {
                $headElement->insertBefore($meta, $headElement->firstChild);
            } else {
                $headElement->appendChild($meta);
            }
            $html = parent::saveHTML();
            $html = rtrim($html, "\n");

            if ($removeHeadElement) {
                $headElement->parentNode->removeChild($headElement);
            } else {
                $meta->parentNode->removeChild($meta);
            }

            if (strpos($html, 'html5-dom-document-internal-entity') !== false) {
                $html = preg_replace('/html5-dom-document-internal-entity1-(.*?)-end/', '&$1;', $html);
                $html = preg_replace('/html5-dom-document-internal-entity2-(.*?)-end/', '&#$1;', $html);
            }

            $codeToRemove = [
                'html5-dom-document-internal-content',
                '<meta data-html5-dom-document-internal-attribute="charset-meta" http-equiv="content-type" content="text/html; charset=utf-8">',
                '</area>', '</base>', '</br>', '</col>', '</command>', '</embed>', '</hr>', '</img>', '</input>', '</keygen>', '</link>', '</meta>', '</param>', '</source>', '</track>', '</wbr>',
                '<![CDATA[-html5-dom-document-internal-cdata', '-html5-dom-document-internal-cdata]]>', '-html5-dom-document-internal-cdata-endtagfix'
            ];
            if ($removeHeadElement) {
                $codeToRemove[] = '<head></head>';
            }
            if ($removeHtmlElement) {
                $codeToRemove[] = '<html></html>';
            }

            $html = str_replace($codeToRemove, '', $html);
        }
        return $html;
    }

    /**
     * Returns the first document element matching the selector.
     *
     */
    public function first(string $selector)
    {
        return $this->internalQuerySelector($selector);
    }

    /**
     * Returns a list of document elements matching the selector.
     *
     */
    public function get(string $selector)
    {
        return $this->internalQuerySelectorAll($selector);
    }

}
