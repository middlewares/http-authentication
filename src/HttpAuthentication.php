<?php

namespace Middlewares;

use ArrayAccess;
use InvalidArgumentException;

abstract class HttpAuthentication
{
    /**
     * @var array|ArrayAccess The available users
     */
    protected $users;

    /**
     * @var string
     */
    protected $realm = 'Login';

    /**
     * @var string|null
     */
    protected $attribute;

    /**
     * Define de users.
     *
     * @param array|ArrayAccess $users [username => password]
     */
    public function __construct(array $users)
    {
        if (!is_array($users) && !($users instanceof ArrayAccess)) {
            throw new InvalidArgumentException(
                'The users argument must be an array or implement the ArrayAccess interface'
            );
        }

        $this->users = $users;
    }

    /**
     * Set the realm value.
     *
     * @param string $realm
     *
     * @return self
     */
    public function realm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Set the attribute name to store the user name.
     *
     * @param string $attribute
     *
     * @return self
     */
    public function attribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }
}
