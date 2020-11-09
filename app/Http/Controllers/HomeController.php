<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    private $accessToken;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $messages = request()->session()->get('messages');
        return view('welcome', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $client = new Client();

        $this->accessToken = $request->session()->get('accessToken');
        if (!$this->accessToken) {
            $headers = [
                'x-inbenta-key' => config('app.api_key'),
                'Content-Type' => 'application/json'
            ];
            $body = [
                'secret' => config('app.secret')
            ];
            //AUTH
            $res = $client->request('POST', 'https://api.inbenta.io/v1/auth', ['headers' => $headers, 'body' => \GuzzleHttp\json_encode($body)]);
            $res = \GuzzleHttp\json_decode($res->getBody());
            $request->session()->put('accessToken', $res->accessToken);
            $request->session()->put('expirationToken', $res->expiration);

            $headers = [
                'x-inbenta-key' => config('app.api_key'),
                'Authorization' => 'Bearer ' . $res->accessToken
            ];
            //TAKE API ROUTE
            $res = $client->request('GET', 'https://api.inbenta.io/v1/apis', ['headers' => $headers]);
            $res = \GuzzleHttp\json_decode($res->getBody());
            $request->session()->put('chatbotApiUrl', $res->apis->chatbot . "/v1");
            $this->newConversation();


        }

        try {
            $message = $this->send();
        } catch (\Exception $exception) {
            $this->newConversation();
            $message = $this->send();

        }

        $this->storeMessages($request, "Me:", $request->input('mensagem'));

        $this->storeMessages($request, "YodaBot:", $message);

        return $message;
//        no-results

//        return $request->message;
    }

    protected function storeMessages($request, $from, $message)
    {
        $messages = $request->session()->get('messages');
        if (is_array($messages)) {
            array_push($messages, array("from" => $from, "message" => $message));
        } else {
            $messages = [];
            array_push($messages, array("from" => $from, "message" => $message));
        }
        $request->session()->put("messages", $messages);
    }

    protected function checkFail($request, $res)
    {
        $result = $request->session()->get("no-results");

        $flags = $res->answers[0]->flags;
        if (count($flags) >= 1) {
            foreach ($flags as $flag) {
                if ($flag == "no-results") {
                    $result = $result + 1;
                }
            }
        } else {
            $result = 0;
        }
        $request->session()->put("no-results", $result);
        if ($result >= 2)
            return true;


        return false;

    }

    protected function newConversation()
    {

        $request = request();
        $this->accessToken = $request->session()->get('accessToken');

        $headers = [
            'x-inbenta-key' => config('app.api_key'),
            'Authorization' => 'Bearer ' . $this->accessToken
        ];
        $client = new Client();

        try {
            //OPEN NEW CONVERSATION
            $res = $client->request('POST', $request->session()->get("chatbotApiUrl") . "/conversation", ['headers' => $headers]);
            $res = \GuzzleHttp\json_decode($res->getBody());
            $request->session()->put('sessionToken', $res->sessionToken);
        } catch (\Exception $e) {
            //Conexão expirou zerar token
            $request->session()->forget('accessToken');
            $this->sendMessage($request);
        }


    }

    protected function getFilms()
    {
        $query_films = '{
                              allFilms {
                                films {
                                  title,
                                }
                              }
                            }';
        $client = new Client();


        $response = $client->request('post', 'https://inbenta-graphql-swapi-prod.herokuapp.com/api', [
            'json' => [
                "query" => $query_films
            ]
        ]);
        $response = \GuzzleHttp\json_decode($response->getBody());

        $films = "";

        foreach ($response->data->allFilms as $allFilms) {
            foreach ($allFilms as $film) {
                $films = $films . $film->title . ", ";
            }
        }

        return "The force is in this movies: " . $films;


    }

    protected function send()
    {
        $request = request();
        $this->accessToken = $request->session()->get('accessToken');
        $client = new Client();

        $headers = [
            'x-inbenta-key' => config('app.api_key'),
            'Authorization' => 'Bearer ' . $this->accessToken,
            'x-inbenta-session' => 'Bearer ' . $request->session()->get('sessionToken')
        ];

        $body = [
            'message' => $request->input('mensagem')
        ];

        //SEND MESSAGE
        $res = $client->request('POST', $request->session()->get("chatbotApiUrl") . "/conversation/message", [
            'headers' => $headers,
            'body' => \GuzzleHttp\json_encode($body)
        ]);
        $res = \GuzzleHttp\json_decode($res->getBody());

        $answer = $res->answers[0]->message;

        if (strpos(strtolower($request->input('mensagem')), strtolower("force")) !== false) {
            return $this->getFilms();

        }


        if ($this->checkFail($request, $res)) {
            return $this->getPeople();
        }


        return $answer;
    }

    protected function getPeople()
    {
        $query_people = '{
                          allPeople(first:10) {
                            people {
                                name
                            }
                          }
                        }';
        $client = new Client();


        $response = $client->request('post', 'https://inbenta-graphql-swapi-prod.herokuapp.com/api', [
            'json' => [
                "query" => $query_people
            ]
        ]);
        $response = \GuzzleHttp\json_decode($response->getBody());

        $people = "";

        foreach ($response->data->allPeople as $allPeople) {
            foreach ($allPeople as $person) {
                $people = $people . $person->name . ", ";
            }
        }
        request()->session()->put("no-results", 0);

        return "I haven't found any results, but here is a list of some Star Wars characters: " . $people;


    }
}
