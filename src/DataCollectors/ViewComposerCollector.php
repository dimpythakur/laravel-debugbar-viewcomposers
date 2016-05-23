<?php

namespace Joshbrw\DebugbarViewComposers\DataCollectors;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class ViewComposerCollector implements DataCollectorInterface, Renderable
{
    /**
     * @var CliDumper
     */
    protected $cliDumper;

    /**
     * @var Collection
     */
    protected $viewComposers;

    /**
     * @var VarCloner
     */
    protected $varCloner;

    public function __construct(CliDumper $cliDumper, VarCloner $varCloner)
    {
        $this->cliDumper = $cliDumper;
        $this->viewComposers = new Collection();
        $this->varCloner = $varCloner;
    }

    /**
     * Collect the View Composer
     * @param View $view    The View the View Composer is attached to
     * @param mixed $viewComposer View Composer
     * @return $this
     */
    public function addViewComposer(View $view, $viewComposer)
    {
        if (!$description = $this->describeViewComposer($view, $viewComposer)) {
            return false;
        }

        $this->viewComposers->push([
            'message' => $description
        ]);
    }

    /**
     * Describe a View Composer for logging.
     * @param View $view    View being created
     * @param mixed $viewComposer   View Composer
     * @return string
     */
    protected function describeViewComposer(View $view, $viewComposer)
    {
        $description = "View Composer called for view '{$view->name()}': ";

        $this->cliDumper->dump(
            $this->varCloner->cloneVar($viewComposer),
            function ($line, $depth) use (&$description) {
                $description .= "{$line} ";
            }
        );

        return $description;
    }

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    function collect()
    {
        return [
            'view-composers' => $this->viewComposers->toArray(),
            'count' => $this->viewComposers->count()
        ];
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    function getName()
    {
        return 'ViewComposers';
    }

    /**
     * Returns a hash where keys are control names and their values
     * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
     *
     * @return array
     */
    function getWidgets()
    {
        $name = $this->getName();

        return array(
            "$name" => array(
                'icon' => 'list-alt',
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.view-composers",
                "default" => "[]"
            ),
            "$name:badge" => array(
                "map" => "$name.count",
                "default" => "null"
            )
        );
    }
}
