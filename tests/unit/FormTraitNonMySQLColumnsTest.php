<?php namespace Nickwest\EloquentForms\Test\unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Nickwest\EloquentForms\FormTrait;
use Nickwest\EloquentForms\Test\TestCase;

/**
 * Covers the non-MySQL column introspection path (FormTrait::setColumnsFromOther),
 * which the rest of the suite never reaches because it runs against MySQL.
 */
class FormTraitNonMySQLColumnsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.sqlite_forms', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        config()->set('database.default', 'sqlite_forms');

        Schema::create('non_mysql_sample', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 50);
            $table->string('status')->default('active');
            $table->text('bio')->nullable();
            $table->integer('favorite_number')->nullable();
        });
    }

    public function test_columns_are_read_without_doctrine_on_sqlite()
    {
        $columns = (new SqliteSample())->getColumnsArray();

        $this->assertSame(['id', 'first_name', 'status', 'bio', 'favorite_number'], array_keys($columns));

        $this->assertSame('varchar', $columns['first_name']['type']);
        $this->assertSame('text', $columns['bio']['type']);
        $this->assertSame('int', $columns['favorite_number']['type']);
        $this->assertNull($columns['first_name']['values']);
    }

    public function test_string_defaults_are_unquoted()
    {
        $columns = (new SqliteSample())->getColumnsArray();

        $this->assertSame('active', $columns['status']['default']);
        $this->assertNull($columns['bio']['default']);
    }

    public function test_form_can_be_prepared_from_sqlite_columns()
    {
        $model = new SqliteSample();
        $model->prepareForm();

        $this->assertSame('active', $model->Form()->status->default_value);
    }
}

class SqliteSample extends Model
{
    use FormTrait;

    protected $table = 'non_mysql_sample';

    public function prepareForm()
    {
        $this->generateFormData();
    }
}
