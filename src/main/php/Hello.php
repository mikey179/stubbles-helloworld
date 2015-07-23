<?php
/**
 * This file is part of bit/helloworld.
 */
namespace stubbles\helloworld;
use stubbles\db\Database;
use stubbles\webapp\Request;
use stubbles\webapp\Response;
use stubbles\webapp\Target;
use stubbles\webapp\UriPath;
/**
 * Hello world example resource.
 *
 * @Name('Hello world')
 * @Description('Example for a hello world resource.')
 * @Status(code=200, description='Default.')
 * @Status(code=404, description='No person to greet found.')
 * @Status(code=500, description='Error while retrieving the greeted person. Please try again later.')
 * @SupportsMimeType(mimeType='application/json')
 */
class Hello implements Target
{
    /**
     * @type  \stubbles\db\Database
     */
    private $database;

    /**
     * constructor
     *
     * @param  \stubbles\db\Database  $database
     * @Named('greetings')
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * resolves the request and returns resource data
     *
     * @param   \stubbles\webapp\Request   $request   current request
     * @param   \stubbles\webapp\Response  $response  response to send
     * @param   \stubbles\webapp\UriPath   $uriPath   information about called uri path
     * @return  mixed
     */
    public function resolve(Request $request, Response $response, UriPath $uriPath)
    {
        $greeted = $this->greeted($uriPath);
        if (empty($greeted)) {
            return $response->notFound();
        }

        return 'Hello ' . $greeted;
    }

    /**
     * returns the greeted name
     *
     * @param   \stubbles\webapp\UriPath  $uriPath
     * @return  string
     */
    private function greeted(UriPath $uriPath)
    {
        if ($uriPath->hasArgument('greeted')) {
            return $this->database->fetchOne(
                    'SELECT greeted FROM greetings WHERE path = :path',
                    [':path' => $uriPath->readArgument('greeted')->asString()]
            );
        }

        return 'World';
    }
}

