<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/9/15
 * Time: 12:39 AM
 */

namespace App\Http\Controllers;

class CardJsonController extends Controller
{
    public function index() {
        return view('card_json');
    }
}