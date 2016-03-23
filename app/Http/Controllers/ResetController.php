<?php namespace Blog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Artisan;

class ResetController extends Controller
{
    public function reset(JsonResponse $res) {
        Artisan::call('db:seed', [
            '--class' => 'APIResetSeeder',
            '--force' => true,
        ]);

        return new JsonResponse([ 'complete' => true ]);
    }
}
