<?php

namespace Joshbrw\DebugbarViewComposers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Joshbrw\DebugbarViewComposers\DataCollectors\ViewComposerCollector;

class ServiceProvider extends LaravelServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    public function boot(LaravelDebugbar $debugBar, EventDispatcher $eventDispatcher, ViewComposerCollector $viewComposerCollector)
    {
        $eventDispatcher->listen("creating: *", function (View $view) use ($viewComposerCollector, $eventDispatcher) {
            foreach ($this->findApplicableComposers($view, $eventDispatcher) as $composer) {
                $viewComposerCollector->addViewComposer($view, $composer);
            }
        });

        $debugBar->addCollector($viewComposerCollector);
    }

    /**
     * Find all applicable View Composers that have been registered for a View.
     * @param View $view    The View
     * @param EventDispatcher $eventDispatcher  Laravel's Events Dispatcher.
     * @return array
     */
    protected function findApplicableComposers(View $view, EventDispatcher $eventDispatcher)
    {
        if (! method_exists($eventDispatcher, 'getListeners')) {
            return [];
        }

        return $eventDispatcher->getListeners("composing: {$view->name()}");
    }

}
