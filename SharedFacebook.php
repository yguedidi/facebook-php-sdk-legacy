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

namespace Facebook;

/**
 * Extends the Facebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class SharedFacebook extends Facebook
{
    const FBSS_COOKIE_NAME = 'fbss';

    // We can set this to a high number because the main session
    // expiration will trump this.
    const FBSS_COOKIE_EXPIRE = 31556926; // 1 year

    // Stores the shared session ID if one is set.
    protected $sharedSessionID;

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        parent::__construct($config);

        $cookie_name = $this->getSharedSessionCookieName();
        if (isset($_COOKIE[$cookie_name])) {
            $data = $this->parseSignedRequest($_COOKIE[$cookie_name]);
            if ($data && !empty($data['domain']) &&
                self::isAllowedDomain($this->getHttpHost(), $data['domain'])) {
                // good case
                $this->sharedSessionID = $data['id'];

                // re-load the persisted state, since parent
                // attempted to read out of non-shared cookie
                $state = $this->getPersistentData('state');
                if (!empty($state)) {
                    $this->state = $state;
                } else {
                    $this->state = null;
                }

                return;
            }
            // ignoring potentially unreachable data
        }
        // evil/corrupt/missing case
        $base_domain = $this->getBaseDomain();
        $this->sharedSessionID = md5(uniqid(mt_rand(), true));
        $cookie_value = $this->makeSignedRequest(
            array(
                'domain' => $base_domain,
                'id' => $this->sharedSessionID,
            )
        );
        $_COOKIE[$cookie_name] = $cookie_value;
        if (!headers_sent()) {
            $expire = time() + self::FBSS_COOKIE_EXPIRE;
            setcookie($cookie_name, $cookie_value, $expire, '/', '.' . $base_domain);
        } else {
            // @codeCoverageIgnoreStart
            $this->logger->error(
                'Shared session ID cookie could not be set! You must ensure you ' .
                'create the Facebook instance before headers have been sent. This ' .
                'will cause authentication issues after the first request.'
            );
            // @codeCoverageIgnoreEnd
        }
    }

    protected function deleteSharedSessionCookie()
    {
        $cookie_name = $this->getSharedSessionCookieName();
        unset($_COOKIE[$cookie_name]);
        $base_domain = $this->getBaseDomain();
        setcookie($cookie_name, '', 1, '/', '.' . $base_domain);
    }

    protected function getSharedSessionCookieName()
    {
        return self::FBSS_COOKIE_NAME . '_' . $this->getAppId();
    }

    /**
     * {@inheritdoc}
     */
    protected function clearAllPersistentData()
    {
        parent::clearAllPersistentData();

        if ($this->sharedSessionID) {
            $this->deleteSharedSessionCookie();
        }
    }

    protected function constructSessionVariableName($key)
    {
        $name = parent::constructSessionVariableName($key);

        if ($this->sharedSessionID) {
            return $this->sharedSessionID . '_' . $name;
        }

        return $name;
    }

    protected static function isAllowedDomain($big, $small)
    {
        if ($big === $small) {
            return true;
        }

        return self::endsWith($big, '.' . $small);
    }

    protected static function endsWith($big, $small)
    {
        $len = strlen($small);
        if ($len === 0) {
            return true;
        }

        return substr($big, -$len) === $small;
    }
}
