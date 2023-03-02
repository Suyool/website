<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HtmlCompressExtention extends AbstractExtension
{
    /**
     * @param Environment $twig
     * @param string      $html
     *
     * @return string
     */
    public function compress(Environment $twig, $html)
    {
        if(!isset($_GET['cleanhtml'])) {
            return
                // remove ws outside of all elements
                preg_replace('/>(?:\s\s*)?([^<]+)(?:\s\s*)?</s', '>$1<',
                    // remove ws around all elems excepting script|style|pre|textarea elems
                    preg_replace(
                        '/\s+(<\\/?(?!script|style|pre|textarea)\b[^>]*>)/i', '$1',
                        // trim line start
                        preg_replace('/^\s\s*/m', '',
                            // trim line end
                            preg_replace('/\s\s*$/m', '',
                                // remove HTML comments (not containing IE conditional comments)
                                preg_replace_callback(
                                    '/<!--([\s\S]*?)-->/',
                                    function ($m) {
                                        return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![')) ? $m[0] : '';
                                    },
                                    // start point
                                    $html
                                )
                            )
                        )
                    )
                );
        }

        return $html;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return[
            new TwigFilter('compresshtml', [$this, 'compressHtml'],[
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ])
        ];
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('compresshtml', [$this, 'compressHtml'],[
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getTokenParsers(): array
    {
        return [
            new HtmlCompressTokenParser(),
        ];
    }

/*    public function isCompressionActive(Environment $twig): bool
    {
        return $this->forceCompression
            ||
            !$twig->isDebug();
    }*/
}
