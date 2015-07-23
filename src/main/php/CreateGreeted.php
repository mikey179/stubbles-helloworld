<?php
/**
 * This file is part of bit/helloworld.
 */
namespace stubbles\helloworld;
use stubbles\db\Database;
use stubbles\input\errors\messages\ParamErrorMessages;
use stubbles\input\filter\range\StringLength;
use stubbles\webapp\Request;
use stubbles\webapp\Response;
use stubbles\webapp\Target;
use stubbles\webapp\UriPath;
use stubbles\webapp\response\Error;
/**
 * Creates a greating.
 *
 * @Name('Create a greeting.')
 * @Description('Allows to create a greeting for a person. Data should be submitted using <i>application/x-www-form-urlencoded</i>.')
 * @Status(code=201, description='Greeting created successfully.')
 * @Status(code=500, description='Error while storing the greeting. Please try again later.')
 * @Header(name='Location', description='URL under which the new greeting can be accessed.')
 * @Parameter(name='path', in='body', description='Path under which creating should be accessible.', required=true)
 * @Parameter(name='greeted', in='body', description='Name of person or thing to greet.', required=true)
 * @SupportsMimeType(mimeType='application/json')
 */
class CreateGreeted implements Target
{
    /**
     * @type  \stubbles\db\Database
     */
    private $database;
    /**
     * @type  \stubbles\input\errors\messages\ParamErrorMessages
     */
    private $errorMessages;

    /**
     * constructor
     *
     * @param  \stubbles\db\Database  $database
     * @Named{database}('greetings')
     */
    public function __construct(
            Database $database,
            ParamErrorMessages $errorMessages)
    {
        $this->database      = $database;
        $this->errorMessages = $errorMessages;
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
        $path    = $request->readParam('path')->required()->asString(new StringLength(1, 20));
        $greeted = $request->readParam('greeted')->required()->asString(new StringLength(1, 20));
        if ($request->paramErrors()->exist()) {
            $response->status()->badRequest();
            return Error::inParams($request->paramErrors(), $this->errorMessages);
        }

        $this->database->query(
                'INSERT INTO greetings VALUES (:path, :greeted)',
                [':path' => $path, ':greeted' => $greeted]
        );
        $response->status()->created($request->uri()->withPath('/' . $path));
        return 'Hello ' . $greeted;
    }
}

