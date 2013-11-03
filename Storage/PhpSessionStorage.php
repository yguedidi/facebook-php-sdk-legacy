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

namespace Facebook\Storage;

/**
 * A Facebook storage implementation uses PHP sessions
 *
 * @author Yassine Guedidi <yassine@guedidi.com>
 */
class PhpSessionStorage implements StorageInterface
{
    /**
     * Storage namespace
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }

        $this->setNamespace(null);
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentData($key, $default = false)
    {
        if (empty($this->namespace)) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        } else {
            return isset($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : $default;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistentData($key, $value)
    {
        if (empty($this->namespace)) {
            $_SESSION[$key] = $value;
        } else {
            $_SESSION[$this->namespace][$key] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearPersistentData($key)
    {
        if (empty($this->namespace)) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        } else {
            if (isset($_SESSION[$this->namespace][$key])) {
                unset($_SESSION[$this->namespace][$key]);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAllPersistentData()
    {
        if (empty($this->namespace)) {
            $_SESSION = array();
        } else {
            $_SESSION[$this->namespace] = array();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        $this->namespace = (string) $namespace;

        if (!empty($this->namespace) && !isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = array();
        }

        return $this;
    }
}
