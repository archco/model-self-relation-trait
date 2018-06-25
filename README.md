# ModelSelfRelation

The trait for self relationship for an eloquent model.

## Usage

Database table schema

``` sql
CREATE TABLE `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  `parent_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `post_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
);
```

Eloquent model

``` php
namespace App;

use App\Support\Traits\ModelSelfRelation;

class Comment extends Model
{
    use ModelSelfRelation;

    // if you want set self-reference column. default is "parent_id".
    protected $selfReferenceColumn = 'parent_id';
}
```

## API

### parent

Access parent row.

``` php
$model->parent()->first();
// or
$model->parent;
```

### children

Access child rows.

``` php
$model->children()->get();
// or
$model->children;
```

### boolean

``` php
$model->hasParent();

$model->hasChild();
```

### getNestedLevel

Get depth level.

``` php
$model->getNestedLevel(); // integer.
```

### surface

Query scope. Retrieves rows that doesn't has parent.

``` php
Model::surface()->get();
```
