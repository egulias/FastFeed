<?php
/**
 * This file is part of the FastFeed package.
 *
 * (c) Daniel González <daniel@desarrolla2.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FastFeed\Processor;

use DOMElement;
use FastFeed\Item;

/**
 * ImageProcessor
 */
class ImageProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $ignoredPatterns = array();

    /**
     * @param array $ignoredPatterns
     */
    public function setIgnoredPatterns(array $ignoredPatterns)
    {
        $this->ignoredPatterns = array();
        foreach ($ignoredPatterns as $ignoredPattern) {
            $this->addIgnoredPattern($ignoredPattern);
        }
    }

    /**
     * @param $ignoredPattern
     */
    public function addIgnoredPattern($ignoredPattern)
    {
        $this->ignoredPatterns[] = (string)$ignoredPattern;
    }

    /**
     * @return array
     */
    public function getIgnoredPatterns()
    {
        return $this->ignoredPatterns;
    }

    /**
     * @param DOMElement $node
     * @param Item       $item
     */
    public function process(DOMElement $node, Item $item)
    {
        if ($item->hasImage()) {
            return;
        }

        $this->setImageFromContent($item);
    }

    /**
     * @param Item $item
     *
     * @return bool|string
     */
    protected function setImageFromContent(Item $item)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($item->getContent());
        $dom->preserveWhiteSpace = false;
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $imageSrc = $image->getAttribute('src');

            if ($this->isOnIgnoredPatterns($imageSrc)) {
                continue;
            }

            return $imageSrc;
        }

        return false;
    }

    /**
     * @param $imageSrc
     *
     * @return bool
     */
    protected function isOnIgnoredPatterns($imageSrc)
    {
        foreach ($this->ignoredPatterns as $ignoredPattern) {
            if (preg_match($ignoredPattern, $imageSrc)) {
                return true;
            }
        }

        return false;
    }

} 