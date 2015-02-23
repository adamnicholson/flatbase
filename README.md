# Flatbase

> In active development. Use with caution

Flatbase is a flat file database written in PHP which aims to be:

- Lightweight
- Very easy to install, with minimal configuration
- Simple intuitive API
- Suitable for small data sets
- Suitable for testing or prototyping

Flatbase is *not* intended to be a replacement for "real" database engines. If you're storing sensitive data in a production environment then this probably isn't for you.

## Basic Usage

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
    
    $flatbase->read()->in('users')->count();
    // (int) 1
    
    $flatbase->update()->in('users')->where('age', '==', "6'4")->setValues(['name' => 'Joe'])->execute();
    
    $flatbase->delete()->in('users')->execute();
    
    
## Installation

@todo
    
## The syntax

All Flatbase features follow the same API:

    <?php

    // Create a query object with either read(), update(), delete() or insert()
    $query = $flatbase->read();
    
    // Set the collection we're working with. in() is an alias of setCollection()
    $query->in('users');
    
    // Optionally add some where clauses - as many as you like.
    $query->where('user_id', '=', 1);
    $query->where('age', '<', 28);
    
    // If this were an update or insert, add some values
    // $query->setValues(['name' => 'Adam']);
    
    // Execute the query. $query->get() is an alias of $query->execute();
    // This returns an ArrayObject instance
    $collection = $query->get();
    
    


## Why?

What are some of the advantages of a flat file database?

#### It's really easy to get started
Just add `flatbase/flatbase` to your `composer.json` and you're rolling. No need for any other services running.

#### No need to write migrations
Flatbase is schema-less, so you don't have to worry about writing migration scripts. This is particularly useful when developing/prototyping new features if you aren't exactly sure what data is going to be required.

#### Store plain old PHP objects
Data is stored in a native PHP serialized array using [PHPSerializer](https://github.com/adamnicholson/php-serializer). This means that you can store plain old PHP objects straight in the database:

    <?php

    $flatbase->insert()->in('users')->setValues([
        'id' => 1,
        'name' => 'Adam',
        'added' => new DateTime()
    ])->execute();
    
    $record = $flatbase->read()->in('users')->where('id', '==', 1)->first();
    var_dump($record['added']); // DateTime
    
It also means that you can, at any point, easily unserialize() your data without having to go through Flatbase if you wish. 
    
#### It isn't actually that slow

Ok, that's a bit of a baiting title. Some operations are remarkably quick considering this is a flat file database. On a mediocre Ubuntu desktop development environment it can process around 50,000 "inserts" in 1 second. No, that is still nowhere near a database like MySQL or Mongo, but it's a hell of a lot more than most people need. 

Reading data out is certainly a lot slower, and although there's lots of places we can optimise, ultimately you'd need to accept this is never going to be a high performance solution for persistence.

## Author

Adam Nicholson - adamnicholson10@gmail.com

## Contributing

Contributions are welcome, and they can be made via GitHub issues or pull requests.

## License

Flatbase is licensed under the MIT License - see the `LICENSE.txt` file for details