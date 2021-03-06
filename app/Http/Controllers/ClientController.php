<?php
namespace App\Http\Controllers;

use App\Client;
use App\Contact;
use App\Schemas\ClientSchema;
use App\Schemas\ContactSchema;
use App\Services\Client\ClientService;
use App\Util\Pagination;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends ContactableController
{

    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * @api {get} /clients Fetch clients list
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName GetClients
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function index(Request $request)
    {
        $pageSize = Pagination::getInstance($request)->getPageSize();
        return $this->withJsonApi($this->getEncoder()->encodeData(Client::paginate($pageSize)));
    }

    /**
     * @api {post} /clients Create client
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName CreateClient
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function store(Request $request)
    {
        $this->validateRequest($request, $this->getClientService()->validateOnCreate($request));
        $client = $this->getClientService()->create($request);
        return $this->withJsonApi($this->getEncoder()->encodeData($client), Response::HTTP_CREATED);
    }

    /**
     * @api {get} /accounts/:clientId Fetch client
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName GetClient
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function show($clientId)
    {
        return $this->withJsonApi($this->getEncoder()->encodeData(Client::find($clientId)));
    }

    /**
     * @api {put} /accounts/:clientId Update client
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName UpdateClient
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function update(Request $request, $clientId)
    {
        return $this->findClientAndExecuteCallback($clientId, function (Client $client) use ($request) {
            $legalDocumentCode = Client::LEGAL_DOCUMENT_CODE;
            $this->validateRequest($request, $this->getClientService()->validateOnUpdate($request), [
                $legalDocumentCode => "required|unique:clients,{$legalDocumentCode},{$client->id}"
            ]);
            $this->getClientService()->update($request, $client);
            return $this->withStatus(Response::HTTP_NO_CONTENT);
        });
    }

    /**
     * @api {delete} /accounts/:clientId Delete client
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName DeleteClient
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function destroy($clientId)
    {
        return $this->findClientAndExecuteCallback($clientId, function (Client $client) {
            $client->delete();
            return $this->withStatus(Response::HTTP_NO_CONTENT);
        });
    }

    /**
     * @api {post} /clients/:clientId/contacts Create client's contact
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName CreateClientContact
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function storeContact(Request $request, $clientId)
    {
        return $this->storeContactHandler($request, $this->getClientService(), function (callable $createContact) use ($clientId) {
            return $this->findClientAndExecuteCallback($clientId, function (Client $client) use ($createContact) {
                return $createContact($client);
            });
        });
    }

    /**
     * @api {put} /accounts/:clientId/contacts/:contactId Update client's contact
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName UpdateClientContact
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function updateContact(Request $request, $clientId, $contactId)
    {
        return $this->updateContactHandler($request, $this->getClientService(), $contactId, function (callable $updateContact) use ($clientId) {
            return $this->findClientAndExecuteCallback($clientId, function () use ($updateContact) {
                return $updateContact();
            });
        });
    }

    /**
     * @api {delete} /accounts/:clientId/contacts/:contactId Delete client's contact
     * @apiVersion 1.0.0
     * @apiGroup Client
     * @apiName DeleteClientContact
     * @apiHeader {String} Authorization User generated JWT token
     */
    public function destroyContact(Request $request, $clientId, $contactId)
    {
        return $this->destroyContactHandler($request, $contactId, function (callable $destroyContact) use ($clientId) {
            return $this->findClientAndExecuteCallback($clientId, function () use ($destroyContact) {
                return $destroyContact();
            });
        });
    }

    private function findClientAndExecuteCallback($clientId, callable $callback)
    {
        $client = Client::find($clientId);
        if (is_null($client)) {
            return $this->withStatus(Response::HTTP_NOT_FOUND);
        }
        return $callback($client);
    }

    private function getEncoder()
    {
        return $this->createEncoder([
            Client::class => ClientSchema::class,
            Contact::class => ContactSchema::class,
        ]);
    }

    private function getClientService()
    {
        return $this->clientService;
    }

}
