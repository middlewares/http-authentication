<?php

namespace Middlewares;

abstract class HttpAuthentication
{
    /**
     * @var array The available users
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
     * @param array $users [username => password]
     */
    public function __construct(array $users)
    {
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
