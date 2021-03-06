<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2015, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Realdom;

use Hoa\Math;

/**
 * Class \Hoa\Realdom\Date.
 *
 * Realistic domain: date.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Date extends Realdom
{
    /**
     * Realistic domain name.
     *
     * @const string
     */
    const NAME = 'date';

    /**
     * Constants that represent defined formats.
     * As examples, you could see \DateTime constants.
     *
     * @var array
     */
    protected static $_constants = null;

    /**
     * Realistic domain defined arguments.
     *
     * @var array
     */
    protected $_arguments        = [
        'RealdomString  format'    => 'c',
        'Integer        timestamp' => -1
    ];



    /**
     * Construct a realistic domain.
     *
     * @return  void
     */
    protected function construct()
    {
        if (null === static::$_constants) {
            $reflection         = new \ReflectionClass('DateTime');
            static::$_constants = $reflection->getConstants();
        }

        $constants               = &static::$_constants;
        $this['format']['value'] = preg_replace_callback(
            '#__(\w+)__#',
            function (Array $matches) use (&$constants) {
                $c = $matches[1];

                if (!isset($constants[$c])) {
                    return $matches[0];
                }

                return $constants[$c];
            },
            $this['format']->getConstantValue()
        );

        if ($this['timestamp'] instanceof Constinteger &&
            -1 === $this['timestamp']->getConstantValue()) {
            $this['timestamp'] = new Constinteger(time());
        }

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @param   mixed  $q    Sampled value.
     * @return  boolean
     */
    protected function _predicate($q)
    {
        $format = $this['format']->getConstantValue();

        try {
            $datetime = \DateTime::createFromFormat($format, $q);
        } catch (\Exception $e) {
            return false;
        }

        return
            false !== $datetime &&
            $this['timestamp']->predicate(intval($datetime->format('U')));
    }

    /**
     * Sample one new value.
     *
     * @param   \Hoa\Math\Sampler  $sampler    Sampler.
     * @return  mixed
     */
    protected function _sample(Math\Sampler $sampler)
    {
        return date(
            $this['format']->getConstantValue(),
            $this['timestamp']->sample($sampler)
        );
    }
}
