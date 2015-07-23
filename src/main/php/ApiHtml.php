<?php
/**
 * This file is part of bit/helloworld.
 */
namespace stubbles\helloworld;
use stubbles\streams\OutputStream;
use stubbles\webapp\response\Error;
use stubbles\webapp\response\mimetypes\MimeType;
use stubbles\webapp\routing\api\Resources;
/**
 * Serializer for API information.
 */
class ApiHtml extends MimeType
{
    /**
     * returns default mime type name
     *
     * @return  string
     */
    protected function defaultName()
    {
        return 'text/html';
    }

    /**
     * serializes resource to output stream
     *
     * It returns the output stream that was passed.
     *
     * @param   mixed  $resource
     * @param   \stubbles\streams\OutputStream  $out
     * @return  \stubbles\streams\OutputStream
     */
    public function serialize($resource, OutputStream $out)
    {
        if ($resource instanceof Error) {
            $headline = $resource->type();
            $content  = '<p>' . $resource->message() . '</p>';
        } else {
            $headline = 'Available resources';
            $content  = $this->doSerialize($resource);
        }

        $out->write($this->createHtml($headline, $content));
        return $out;
    }

    /**
     * returns serialized resource
     *
     * @param   mixed  $resource
     * @return  string
     */
    protected function doSerialize($resource)
    {
        if (!($resource instanceof Resources)) {
            return '<p>Unknown resource</p>';
        }

        $content = '<h2>Available resources</h2>';
        foreach ($resource as $apiResource) {
            /* @var $apiResource \stubbles\webapp\routing\api\Resource */
            $content .= '<h3>' . $apiResource->name() . '</h3>';
            if ($apiResource->hasDescription()) {
                $content .= '<p>' . $apiResource->description() . '</p>';
            }

            $content .= '<ul>';
            foreach ($apiResource->links() as $link) {
                $content .= '<li>' . join(', ', $apiResource->requestMethods())
                         . ' <a href="' . $link . '">' . htmlentities($link)
                         . '</a></li>';
            }

            $content .= '</ul>';
            if ($apiResource->hasParameters()) {
                $content .= '<p>Parameters (bold ones are required):</p><ul>';
                foreach ($apiResource->parameters() as $parameter) {
                    $content .= '<li>';
                    if ($parameter->isRequired()) {
                        $content .= '<b>';
                    }

                    $content .= $parameter->name();
                    if ($parameter->isRequired()) {
                        $content .= '</b>';
                    }

                    $content .= ' (in ' . $parameter->place() . '): ' . $parameter->description() . '</li>';
                }

                $content .= '</ul>';
            }

            if ($apiResource->providesStatusCodes()) {
                $content .= '<p>Possible response status codes:</p><ul>';
                foreach ($apiResource->statusCodes() as $status) {
                    $content .= '<li>' . $status->code() . ': ' . $status->description() . '</li>';
                }

                $content .= '</ul>';
            }

            if ($apiResource->hasHeaders()) {
                $content .= '<p>Response headers:</p><ul>';
                foreach ($apiResource->headers() as $header) {
                    $content .= '<li>' . $header->name() . ': ' . $header->description() . '</li>';
                }

                $content .= '</ul>';
            }

            if ($apiResource->hasMimeTypes()) {
                $content .= '<p>Supported mime types:</p><ul>';
                foreach ($apiResource->mimeTypes() as $mimeType) {
                    $content .= '<li>' . htmlentities($mimeType) . '</li>';
                }

                $content .= '</ul>';
            }
        }

        $content .= <<<EOD
<h2>Common information for all resources</h2>

<h3>Errors</h3>
<p>With mime type <i>application/json</i> all error responses contain an error
message: <i>{"error":"Some error message"}</i></p>

<h3>Common response status codes</h3>
<p>All resources might return the following response status codes. Resources
might return additional response status codes, see specification of the resources.</p>
<ul>
<li>405 Method Not Allowed Requested resource with non-supported request method.</li>
<li>406 Not Acceptable Requested resource representation via Accept header which is not supported.</li>
</ul>

<h3>Request Id</h3>
<p>Each response from the server contains a response header X-Request-ID. This is
a unique identifier for the request and can be used to track problems. Requests
can supply their own request id via a X-Request-ID header. Its value must satisfy
the following regular expression: <i>^([a-zA-Z0-9+/=-]{20,200})$</i>. If such a
valid value is supplied the server will use this request id for the response
header.</p>

<h3>Requests with HEAD and OPTIONS</h3>
<p>All resources can also be requested with the <i>OPTIONS</i> request method.
Response body will be empty, but the response will contain the headers <i>Allow</i>
and <i>Access-Control-Allow-Methods</i> which both list all allowed request
methods for this resource as comma separated value.</p>

<p>All resources available via <i>GET</i> are also available via <i>HEAD</i>.</p>
EOD;
        return $content;
    }

    /**
     * returns complete html with given content
     *
     * @param   string  $content
     * @return  string
     */
    private function createHtml($headline, $content)
    {
        return <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>BK Feature Stats: $headline</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  </head>
  <body>
    <div id="header"><h1>Hello World API</h1></div>
    <div id="content">
      $content
    </div>
  </body>
</html>
EOD;
    }
}
