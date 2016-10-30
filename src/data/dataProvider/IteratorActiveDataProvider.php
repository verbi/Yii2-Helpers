<?php
namespace verbi\yii2Helpers\data\dataProvider;
use yii\helpers\ArrayHelper;

/**
 * IteratorDataProvider implements a data provider based on an iterator or generator.
 *
 * The [[allModels]] property contains all data models that may be sorted and/or paginated.
 * IteratorDataProvider will provide the data after sorting and/or pagination.
 * You may configure the [[sort]] and [[pagination]] properties to
 * customize the sorting and pagination behaviors.
 *
 * Elements in the [[allModels]] iterator may be either objects (e.g. model objects)
 * or associative arrays (e.g. query results of DAO).
 * Make sure to set the [[key]] property to the name of the field that uniquely
 * identifies a data record or false if you do not have such a field.
 *
 * Compared to [[ArrayDataProvider]], IteratorDataProvider will be more efficient
 * because it does not need to have [[allModels]] ready.
 *
 * IteratorDataProvider may be used in the following way:
 *
 * ```php
 * $query = new Query;
 * $provider = new IteratorDataProvider([
 *     'iterator' => $query->genAll(),
 *     'sort' => [
 *         'attributes' => ['id', 'username', 'email'],
 *     ],
 *     'pagination' => [
 *         'pageSize' => 10,
 *     ],
 * ]);
 * // get the posts in the current page
 * $posts = $provider->getModels();
 * ```
 *
 * Note: if you want to use the sorting feature, you must configure the [[sort]] property
 * so that the provider knows which columns can be sorted.
 *
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class IteratorActiveDataProvider extends \yii\data\BaseDataProvider
{
    use \verbi\yii2Helpers\traits\ComponentTrait;
    /**
     * @var string|callable the column that is used as the key of the data models.
     * This can be either a column name, or a callable that returns the key value of a given data model.
     * If this is not set, the index of the [[models]] array will be used.
     * @see getKeys()
     */
    public $key;
    
    /**
     * @var iterator the data that is not paginated or sorted. When pagination is enabled,
     * this property usually contains more elements than [[models]].
     * The array elements must use zero-based integer keys.
     */
    public $iterator;
    
    /**
     *
     * @var int the total count of rectods in the generator, if countable. If the iterator
     * is not countable, this value will be ignored.
     */
    public $totalCount;
    
    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $models = [];
        if ($this->iterator === null) {
            return [];
        }
        if (($sort = $this->getSort()) !== false) {
            $this->sortModels($this->iterator, $sort);
        }
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ( $pagination->getPageSize() > 0 ) {
                $i = 0;
                foreach( $this->iterator as $key => $item ) {
                    if( $i >= $pagination->getOffset() ) {
                        $models[$key] = $item;
                    }
                    $i++;
                    if( $i >= $pagination->getOffset() + $pagination->getLimit() ) {
                        break;
                    }
                }
            }
        }
        if(!sizeof($models)) {
            $models = iterator_to_array($this->iterator);
        }
        return $models;
    }
    
    /**
     * @inheritdoc
     */
    protected function prepareKeys($models)
    {
        if ($this->key !== null) {
            $keys = [];
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }
            return $keys;
        } else {
            return array_keys($models);
        }
    }
    
    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        return $this->totalCount;
    }
    
    /**
     * Sorts the data models according to the given sort definition
     * @param array $models the models to be sorted
     * @param Sort $sort the sort definition
     * @return array the sorted data models
     */
    protected function sortModels($models, $sort)
    {
        $orders = $sort->getOrders();
        if (!empty($orders)) {
            ArrayHelper::multisort($models, array_keys($orders), array_values($orders));
        }
        return $models;
    }
}