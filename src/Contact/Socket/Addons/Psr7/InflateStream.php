<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespaces
|---------------------------------------------------
*/
namespace Kiaan\Contact\Socket\Addons\Psr7;

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Contact\Socket\Addons\Psr\StreamInterface;

/*
|---------------------------------------------------
| Class
|---------------------------------------------------
*/
class InflateStream implements StreamInterface
{
    use StreamDecoratorTrait;

    public function __construct(StreamInterface $stream)
    {
        // read the first 10 bytes, ie. gzip header
        $header = $stream->read(10);
        $filenameHeaderLength = $this->getLengthOfPossibleFilenameHeader($stream, $header);
        // Skip the header, that is 10 + length of filename + 1 (nil) bytes
        $stream = new LimitStream($stream, -1, 10 + $filenameHeaderLength);
        $resource = StreamWrapper::getResource($stream);
        stream_filter_append($resource, 'zlib.inflate', STREAM_FILTER_READ);
        $this->stream = $stream->isSeekable() ? new Stream($resource) : new NoSeekStream(new Stream($resource));
    }

    /**
     * @param StreamInterface $stream
     * @param $header
     * @return int
     */
    private function getLengthOfPossibleFilenameHeader(StreamInterface $stream, $header)
    {
        $filename_header_length = 0;

        if (substr(bin2hex($header), 6, 2) === '08') {
            // we have a filename, read until nil
            $filename_header_length = 1;
            while ($stream->read(1) !== chr(0)) {
                $filename_header_length++;
            }
        }

        return $filename_header_length;
    }
}
