# Flatbase

Flatbase is a flat file database written in PHP which aims to be:

- Lightweight
- Very easy to install, with minimal configuration
- Simple intuitive API
- Suitable for small data sets
- Suitable for testing & prototyping

Flatbase is *not* intended to be a replacement for "real" database engines. If you're storing sensitive data in a production environment then this probably isn't for you.

## Example Usage

```php
<?php

$storage = new Flatbase\Storage\Filesystem('/path/to/storage/dir');
$flatbase = new Flatbase\Flatbase($storage);

$flatbase->insert()->in('users')
    ->setValues(['name' => 'Adam', 'height' => "6'4"])
    ->execute();

$flatbase->read()->in('users')
    ->where('name', '=', 'Adam')
    ->first();
// (array) ['name' => 'Adam', 'height' => "6'4"]

```
    
## Installation

    composer require flatbase/flatbase
    
## Basics of Query Building

All functionality follows the same basic flow. Once you understand it, the API is really intuitive:

1) Create a `Query` object. The query object is specific to the type of query, and will be one of the following:

```php
$query = $flatbase->read(); // \Flatbase\Query\ReadQuery
$query = $flatbase->insert(); // \Flatbase\Query\InsertQuery
$query = $flatbase->update(); // \Flatbase\Query\UpdateQuery
$query = $flatbase->delete(); // \Flatbase\Query\DeleteQuery
```

2) Next, set the query properties. This will be one of, or a collection of `in`, `where` and `set`, depending on the type of query.

```php
$query = $flatbase->update();
$query->in('posts');
$query->where('post_id', '=', 5);
$query->setValues(['title' => 'New Post Title']);
```

> All `Query` methods implement a fluent interface, so you can chain method calls if you prefer:

3) Finally, `execute` the query. Executing a `ReadQuery` will return a `Flatbas\Collection` object; otherwise we return `void`.

```php
$query->execute();
```

> For `ReadQuery`'s, calling `$query->get()` is an alias of `$query->execute()`. `$query->first()` is also provided to return the first collection item, or `null` if the collection is empty.

## SQL Cheat Sheet

SQL Statement | Flatbase Query
--- | ---
`SELECT * FROM posts` | `$flatbase->read()->in('posts')->get();`
`SELECT * FROM posts LIMIT 0,1` | `$flatbase->read()->in('posts')->first();`
`SELECT * FROM posts WHERE id = 5` | `$flatbase->read()->in('posts')->where('id', '==', 5)->get();`
`SELECT * FROM posts WHERE views > 500` | `$flatbase->read()->in('posts')->where('views', '>', 500)->get();`
`SELECT * FROM posts WHERE views > 50 AND id = 5` | `$flatbase->read()->in('posts')->where('views', '>', 50)->where('id', '==', '5')->get();`
`UPDATE posts SET title = 'Foo' WHERE content = 'bar'` | `$flatbase->update()->in('posts')->setValues(['title' => 'var'])->where('content', '==', 'bar')->execute();`
`DELETE FROM posts WHERE id = 2` | `$flatbase->delete()->in('posts')->where('id', '==', 2)->execute();`
`INSERT INTO posts SET title='Foo', content='Bar'` | `$flatbase->insert()->in('posts')->setValues(['title' => 'Foo', 'content' => 'Bar')->execute();`

## Why?

What are some of the advantages of a flat file database?

#### It's really easy to get started
Just add `flatbase/flatbase` to your `composer.json` and you're rolling. No need for any other services running.

#### It's not a relational database
Flatbase is schema-less, so you don't have to worry about defining a schema, writing migration scripts, or any of that other boring stuff. Just instantiate `Flatbase` and start giving it data. This is particularly useful when developing/prototyping new features.

#### Store plain old PHP objects
Data is stored in a native PHP serialized array using [PHPSerializer](https://github.com/adamnicholson/php-serializer). This means that you can store plain old PHP objects straight in the database:

```php
$flatbase->insert()->in('users')->setValues([
    'id' => 1,
    'name' => 'Adam',
    'added' => new DateTime()
])->execute();

$record = $flatbase->read()->in('users')->where('id', '==', 1)->first();
var_dump($record['added']); // DateTime
```
    
It also means that you can, at any point, easily unserialize() your data without having to go through Flatbase if you wish. 
> Note: Althought serializing is possible, be careful when using this in production. Remember that if you serialize an object, and then later on delete or move the class it was an instance of, you won't be able to un-serialze it. Storing scalar data is always a safer alternative.
    
#### It isn't actually that slow

Ok, that's a bit of a baiting title. Some operations are remarkably quick considering this is a flat file database. On a mediocre Ubuntu desktop development environment it can process around 50,000 "inserts" in 1 second. No, that is still nowhere near a database like MySQL or Mongo, but it's a hell of a lot more than most people need. 

Reading data out is certainly a lot slower, and although there's lots of places we can optimise, ultimately you'd need to accept this is never going to be a high performance solution for persistence.

## Author

Adam Nicholson - adamnicholson10@gmail.com

## Contributing

Contributions are welcome, and they can be made via GitHub issues or pull requests.

## License

Flatbase is licensed under the MIT License - see the `LICENSE.txt` file for details
