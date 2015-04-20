<?php namespace Phasty\Front\Controllers;

use Phalcon\Commander\CommanderTrait;
use Phalcon\Paginator\Adapter\Model;
use Phalcon\Paginator\Pager;
use Phasty\Common\Models\Categories;
use Phasty\Common\Models\Products;
use Phasty\Common\Repo\Brands\CacheBrands;
use Phasty\Common\Repo\Categories\CacheCategories;
use Phasty\Common\Commands\SearchProductsCommand;

class CatalogController extends ControllerBase
{
    use CommanderTrait;
    protected $categories;
    protected $brands;
    protected $maxPrice;
    protected $category;

    protected function initialize()
    {
        $this->brands = new CacheBrands();
        $this->categories = new CacheCategories();
        parent::initialize();
        $this->tag->setTitle('Catalog | E-Shopper');
    }

    /**
     * @param string slug
     * @return view
     */
    public function indexAction($slug = null)
    {
        $categories = $this->categories->all()->categories;
        $brands = $this->brands->all()->brands;
        $this->view->setVars([
            'categories' => $this->jsonEncode($categories),
            'brands' => $this->prepareForSelectBox($brands),
            'pager' => $this->getPager($slug),
            'maxPrice' => $this->maxPrice,
            'category' => $this->category,
        ]);
    }

    public function searchAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
            //$input =  filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $input = $this->request->getPost();
            if(is_array($input) && !empty(array_filter($input)) && $this->persistent->slug){
                $input['slug'] = $this->persistent->slug;
                $pager = $this->execute(SearchProductsCommand::class, $input);
                echo $this->view->partial('catalog/_list', ['pager' => $pager]);
            }
        }
    }

    protected function prepareForSelectBox($array = [])
    {
        $temp = [];
        foreach ($array as $key => $value) {
            $temp[$value['id']] = $value['name'];
        }
        return $temp;
    }

    protected function getPager($slug = null)
    {
        $this->persistent->slug = $slug;
        $numberPage = $this->request->getQuery("page", "int", 1);
        if (!$numberPage or $numberPage <= 0) {
            $numberPage = 1;
        }

        $this->category = $slug ? Categories::findFirst("slug = '$slug'") : null;
        $this->maxPrice = $this->category ? Products::getMaxPrice($this->category->id) : Products::getMaxPrice();

        $pager = new Pager(new Model([
            "data" => $this->category ? Products::find(["categoryId = :id:", 'bind' => ['id' => $this->category->id]]) : Products::find(),
            "limit" => 6,
            "page" => (int)$numberPage]),
            [
                'layoutClass' => 'Phalcon\Paginator\Pager\Layout\Bootstrap',
                'rangeLength' => 5,
                'urlMask' => '?page={%page_number}',
            ]);
        return $pager;
    }

}