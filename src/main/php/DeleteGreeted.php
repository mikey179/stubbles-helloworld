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
 * Deletes a greeting.
 *
 * @Name('Delete a greeting')
 * @Description('Deletes a greeting.')
 * @Status(code=200, description='Default.')
 * @Status(code=404, description='Nothing found that could be deleted.')
 * @Status(code=500, description='Error while retrieving the greeted person. Please try again later.')
 * @Parameter(name='greeted', in='path', description='The id of the greeting to delete.', required=true)
 * @SupportsMimeType(mimeType='application/json')
 */
class DeleteGreeted implements Target
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
        $path = $uriPath->readArgument('greeted')->asString();
        if (empty($path)) {
            return $response->notFound();
        }

        $deleted = $this->database->query(
                'DELETE FROM greetings WHERE path = :path',
                [':path' => $path]
        );
        if (0 == $deleted) {
            return $response->notFound();
        }

        $response->status()->noContent();
    }
}

