<?php

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Joshbrw\DebugbarViewComposers\DataCollectors\ViewComposerCollector;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class ViewComposerCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider mockedCollector
     * @param ViewComposerCollector $collector
     * @param CliDumper $dumper
     * @param VarCloner $cloner
     */
    public function itShouldAcceptVarDumperAndCloner(
        ViewComposerCollector $collector,
        CliDumper $dumper,
        VarCloner $cloner
    ) {
        $this->assertInstanceOf(
            ViewComposerCollector::class,
            $collector
        );
    }

    /**
     * @test
     * @dataProvider mockedCollector
     */
    public function itShouldDescribeComposerAndStoreResult(
        ViewComposerCollector $collector,
        CliDumper $dumper,
        VarCloner $cloner
    ) {
        $view = Mockery::mock(View::class);

        $collector->shouldReceive('describeViewComposer')
            ->once()
            ->with($view, $viewComposer = "I'm a View Composer")
            ->andReturn($result = "I'm a description");

        $collector->addViewComposer($view, $viewComposer);

        $this->assertAttributeEquals(
            collect([[ 'message' => $result ]]),
            'viewComposers',
            $collector
        );
    }

    /**
     * @test
     * @dataProvider mockedCollector
     */
    public function itShouldOutputCollectionCorrectly(
        ViewComposerCollector $collector,
        CliDumper $dumper,
        VarCloner $cloner
    ) {
        $view = Mockery::mock(View::class);

        $collector->shouldReceive('describeViewComposer')
            ->once()
            ->with($view, $viewComposer = "I'm a View Composer")
            ->andReturn($resultOne = "I'm a description");

        $collector->addViewComposer($view, $viewComposer);

        $collector->shouldReceive('describeViewComposer')
            ->once()
            ->with($view, $viewComposer = "I'm a View Composer, too!")
            ->andReturn($resultTwo = "I'm a description, too!");

        $collector->addViewComposer($view, $viewComposer);

        $this->assertSame(
            [
                'view-composers' => [
                    [ 'message' => $resultOne ],
                    [ 'message' => $resultTwo ]
                ],
                'count' => 2
            ],
            $collector->collect()
        );
    }

    /**
     * @test
     * @dataProvider mockedCollector
     */
    public function itShouldImplementCorrectClasses(
        ViewComposerCollector $collector,
        CliDumper $dumper,
        VarCloner $cloner
    ) {
        $this->assertInstanceOf(DataCollectorInterface::class, $collector);
        $this->assertInstanceOf(Renderable::class, $collector);
    }

    /**
     * Provide a ViewComposerCollector and it's mocked arguments
     * @return array
     */
    public function mockedCollector()
    {
        $collector = Mockery::mock(ViewComposerCollector::class, [
                $dumper = Mockery::mock(CliDumper::class),
                $cloner = Mockery::mock(VarCloner::class)
            ])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        return [
            [ $collector, $dumper, $cloner ]
        ];
    }

}
