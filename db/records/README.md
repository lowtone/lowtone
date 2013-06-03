# Records

The Lowtone Library for WordPress includes a simple [ORM](http://en.wikipedia.org/wiki/Object-relational_mapping) system designed specifically with WordPress in mind.

## Create a record class

To use ORM a new class should be created extending the base class `lowtone\db\records\Record`.

```php
use lowtone\db\records\Record;

class Foo extends Record {

	const PROPERTY_ID = "foo_id",
		PROPERTY_TITLE = "title",
		PROPERTY_CONTENT = "content",
		PROPERTY_CREATED = "created";

}
```

The basic idea is that properties are defined using class constants. From those property definitions a schema is reverse-engineered when e.g. an instance of the class is created using `Record::__createSchema()` (a schema is only created once and is then stored for that specific class).

By default `Record::__createSchema()` will try to figure out the property types by the names of the class constants. E.g. a constant name matching `TIMESTAMP`, `DATETIME`, `CHANGED`, or `CREATED` will be of the DateTime-type, where the `CHANGED` and `CREATED` properties will default to the current time, `DATE` and `TIME` properties will respectively be of the date and time-type and properties ending with `_ID` will be of the numeric type and the first occurrence of an `_ID` property will become the record's primary key.

Manually creating a schema can be done by overwriting the `Record::__createSchema()` method in the extended class.

## Create storage

Creating a database table for a Record class can be done by calling `Record::__createStorage()`. This will create a `CREATE TABLE` query from the class's schema and execute it on the database.

```php
Foo::__createStorage();
```

The name for the table is created from the class name and is prefixed with the prefix as defined in the WordPress configuration and pluralized by adding an "s" at the end. With the default prefix this would result in `wp_foos` for the above example. It is also possible to define a custom table name by overwriting `Record::__getTable()`.

## Insert record

To store a new record in the database first a new instance of the class should be created. Then it can be stored by calling its `save()` method.

```php
$foo = new Foo(array(
		Foo::PROPERTY_TITLE => "Hello Record!",
		Foo::PROPERTY_CONTENT => "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
	));

$foo->save();
```

In the above example a record of the `Foo` class is created with a title and content defined using its constructor. These values are then stored using `$foo->save()`.

### Retrieving & updating properties

The `Record` class extends the `lowtone\types\objects\Object` class and therefore inherits its interface for property access. Initial properties can be set by supplying an array or object to the constructor. Properties can be accessed either by using the record instance as an array (like `$foo["foo_id"]`) or an object (like `$foo->foo_id`).

Updating multiple properties at once can be achieved by calling the record object as a function and supplying its new properties as an argument.

```php
$foo(array(
		Foo::PROPERTY_TITLE => "New Title!",
		Foo::PROPERTY_CONTENT => "New Content!"
	));
```

Using automatically generated methods for property access also works but only for properties that are defined in the schema. Calling a method for a nonexistent property (like `$foo->bar("New value for bar")`) will throw an `ErrorException`.

Another difference with the `Object` class is that getters and setters can be defined by the schema. A property of the DateTime-type will, for example, when a new value is assigned automatically try to convert it to a `lowtone\types\datetime\DateTime` object. This will happen regardless of how the property is accessed, either using the object as an array, as an object, or accessing the property using an automatically generated method.

A full description for the use of `Object` instances can be found [here](https://github.com/lowtone/lowtone/tree/master/types/objects).

## Fetch records

To retrieve records from the database there are some static functions defined for the `Record` class.

To retrieve all records for a specific class `Record::all()` should be used.

```php
$foos = Foo::all();
```

The `Record::all()` method takes a single `$options` parameter which could be used to change its behavior (e.g. limit its results or match specific conditions).

To look for records matching specific values `Record::find()` could be used. This method takes an array of properties and values and returns a `Collection` of records matching all of those conditions. A second parameter can be supplied to define additional options.

```php
$foos = Foo::find(array(
		Foo::PROPERTY_TITLE => "Hello Record!"
	));
```

The above example will look for records having a title that matches "Hello Record!".

To retrieve records that match a specific primary key `Record::findById()` should be used.

```php
$foo = Foo::findById(1);
```

The above example returns a single `Foo` instance with a primary key matching `1` or `FALSE` if such a record can't be found.

`Record::findById()` could also be used to retrieve a collection of multiple records. A `Collection` instance will be returned when either multiple parameters for ID values are supplied, the ID value or values are supplied using an array, or a combination of those.

```php
$foos = Foo::findById(1, 2, array(3, 4));
```

### Find by everything

Use `Record::findBy<property_name>()` to use any of the Record's properties to find matching records. If a property name is supplied using camelcase it is converted to an underscored string. E.g. `Foo::findByFooId()` will look for records where `foo_id` matches any of the given values.

```php
$foos = Foo::findByTitle("Hello Record!", array("Hello Bird!", "Hello Dog!"));
```

The above example will return a Collection of Foos where the title matches either "Hello Record!", "Hello Bird!", or "Hello Dog!".

### A note on find by results

Remember that when a single argument is supplied to any of the find by methods a single instance is returned (and not a collection which would be the case when multiple arguments are supplied). This wouldn't be a problem with ID values since they'd probably be unique anyway but could cause some confusion with non-unique values like in the example below:

```php
$foos = Foo::findByTitle("Hello Bird!");
```

Here you might expect `$foos` to be a collection of all `Foo` instances where the title matches "Hello Bird!" but it actually will be only the first single `Foo` instance that matches this condition.

To make sure the result will be a collection multiple arguments should be supplied or, for the above example where there is just one value to search for, the argument should be supplied as an array like the example below:

```php
$foos = Foo::findByTitle(array("Hello Bird!"));
```

## Deleting a record

A record can be deleted from the database using its `delete()` method. This will remove any record matching the primary keys as set for the called object from the database.