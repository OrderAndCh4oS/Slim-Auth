<?php

namespace Oacc\Validation;

use Doctrine\Common\EventSubscriber;
use Oacc\Entity\User;
use Oacc\Error\Error;
use Oacc\Validation\Exceptions\ValidationException;

class UserValidation implements EventSubscriber
{
    public function validate(User $user)
    {
        if (empty($user->getUsername())) {
            Error::setError('username', 'Please enter a username');
        }
        if (strlen($user->getUsername()) > 80) {
            Error::setError('username', 'Username is too long');
        }
        if (preg_match('/[^A-Za-z0-9_-]/', $user->getUsername()) && !empty($user->getUsername())) {
            Error::setError('username', 'Username can only contain letters, numbers, underscores and hyphens');
        }
        if (empty($user->getEmailAddress())) {
            Error::setError('email', 'Please enter an email address');
        }
        if (!filter_var($user->getEmailAddress(), FILTER_VALIDATE_EMAIL)) {
            Error::setError('email', 'Please enter a valid email address');
        }
        if (empty($user->getPlainPassword())) {
            Error::setError('password', 'Please enter a password');
        }
        if (Error::hasErrors()) {
            throw new ValidationException();
        }
    }

    public function prePersist(User $user)
    {
        $this->validate($user);
    }

    public function preUpdate(User $user)
    {
        $this->validate($user);
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
}