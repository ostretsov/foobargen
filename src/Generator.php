<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.09.17 16:35.
 */

namespace Foobargen;

use Foobargen\Exception\MDFileNotFoundException;
use Foobargen\Exception\Page\InvalidPathException;
use Foobargen\Exception\Page\PageTitleIsNotDefinedException;
use Foobargen\Model\Page;
use Foobargen\Model\Tag;
use Foobargen\Model\TagPool;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

final class Generator
{
    /**
     * @var string
     */
    private $dataDir;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var TagPool
     */
    private $tagPool;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(string $dataDir, string $webDir, string $theme)
    {
        $this->dataDir = $dataDir;
        $this->webDir = __DIR__.'/../'.$webDir;
        $this->theme = $theme;
        $this->tagPool = new TagPool();
        $this->filesystem = new Filesystem();

        $loader = new \Twig_Loader_Filesystem([__DIR__.'/../themes/'.$theme.'/pages']);
        $this->twig = new \Twig_Environment($loader);
    }

    public function generate(): void
    {
        $pages = $this->parsePages();

        $this->initThemeAssets();
        $this->renderIndex($pages);
        $this->renderPages($pages);
        $this->renderArchive($pages);
        $this->renderTags();
        $this->renderRSS($pages);
    }

    private function parsePages(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../'.$this->dataDir)->name('*.meta');

        // parse data
        $pages = [];
        $pageFactory = new PageFactory($this->tagPool);
        foreach ($finder as $metaFile) {
            try {
                $pages[] = $pageFactory->createPage($metaFile);
            } catch (MDFileNotFoundException $e) {
                fwrite(STDERR, sprintf('Warning! File %s does have appropriate MD file!', $metaFile->getRealPath()));
            } catch (InvalidArgumentException $e) {
                fwrite(STDERR, sprintf('Invalid configuration: %s', $e->getMessage()));
            } catch (PageTitleIsNotDefinedException $e) {
                fwrite(STDERR, sprintf('Page title (h1 tag in the content) is not defined in the %s page', $metaFile->getRealPath()));
            } catch (InvalidPathException $e) {
                fwrite(STDERR, sprintf('Invalid path in %s! Path must start and end with forward slash.', $metaFile->getRealPath()));
            }
        }

        // sort
        usort($pages, function (Page $p1, Page $p2) {
            return $p2->getPublishedAt()->format('U') - $p1->getPublishedAt()->format('U');
        });

        return $pages;
    }

    private function initThemeAssets(): void
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../themes/'.$this->theme.'/assets');
        foreach ($finder as $asset) {
            $dstFile = $this->webDir.'/'.$asset->getRelativePathname();
            $this->filesystem->copy($asset->getPathname(), $dstFile, true);
        }
    }

    /**
     * @param Page[] $pages
     */
    private function renderIndex($pages): void
    {
        $this->renderTemplate('index.html.twig', ['pages' => $pages], $this->webDir.'/index.html');
    }

    /**
     * @param Page[] $pages
     */
    private function renderArchive($pages): void
    {
        $this->renderTemplate('archive.html.twig', ['pages' => $pages], $this->webDir.'/archive/index.html');
    }

    /**
     * @param Page[] $pages
     */
    private function renderPages($pages): void
    {
        foreach ($pages as $page) {
            $this->renderTemplate('page.html.twig', ['page' => $page], $this->webDir.$page->getPath().'/index.html');
        }
    }

    private function renderTags(): void
    {
        $tags = $this->tagPool->getPool();
        usort($tags, function (Tag $t1, Tag $t2) {
            return $t2->getWeight() - $t1->getWeight();
        });
        $this->renderTemplate('tags.html.twig', ['tags' => $tags], $this->webDir.'/tags/index.html');
    }

    private function renderRSS($pages)
    {
        $this->renderTemplate('rss.xml.twig', ['pages' => $pages], $this->webDir.'/feed/rss.xml');
    }

    private function renderTemplate(string $templateName, array $parameters, string $outputTo)
    {
        $HTML = $this->twig->render($templateName, $parameters);
        $this->filesystem->dumpFile($outputTo, $HTML);
    }
}