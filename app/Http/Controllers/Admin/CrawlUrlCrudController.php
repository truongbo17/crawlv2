<?php

namespace App\Http\Controllers\Admin;

use App\Crawler\Enum\DataStatus;
use App\Models\CrawlUrl;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CrawlUrlCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CrawlUrlCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(CrawlUrl::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/crawl-url');
        CRUD::setEntityNameStrings('crawl url', 'crawl urls');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'id',
                'type' => 'text'
            ],
            [
                'name' => 'site',
                'type' => 'url_reducer'
            ],
            [
                'name' => 'url',
                'type' => 'url_reducer',
            ],
            [
                'name' => 'data_status',
                'type' => 'select_from_array',
                'options' => array_flip(DataStatus::asArray()),
            ],
        ]);

        $this->crud->removeAllButtons();
    }
}
