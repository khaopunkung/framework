<?php

namespace Illuminate\Console\View\Components;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table as SymfonyTable;
use Symfony\Component\Console\Helper\TableStyle;

class Table extends Component
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  array  $headers
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $rows
     * @return void
     */
    public function render($headers, $rows)
    {
        $table = new SymfonyTable($this->output);
        $tableStyle = (new TableStyle())
            ->setHorizontalBorderChars('')
            ->setVerticalBorderChars(' ')
            ->setDefaultCrossingChar('')
            ->setCellHeaderFormat('<fg=green;options=bold>%s</>');

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        $table
            ->setStyle($tableStyle)
            ->setHeaders((array) $headers)
            ->setRows($rows)
            ->render();
    }
}
