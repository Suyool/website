<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class HtmlCompressTokenParser extends AbstractTokenParser
{
    /**
     * @param Token $token
     *
     * @return bool
     */
    public function decideHtmlCompressEnd(Token $token): bool
    {
        return $token->test('endcompresshtml');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getTag(): string
    {
        return 'compresshtml';
    }

    /**
     * @param Token $token
     *
     * @return MinifyHtmlNode
     */
    public function parse(Token $token)
    {
        $lineNumber = $token->getLine();
        $stream = $this->parser->getStream();
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideHtmlCompressEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);
        $nodes = ['body' => $body];

        return new HtmlCompressNode($nodes, [], $lineNumber, $this->getTag());
    }
}
