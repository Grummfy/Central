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

namespace Hoa\Xml;

use Hoa\Stream;

/**
 * Class \Hoa\Xml\Read.
 *
 * Read a XML element.
 *
 * @copyright  Copyright © 2007-2015 Hoa community
 * @license    New BSD License
 */
class Read extends Xml implements Stream\IStream\In
{
    /**
     * Start the stream reader as if it is a XML document.
     *
     * @param   \Hoa\Stream\IStream\In  $stream                 Stream to read.
     * @param   bool                    $initializeNamespace    Whether we
     *                                                          initialize
     *                                                          namespaces.
     * @param   mixed                   $entityResolver         Entity resolver.
     * @return  void
     */
    public function __construct(
        Stream\IStream\In $stream,
        $initializeNamespace = true,
        $entityResolver      = null
    ) {
        parent::__construct(
            '\Hoa\Xml\Element\Read',
            $stream,
            $initializeNamespace,
            $entityResolver
        );

        return;
    }

    /**
     * Test for end-of-file.
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->getStream()->eof();
    }

    /**
     * Read n characters.
     *
     * @param   int     $length    Length.
     * @return  string
     * @throws  \Hoa\Xml\Exception
     */
    public function read($length)
    {
        return $this->getStream()->read($length);
    }

    /**
     * Alias of $this->read().
     *
     * @param   int     $length    Length.
     * @return  string
     */
    public function readString($length)
    {
        return $this->getStream()->readString($length);
    }

    /**
     * Read a character.
     *
     * @return  string
     */
    public function readCharacter()
    {
        return $this->getStream()->readCharacter();
    }

    /**
     * Read a boolean.
     *
     * @return  bool
     */
    public function readBoolean()
    {
        return $this->getStream()->readBoolean();
    }

    /**
     * Read an integer.
     *
     * @param   int     $length    Length.
     * @return  int
     */
    public function readInteger($length = 1)
    {
        return $this->getStream()->readInteger($length);
    }

    /**
     * Read a float.
     *
     * @param   int     $length    Length.
     * @return  float
     */
    public function readFloat($length = 1)
    {
        return $this->getStream()->readFloat($length);
    }

    /**
     * Read the XML tree as an array.
     *
     * @param   string  $argument    Not use here.
     * @return  array
     */
    public function readArray($argument = null)
    {
        return $this->getStream()->readArray($argument);
    }

    /**
     * Read a line.
     *
     * @return  string
     */
    public function readLine()
    {
        return $this->getStream()->readLine();
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @param   int  $offset    Offset.
     * @return  string
     */
    public function readAll($offset = 0)
    {
        return $this->getStream()->readAll($context);
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @param   string  $format    Format (see printf's formats).
     * @return  array
     */
    public function scanf($format)
    {
        return $this->getStream()->scanf($format);
    }

    /**
     * Read content as a DOM tree.
     *
     * @return  \DOMElement
     */
    public function readDOM()
    {
        return $this->getStream()->readDOM();
    }
}
