<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 16:59.
 */

namespace Foobargen;


use Foobargen\Exception\MDFileNotFoundException;
use Foobargen\Exception\Page\InvalidPathException;
use Foobargen\Model\Page;
use Foobargen\Model\Tag;
use Foobargen\Model\TagPool;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageFactory
{
    /**
     * @var TagPool
     */
    private $tagPool;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var \Parsedown
     */
    private $parsedown;

    public function __construct(TagPool $tagPool)
    {
        $this->tagPool = $tagPool;

        $resolver = new OptionsResolver();
        $resolver->setRequired(['path', 'published', 'audio']);
        $resolver->setDefined(['tags', 'youtube-video']);
        $resolver->setDefaults(['tags' => []]);
        $this->optionsResolver = $resolver;

        $this->parsedown = new \Parsedown();
    }

    public function createPage(\SplFileInfo $metaFile): Page
    {
        $meta = $this->parseMetaInfo($metaFile);
        $HTMLContent = $this->getHTMLContent($metaFile);

        return new Page($meta['path'], $meta['published'], $meta['audio'], $HTMLContent, $meta['youtube-video'], $meta['tags']);
    }

    private function parseMetaInfo(\SplFileInfo $metaFile): array
    {
        $meta = [];
        $f = fopen($metaFile->getPathname(), 'r');
        while ($s = fgets($f)) {
            [$key, $value] = explode(': ', $s);
            $key = trim($key);
            $value = trim($value);

            switch ($key) {
                case 'path':
                    if (!preg_match('/^\/.*\/$/', $value)) {
                        throw new InvalidPathException();
                    }
                    $meta[$key] = $value;
                    break;
                case 'published':
                    $meta[$key] = new \DateTime($value);
                    break;
                case 'tags':
                    $meta[$key] = $this->tagPool->process($value);
                    break;
                default:
                    $meta[$key] = $value;
            }
        }
        fclose($f);

        $meta = $this->optionsResolver->resolve($meta);

        /** @var Tag $tag */
        foreach ($meta['tags'] as $tag) {
            $tag->of($meta['path']);
        }

        return $meta;
    }

    private function getHTMLContent(\SplFileInfo $metaFile): string
    {
        $MDFilePathname = str_replace('.meta', '.md', $metaFile->getPathname());
        if (!file_exists($MDFilePathname)) {
            throw new MDFileNotFoundException();
        }

        return $this->parsedown->text(file_get_contents($MDFilePathname));
    }
}