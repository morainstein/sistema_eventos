<?php

namespace App\Http\Controllers;

use App\Events\PaggueCredentialsCreatedEvent;
use App\Http\Requests\CredentialsFormRequest;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use App\Services\PagguePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class PaggueCredentialsController extends Controller
{
    public function store(CredentialsFormRequest $request)
    /*
     * - Cria as credêciais
     * - Não permite mais de uma credencial armazenada (atualiza a antiga)
     * - Dispara evento de criação de credencial
     *   - Listeners: Se inscreve no webhook da Paggue, de acordo com as credênciais
     *   - Listeners: Apaga as credenciais e o webhook depois de um mês
     */
    {
        $credentials = PaggueCredentials::where('promoter_id',Auth::user()->id)
            ->first() ?? new PaggueCredentials();
        $credentials->fill($request->all());
        $credentials->promoter_id = Auth::user()->id;
        $credentials->save();
        
        PaggueCredentialsCreatedEvent::dispatch($credentials);

        $message = 'Credentials has been stored. For security reasons, credentials will be destroyed within a month. In case you had old credentials, it has been replaced by these new ones you sent now';

        return response()->json(['message' => $message],201);
    }

    public function destroy()
    /**
     * - Deleta as credenciais
     * - Deleta a inscrição do webhook desse promotor na Paggue
     */
    {
        $credentials = Auth::user()->credentials;
        PagguePaymentService::credentials($credentials)->deletePixWebhook();
        $credentials->delete();

        return response()->json(['message' => 'Your credentials has been destroyed'],200);
    }
}
