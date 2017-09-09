<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 10:19.
 */

namespace Foobargen\Model;

final class Tag
{
    /**
     * @var string Transliterated name
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $weight;

    public function __construct(string $name)
    {
        $this->id = transliterator_transliterate('Russian-Latin/BGN', $name);
        $this->name = $name;
        $this->weight = 1;
    }

    public function weightUp(): void
    {
        ++$this->weight;
    }
}