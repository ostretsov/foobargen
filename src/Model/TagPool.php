<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 19:11.
 */

namespace Foobargen\Model;


class TagPool
{
    /**
     * @var Tag[]
     */
    private $pool;

    public function process(string $tagLine): array
    {
        $tagNames = explode(',', $tagLine);
        $tagNames = array_map(function ($tagName) {
            return trim($tagName);
        }, $tagNames);
        $tagNames = array_unique($tagNames);

        $tags = [];
        foreach ($tagNames as $tagName) {
            if (isset($this->pool[$tagName])) {
                $tag = $this->pool[$tagName];
            } else {
                $tag = new Tag($tagName);
                $this->pool[$tagName] = $tag;
            }
            $tags[] = $tag;
        }

        return $tags;
    }

    /**
     * @return Tag[]
     */
    public function getPool(): array
    {
        return $this->pool;
    }
}