# Records

## Create a record class

```php
use lowtone\db\records\Record;

class Foo extends Record {

	const PROPERTY_ID = "foo_id",
		PROPERTY_TITLE = "title",
		PROPERTY_CONTENT = "content",
		PROPERTY_CREATED = "created";

}
```

## Create storage

```php
Foo::__storageCreate();
```

## Insert record

```php
$foo = new Foo(array(
		Foo::PROPERTY_TITLE => "Hello Record!",
		Foo::PROPERTY_CONTENT => "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
	));

$foo->save();
```

## Fetch records

```php
$foos = Foo::all();
```

```php
$foos = Foo::find(array(
		Foo::PROPERTY_TITLE => "Hello Record!"
	));
```

```php
$foo = Foo::findById(1);
```

```php
$foos = Foo::findById(1, 2, array(3, 4));
```

### Find by everything

Use `Record::findBy<property_name>()` to use any of the Record's properties to find matching records. If a property name is supplied using camelcase it is converted to an underscored string. E.g. `Foo::findByFooId()` will look for records where `foo_id` matches any of the given values.

```php
$foos = Foo::findByTitle("Hello Record!", array("Hello Bird!", "Hello Dog!"));
```

The above example will return a Collection of Foos where the title matches either "Hello Record!", "Hello Bird!", or "Hello Dog!".