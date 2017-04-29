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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, $this->getParentColumn());
    }

    /**
     * getParentAttribute - Eloquent attribute accessor.
     *
     * @param  mix $value
     * @return \App\Model | null
     */
    public function getParentAttribute($value)
    {
        if (!$value) {
            return $this->parent()->first();
        }
    }

    /**
     * childs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany(static::class, $this->getParentColumn());
    }

    /**
     * getChildsAttribute - Eloquent attribute accessor.
     *
     * @param  mix $value
     * @return \Illuminate\Database\Eloquent\Collection
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
        return !empty($this->parent()->first());
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
            $model = $model->parent()->first();
        }

        return $level;
    }

    /**
     * scopeSurface - local scope, The rows that doesn't have parent_id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
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
