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
        $this->categories = new CacheCategories();
        $this->brands = new CacheBrands();
        parent::initialize();
        $this->tag->setTitle('Catalog | Phasty');
    }

    /**
     * @param string slug
     * @return view
     */
    public function indexAction($slug = null)
    {
        $categories = $this->categories->all()->categories;
        $this->view->setVars([
            'categories' => $this->jsonEncode($categories),
            'pager' => $this->getPager($slug),
            'maxPrice' => $this->maxPrice,
            'category' => $this->category,
            'brands' => $this->prepareBrandsForSelectBox()
        ]);
    }

    public function searchAction()
    {
        if ($this->request->isAjax()) {
            $this->view->disable();
            //$input =  filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $input = $this->request->getPost();
            if (is_array($input) && !empty(array_filter($input)) && $this->persistent->slug) {
                $input['slug'] = $this->persistent->slug;
                $pager = $this->execute(SearchProductsCommand::class, $input);
                echo $this->view->partial('catalog/_list', ['pager' => $pager]);
            }
        }
    }

    protected function prepareBrandsForSelectBox()
    {
        $brands = [];
        $temp = [];
        if ($this->category) {
            $brands = $this->brands->getBrandsForCategory($this->category->id);
        }
        if (!empty($brands)) {
            foreach ($brands as $key => $value) {
                $temp[$value['id']] = $value['name'];
            }
            return $temp;
        }
    }

    protected function getPager($slug = null)
    {
        $this->persistent->slug = $slug;
        $numberPage = $this->request->getQuery("page", "int", 1);

        $this->category = $slug ? Categories::findFirst("slug = '$slug'") : null;
        $this->maxPrice = $this->category ? Products::getMaxPrice($this->category->id) : Products::getMaxPrice();

        return $this->execute(SearchProductsCommand::class, ['page' => $numberPage, 'slug' => $slug]);
    }

}