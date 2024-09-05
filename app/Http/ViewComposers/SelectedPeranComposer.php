<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class SelectedPeranComposer
{
    public function compose(View $view)
    {
        $selectedPeran = Session::get('selected_peran');
        $view->with('selectedPeran', $selectedPeran);
    }
}