<?php

namespace App\Support\Traits;

trait ModelSelfRelation
{
    // protected $selfReferenceColumn = 'parent_id';

    public static function bootModelSelfRelation()
    {
        static::saved(function ($model) {
            // "parent_id" is must not "id" of itself.
            if ($model->id == $model->getParentId()) {
                $parentColumn = $model->getParentColumn();
                $model->$parentColumn = null;
                $model->save();
            }
        });
    }

    /**
     * parent
     *
     * @return App\Model | null
     */
    public function parent()
    {
        if ($parent_id = $this->getParentId()) {
            return static::find($parent_id);
        }

        return null;
    }

    /**
     * getParentAttribute - Eloquent attribute accessor.
     *
     * @param  mix $value
     * @return void
     */
    public function getParentAttribute($value)
    {
        if (!$value) {
            return $this->parent();
        }
    }

    /**
     * childs
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function childs()
    {
        $parentColumn = $this->getParentColumn();

        return static::where($parentColumn, $this->id);
    }

    /**
     * getChildsAttribute - Eloquent attribute accessor.
     *
     * @param  mix $value
     * @return void
     */
    public function getChildsAttribute($value)
    {
        if (!$value) {
            return $this->childs()->get();
        }
    }

    /**
     * hasParent
     *
     * @return bool
     */
    public function hasParent()
    {
        return !empty($this->parent());
    }

    /**
     * hasChild
     *
     * @return bool
     */
    public function hasChild()
    {
        return $this->childs()->count() > 0;
    }

    /**
     * getNestedLevel
     *
     * @return integer
     */
    public function getNestedLevel()
    {
        $level = 0;
        $model = $this;

        while ($model->hasParent()) {
            $level++;
            $model = $model->parent();
        }

        return $level;
    }

    /**
     * scopeSurface - local scope, The rows that doesn't have parent_id.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSurface($query)
    {
        return $query->where($this->getParentColumn(), null);
    }

    protected function getParentId()
    {
        $parentColumn = $this->getParentColumn();

        return $this->$parentColumn;
    }

    protected static function getParentColumn()
    {
        return (new static)->selfReferenceColumn ?? 'parent_id';
    }
}
