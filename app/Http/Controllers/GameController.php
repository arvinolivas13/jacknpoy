<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index() {
        return view('welcome');
    }

    public function get(Request $request) {

        $rule = [
            [
                'move_id' => 0,
                'lose_to' => [1]
            ],
            [
                'move_id' => 1,
                'lose_to' => [2]
            ],
            [
                'move_id' => 2,
                'lose_to' => [0]
            ]
        ];

        $your_move = $request->id;
        $computer_move = rand(0, count($request->move)-1);

        $outcome = null;

        if($your_move !== $computer_move) {
            foreach ($rule[$your_move]['lose_to'] as $key => $item) {
                if($item === $computer_move) {
                    $outcome = 0;
                }
                else {
                    $outcome = 1;
                }
            }
        }
        else {
            $outcome = 2;
        }
        
        return response()->json(compact('rule', 'computer_move', 'your_move', 'outcome'));
    }
}
