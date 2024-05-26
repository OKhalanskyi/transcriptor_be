<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

class AudioController extends Controller
{
    function transcribe(Request $request)
    {
        if (!$request->hasFile('audio')) {
            return response([
                'message' => 'No audio file provided!'
            ], 400);
        }
        $audio = $request->file('audio');
        $audioPath = $audio->store('audios', 'local');

        $result = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen(Storage::path($audioPath), 'r'),
            'response_format' => 'verbose_json',
            'timestamp_granularities' => ['segment', 'word'],
            'redaction' => [
                'strategy' => 'redact',
            ]
        ]);

        $result2 = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Change all sensitive information to X in next text:' . $result->text],
            ],
        ]);

        return $result2->choices[0]->message->content;
    }
}
