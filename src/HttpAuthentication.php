<?php
declare(strict_types = 1);

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
    public function __construct($users)
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
     */
    public function realm(string $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Set the attribute name to store the user name.
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }
}
