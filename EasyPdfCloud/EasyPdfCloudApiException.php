<?php
/*
 * The MIT License
 *
 * Copyright 2016 BCL Technologies.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Bcl\EasyPdfCloud;

class EasyPdfCloudApiException extends \Exception
{
    private $statusCode;
    private $reasonPhrase;
    private $reasonDetail;

    //////////////////////////////////////////////////////////////////

    public function __construct($statusCode, $reasonPhrase, $reasonDetail = null, $code = 0, Exception $previous = null)
    {
        $message = static::toString($statusCode, $reasonPhrase, $reasonDetail);

        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->reasonDetail = $reasonDetail;
    }

    //////////////////////////////////////////////////////////////////

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    //////////////////////////////////////////////////////////////////

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    //////////////////////////////////////////////////////////////////

    public function getReasonDetail()
    {
        return $this->reasonDetail;
    }

    //////////////////////////////////////////////////////////////////

    private static function toString($statusCode, $reasonPhrase, $reasonDetail)
    {
        $string = (\mb_strlen($reasonDetail, 'utf-8') > 0 ? $reasonDetail : $reasonPhrase);
        $string .= ' (HTTP status code: ' . $statusCode . ')';

        return $string;
    }

    //////////////////////////////////////////////////////////////////
}
