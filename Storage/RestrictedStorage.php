<?php

/*
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace YassineGuedidi\Facebook\Storage;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Storage wrapper that restrict persistable data
 *
 * @author Yassine Guedidi <yassine@guedidi.com>
 */
class RestrictedStorage implements StorageInterface
{
    /**
     * All supported keys
     *
     * @var array
     */
    protected static $supportedKeys = array('state', 'code', 'access_token', 'user_id');

    /**
     * The storage to restrict
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     * A logger used to log unauthorized keys usage
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param StorageInterface $storage The storage to restrict
     * @param LoggerInterface  $logger  Optional logger
     *
     * @throws InvalidArgumentException
     */
    public function __construct(StorageInterface $storage, LoggerInterface $logger = null)
    {
        $this->storage = $storage;
        $this->logger  = $logger ?: new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistentData($key, $value)
    {
        if (!in_array($key, static::$supportedKeys)) {
            $this->logger->error('Unsupported key passed to setPersistentData.');
        } else {
            $this->storage->setPersistentData($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentData($key, $default = false)
    {
        if (!in_array($key, static::$supportedKeys)) {
            $this->logger->error('Unsupported key passed to getPersistentData.');

            return $default;
        }

        return $this->storage->getPersistentData($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function clearPersistentData($key)
    {
        if (!in_array($key, static::$supportedKeys)) {
            $this->logger->error('Unsupported key passed to clearPersistentData.');
        } else {
            $this->storage->clearPersistentData($key);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAllPersistentData()
    {
        foreach (static::$supportedKeys as $key) {
            $this->storage->clearPersistentData($key);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->storage->getNamespace();
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        $this->storage->setNamespace($namespace);

        return $this;
    }
}
