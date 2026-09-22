<?php namespace Nickwest\EloquentForms\Test\unit;

use Illuminate\Support\Facades\Blade;

use Nickwest\EloquentForms\Form;
use Nickwest\EloquentForms\EloquentFormsServiceProvider;
use Nickwest\EloquentForms\Test\TestCase;

class EloquentFormsServiceProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        view()->addNamespace('fixtures', __DIR__.'/../fixtures/views');
    }

    public function test_getViewFromExpression_returns_the_view_name()
    {
        $this->assertEquals("'fixtures::include'", EloquentFormsServiceProvider::getViewFromExpression("'fixtures::include'"));
        $this->assertEquals("'fixtures::include'", EloquentFormsServiceProvider::getViewFromExpression("'fixtures::include', ['greeting' => 'hi']"));
    }

    public function test_eloquentforms_include_renders_the_view_when_it_exists()
    {
        $output = Blade::render("@eloquentforms_include('fixtures::include', ['greeting' => 'Hello'])");

        $this->assertStringContainsString('Included: Hello', $output);
    }

    public function test_eloquentforms_include_falls_back_to_the_default_namespace()
    {
        $output = Blade::render(
            "@eloquentforms_include('missing::form', ['Form' => \$Form, 'view_only' => false])",
            ['Form' => new Form()]
        );

        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('class="fields"', $output);
    }

    public function test_eloquentforms_component_renders_the_view_when_it_exists()
    {
        $output = Blade::render("@eloquentforms_component('fixtures::component')\nInside\n@endcomponent");

        $this->assertStringContainsString('Component[', $output);
        $this->assertStringContainsString('Inside', $output);
    }

    public function test_eloquentforms_component_falls_back_to_the_default_namespace()
    {
        $output = Blade::render(
            "@eloquentforms_component('missing::form', ['Form' => \$Form, 'view_only' => true])\n@endcomponent",
            ['Form' => new Form()]
        );

        $this->assertStringContainsString('<form', $output);
        $this->assertStringNotContainsString('submit-buttons', $output);
    }
}
