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
 * Implement this interface to create a storage for Facebook
 *
 * @author Yassine Guedidi <yassine@guedidi.com>
 */
interface StorageInterface
{
    /**
     * Stores the given ($key, $value) pair, so that future calls to
     * getPersistentData($key) return $value.
     * This call may be in another request, depending on the storage.
     *
     * @param string $key
     * @param array  $value
     *
     * @return StorageInterface This storage
     */
    public function setPersistentData($key, $value);

    /**
     * Get the data for $key, persisted by setPersistentData()
     *
     * @param string  $key     The key of the data to retrieve
     * @param boolean $default The default value to return if $key is not found
     *
     * @return mixed
     */
    public function getPersistentData($key, $default = false);

    /**
     * Clear the data with $key from the persistent storage
     *
     * @param  string           $key
     * @return StorageInterface This storage
     */
    public function clearPersistentData($key);

    /**
     * Clear all data from the persistent storage
     *
     * @return StorageInterface This storage
     */
    public function clearAllPersistentData();

    /**
     * Return the namespace used by the storage
     *
     * @return string|null The namespace
     */
    public function getNamespace();

    /**
     * Set the namespace used by the storage
     *
     * @param string|null $namespace The new namespace
     *
     * @return StorageInterface This storage
     */
    public function setNamespace($namespace);
}
