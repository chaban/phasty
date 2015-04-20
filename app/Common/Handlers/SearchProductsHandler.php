<?php namespace Phasty\Common\Handlers;

use Phalcon\Commander\CommandHandler;
use Phalcon\Mvc\User\Plugin;
use Phasty\Common\Models\Categories;
use Phalcon\Paginator\Adapter\Model;
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
        if (!$category) {
            return false;
        }
        $query = $this->getSearchCriteria($category->id, $command);
        $pager = new Pager(new Model([
            "data" => $query->getQuery()->execute(),
            "limit" => 6,
            "page" => (int)$command->page]),
            [
                'layoutClass' => 'Phalcon\Paginator\Pager\Layout\BootstrapAjax',
                'rangeLength' => 5,
                'urlMask' => '{%page_number}',
            ]);
        return $pager;
    }

    /**
     * @param int $categoryId
     * @param stdObject $command
     * @return \Phalcon\Mvc\Model\Query\BuilderInterface
     */
    protected function getSearchCriteria($categoryId = null, $command)
    {
        $query = $this->modelsManager->createBuilder()->from('Phasty\Common\Models\Products')
            ->where("categoryId = '$categoryId'");
        if (!empty($command->brands)) {
            $query->inWhere('brandId', $command->brands);
        }
        if (!empty($command->price)) {
            $min = (int)($command->price["'min'"]);
            $max = (int)($command->price["'max'"]);
            $query->andWhere("price >= '$min' and price <= '$max'");
        }
        if (!empty($command->attributes)) {
            $match = '';
            foreach ($command->attributes as $key => $value) {
                $value = preg_replace("/[^a-zA-Z0-9]+/", "", $value);
                $match .= $value . ' , ';
            }
            $query->andWhere("MATCH(shortDescription) AGAINST('$match')");
        }
        return $query;
    }

}