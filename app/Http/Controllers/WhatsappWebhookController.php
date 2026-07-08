<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
   
    public function verifier(Request $request) 
    {
        $verifyToken = env('FUAC_WHATSAPP_VERIFY_TOKEN');
        
        if ($request->query('hub_verify_token') === $verifyToken) {
            return response($request->query('hub_challenge'), 200);
        }
        
        return response('Token invalide', 403);
    }
}