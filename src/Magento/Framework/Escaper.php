<?php

namespace Magento\Framework;

use Stringable;

/**
 * @phpstan-type CastableToString null|scalar|Stringable
 */
class Escaper
{
    /**
     * @param CastableToString|array<CastableToString> $data
     * @param string[]|null $allowedTags
     * @return ($data is array ? string[] : string)
     */
    public function escapeHtml($data, array|null $allowedTags = null) {}

    /**
     * @param CastableToString $string
     * @param bool $escapeSingleQuote
     * @return string
     */
    public function escapeHtmlAttr($string, bool $escapeSingleQuote = true) {}

    /**
     * @param CastableToString $string
     * @return string
     */
    public function escapeUrl($string) {}

    /**
     * @param CastableToString $string
     * @return string
     */
    public function encodeUrlParam($string) {}

    /**
     * @param CastableToString $string
     * @return string
     */
    public function escapeJs($string) {}

    /**
     * @param CastableToString $string
     * @return string
     */
    public function escapeCss($string) {}

    /**
     * @param CastableToString[]|CastableToString $data
     * @param string $quote
     * @return ($data is array ? string[] : string)
     */
    public function  escapeJsQuote($data, string $quote = '\'') {}

    /**
     * @param CastableToString $data
     * @return string
     */
    public function escapeXssInUrl($data) {}

    /**
     * @param string $data
     * @param bool $addSlashes
     * @return string
     */
    public function escapeQuote(string $data, bool $addSlashes = false) {}
}