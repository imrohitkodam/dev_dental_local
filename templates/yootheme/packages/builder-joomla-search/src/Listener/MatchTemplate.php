<?php

namespace YOOtheme\Builder\Joomla\Search\Listener;

use Joomla\CMS\Document\Document;

class MatchTemplate
{
    public string $language;

    public function __construct(?Document $document)
    {
        $this->language = $document->language ?? 'en-gb';
    }

    public function handle($view, $tpl): ?array
    {
        if ($tpl) {
            return null;
        }

        $context = $view->get('context');

        if ($context === 'com_search.search') {
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'search' => [
                        'searchword' => $view->searchword,
                        'total' => $view->total,
                        'error' => $view->error ?: null,
                    ],
                    'items' => $view->get('results') ?? [],
                    'pagination' => $pagination,
                ],
            ];
        }

        return null;
    }
}
