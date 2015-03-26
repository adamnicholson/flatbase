# Flatbase

Flatbase is a flat file database written in PHP which aims to be:

- Lightweight
- Very easy to install, with minimal/no configuration
- Simple intuitive API
- Suitable for small data sets, low-load applications, and testing/prototyping

## Example Usage

```php
<?php

$storage = new Flatbase\Storage\Filesystem('/path/to/storage/dir');
$flatbase = new Flatbase\Flatbase($storage);

$flatbase->insert()->in('users')
    ->set(['name' => 'Adam', 'height' => "6'4"])
    ->execute();

$flatbase->read()->in('users')
    ->where('name', '=', 'Adam')
    ->first();
// (array) ['name' => 'Adam', 'height' => "6'4"]

```
    
## Installation

    composer require flatbase/flatbase

## Usage

### Reading

Fetch all the records from a collection:

```php
$flatase->read()->in('users')->get(); // Flatbase\Collection
```

Reading only data matching a certain criteria:

```php
$flatbase->read()->in('users')->where('id', '==', '5')->get();
```

We support all the comparison operators you'd expect:

- `=`
- `!=`
- `==`
- `!==`
- `<`
- `>`

You can chain as many `where()` conditions as you like:

```php
$flatbase->read()
    ->in('users')
    ->where('age', '<', 40)
    ->where('age', '>', 20)
    ->where('country', '==', 'UK')
    ->get();
```

Limit the returned records:

```php
$flatbase->read()->in('users')->limit(10)->get(); // Get the first 10 records
$flatbase->read()->in('users')->skip(5)->limit(10)->get(); // Skip the first 5, then return the next 10
$flatbase->read()->in('users')->first(); // Get the first record
```

Sort the records:

```php
$flatbase->read()->in('users')->sort('age')->get(); // Sort by age in ascending order
$flatbase->read()->in('users')->sortDesc('age')->get(); // Sort by age in descending order
```

Just get a count of records:

```php
$flatbase->read()->in('users')->count();
```

### Deleting

Delete all records in a collection:

```php
$flatbase->delete()->in('users')->execute();
```

Or just some records:

```php
$flatbase->delete()->in('users')->where('id', '==', 5)->execute();
```

### Inserting

```php
$flatbase->insert()->in('users')->set([
    'name' => 'Adam',
    'country' => 'UK',
    'language' => 'English'
])->execute();
```

### Updating

Update all records in a collection:

```php
$flatbase->update()->in('users')->set(['country' => 'IE',])->execute();
```

Or just some records:

```php
$flatbase->update()
    ->in('users')
    ->set(['country' => 'IE',])
    ->where('name', '==', 'Adam')
    ->execute();
```


## SQL Cheat Sheet

SQL Statement | Flatbase Query
--- | ---
`SELECT * FROM posts` | `$flatbase->read()->in('posts')->get();`
`SELECT * FROM posts LIMIT 0,1` | `$flatbase->read()->in('posts')->first();`
`SELECT * FROM posts WHERE id = 5` | `$flatbase->read()->in('posts')->where('id', '==', 5)->get();`
`SELECT * FROM posts WHERE views > 500` | `$flatbase->read()->in('posts')->where('views', '>', 500)->get();`
`SELECT * FROM posts WHERE views > 50 AND id = 5` | `$flatbase->read()->in('posts')->where('views', '>', 50)->where('id', '==', '5')->get();`
`UPDATE posts SET title = 'Foo' WHERE content = 'bar'` | `$flatbase->update()->in('posts')->set(['title' => 'var'])->where('content', '==', 'bar')->execute();`
`DELETE FROM posts WHERE id = 2` | `$flatbase->delete()->in('posts')->where('id', '==', 2)->execute();`
`INSERT INTO posts SET title='Foo', content='Bar'` | `$flatbase->insert()->in('posts')->set(['title' => 'Foo', 'content' => 'Bar')->execute();`

## Command Line Interface

Flatbase includes a command line interface `flatbase` for quick manipulation of data outside of your application.

```bash
php vendor/bin/flatbase read users
```

### Installation

To use the CLI, you must define the path to your storage directory. This can either be done with a `flatbase.json` file in the directory you call flatbase from (usually your application root):

```json
{
    "path": "some/path/to/storage"
}
```

Alternatively, simply include the `--path` option when issuing commands. Eg:

```bash
php vendor/bin/flatbase read users --path="some/path/to/storage"
```


### Demo
<img src="https://raw.githubusercontent.com/adamnicholson/flatbase/master/cli-demo.gif" />

### Usage

```bash
# Get all records
php flatbase read users

# Get the first record in a collection
php flatbase read users --first

# Count the records in a collection
php flatbase read users --count

# Get users matching some where clauses
php flatbase read users --where "name,==,Adam" --where "age,<,30"

# Update some record(s)
php flatbase update users --where "age,<,18" --where "age,>,12" ageGroup=teenager

# Insert a new record
php flatbase insert users name=Adam age=25 country=UK

# Delete some record(s)
php flatbase delete users --where "name,==,Adam"
```

For more info on the CLI, use one of the `help` commands

```bash
php flatbase help
php flatbase read --help
php flatbase update --help
php flatbase insert --help
php flatbase delete --help
```

## Why us a flat file database?

What are some of the advantages of a flat file database?

#### It's really easy to get started
Just add `flatbase/flatbase` to your `composer.json` and you're rolling. No need for any other services running.

#### It's schema-less
You don't have to worry about defining a schema, writing migration scripts, or any of that other boring stuff. Just instantiate `Flatbase` and start giving it data. This is particularly useful when developing/prototyping new features.

#### Store plain old PHP objects
Data is stored in a native PHP serialized array using [PHPSerializer](https://github.com/adamnicholson/php-serializer). This means that you can store plain old PHP objects straight in the database:

```php
$flatbase->insert()->in('users')->set([
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
