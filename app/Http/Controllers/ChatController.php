<?php

namespace App\Http\Controllers;

require_once '../vendor/autoload.php';
use OpenAI;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class ChatController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index()
    {
        // Fetch existing context from session or initialize with a greeting
        $context = session()->get('chat_context', [['role' => 'user', 'content' => 'Hello!']]);
        
        return view('chat', compact('context'));
    }
    
    public function processChat(Request $request)
    {
        $userMessage = $request->input('user_message');
        
        // Fetch existing context from session or initialize with a greeting
        $context = session()->get('chat_context', [['role' => 'user', 'content' => 'Hello!']]);
        
        // Append user message to context
        $context[] = ['role' => 'user', 'content' => $userMessage];
        
        $yourApiKey = //your api key;
        $client = OpenAI::client($yourApiKey);
        
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $context
        ]);
        
        foreach ($response->choices as $result) {
            $context[] = ['role' => 'assistant', 'content' => $result->message->content];
        }
        
        // Store the updated context in the session
        session(['chat_context' => $context]);
        
        return redirect()->route('chat.index');
    }
    
    public function editContext()
    {
        $context = session()->get('chat_context',  [['role' => 'user', 'content' => 'Hello!']]);
        $initialContext = $context[0]['content'];
        
        return view('edit', compact('initialContext'));
    }
    
    public function updateContext(Request $request)
    {
        //dd($request->all());
        $context = [['role' => 'user', 'content' => $request->context]];
        
        session(['chat_context' => $context]);
        
        return redirect('edit');
    }
    
    /**
    * Show the form for creating a new resource.
    */
    public function create()
    {
        //
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        //
    }
    
    /**
    * Display the specified resource.
    */
    public function show(string $id)
    {
        //
    }
    
    /**
    * Show the form for editing the specified resource.
    */
    public function edit(string $id)
    {
        //
    }
    
    /**
    * Update the specified resource in storage.
    */
    public function update(Request $request, string $id)
    {
        //
    }
    
    /**
    * Remove the specified resource from storage.
    */
    public function destroy(string $id)
    {
        //
    }
    
    public function twillioWebhook(Request $request)
    {
        \Log::info('Webhook Request Received', ['request' => $request->all()]);
        //$data = json_decode($request->all(), true);

        $userMessage = $request->input('Body');
        
        // Fetch existing context from session or initialize with a greeting
        $context = session()->get('chat_context', [['role' => 'user', 'content' => 'Hello!']]);
        
        // Append user message to context
        $context[] = ['role' => 'user', 'content' => $userMessage];
        
        $yourApiKey = //yourapikey;
        $client = OpenAI::client($yourApiKey);
        
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $context
        ]);
        
        foreach ($response->choices as $result) {
            $context[] = ['role' => 'assistant', 'content' => $result->message->content];
        }
        
        // Store the updated context in the session
        session(['chat_context' => $context]);

        $sid    = env('TWILIO_SID');
        $token  = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);
        $from = $request->From;

        $message = $twilio->messages
        ->create("whatsapp:+6281331013603", // to
        array(
            "from" => "whatsapp:+14155238886",
            "body" => $result->message->content
            )
        );
        
        
    }
}
