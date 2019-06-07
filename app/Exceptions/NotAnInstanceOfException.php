<?php

namespace App\Exceptions;

use Exception;

class NotAnInstanceOfException extends Exception
{
    public function __construct(object $object, string $instanceOf)
    {
        $className = get_class($object);
        parent::__construct("$className should be an instance of $instanceOf.");
    }

    public static function check($object, $instanceOf)
    {
        // $object should be an object
        $object = is_string($object) ? app($object) : $object;

        // $instanceOf should also be an array os strings
        $io = $instanceOf;
        $ios = !is_array($io) ? [$io] : $io;

        // loop on $ios
        foreach ($ios as $io) {
            if ( ! ($object instanceof $io) ) {
                throw new static($object, $io);
            }
        }
    }
}
