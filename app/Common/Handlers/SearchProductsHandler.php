<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Paginator\Adapter\QueryBuilder;
use Phasty\Common\Models\Categories;
use Phalcon\Paginator\Pager;

class SearchProductsHandler extends Plugin implements CommandHandler
{

    /**
     * Handle the command
     *
     * @param $command
     * @return mixed
     */
    public function handle($command)
    {
        $category = Categories::findFirst("slug = '$command->slug'");
        $categoryIds = [];
        $layoutClass = 'Phalcon\Paginator\Pager\Layout\Bootstrap';
        $urlMusk = '?page={%page_number}';
        if ($category) {
            $layoutClass = 'Phalcon\Paginator\Pager\Layout\BootstrapAjax';
            $urlMusk = '{%page_number}';
            $parents = $category->parents()->toArray();
            //get all attributes from parent categories
            foreach ($parents as $parent) {
                if ($parent['id'] == 1) {
                    continue;
                }
                $categoryIds[] = $parent['id'];
            }
            $categoryIds[] = $category->id;
        }
        $query = $this->getSearchCriteria($categoryIds, $command);
        $pager = new Pager(new QueryBuilder([
            "builder" => $query,
            "limit" => 6,
            "page" => (int)$command->page]),
            [
                'layoutClass' => $layoutClass,
                'rangeLength' => 5,
                'urlMask' => $urlMusk,
            ]);
        return $pager;
    }

    /**
     * @param array $categoryIds
     * @param stdObject $command
     * @return \Phalcon\Mvc\Model\Query\BuilderInterface
     */
    protected function getSearchCriteria($categoryIds = [], $command)
    {
        $query = $this->modelsManager->createBuilder()->from('Phasty\Common\Models\Products');
        if (!empty($categoryIds)) {
            $query->inWhere('categoryId', $categoryIds);
        }
        if (!empty($command->brands)) {
            $query->inWhere('brandId', $command->brands);
        }
        if (!empty($command->price)) {
            $min = (int)($command->price["'min'"]);
            $max = (int)($command->price["'max'"]);
            $query->andWhere("price >= '$min' and price <= '$max'");
        }
        if (!empty($command->attributes)) {
            //$match = '';
            foreach ($command->attributes as $key => $value) {
                $match = '';
                $value = preg_replace("/[^a-zA-Z0-9]+/", "", $value);
                $key = preg_replace("/[^a-zA-Z0-9]+/", "", $key);
                $match .= $key.$value;
                $query->andWhere("MATCH(attributeValues) AGAINST('$match')");
            }
            //$query->andWhere("MATCH(attributeValues) AGAINST('$match')");
        }
        //$this->utils->var_dump($query->getPhql());die;
        return $query;
    }

}